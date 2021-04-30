<?php
/**
 * @author Jason Sylvester Devester
 * @copyright Copyright © 2019 Devester. All rights reserved.
 * @package Devester/Poll
 */

namespace Devester\Poll\Model\ResourceModel;

class Poll extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	
	public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context
	)
	{
		parent::__construct($context);
	}
	
	protected function _construct()
	{
		$this->_init('devester_poll_poll', 'poll_id');
	}
	
    /**
     * Perform after save actions.
     *
     * @param \Magento\Framework\Model\AbstractModel $object AbstractModel.
     *
     * @return void
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->afterSaveStores($object);
        $this->afterSaveAnswers($object);
    }

    /**
     * Assign group to store views.
     *
     * @param \Magento\Framework\Model\AbstractModel $object AbstractModel.
     *
     * @return $this
     */
    private function afterSaveStores(\Magento\Framework\Model\AbstractModel $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();
        if (empty($newStores)) {
            $newStores = (array)$object->getStoreId();
        }
        $table = $this->getTable('devester_poll_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = [
                'poll_id = ?' => (int)$object->getId(),
                'store_id IN (?)' => $delete
            ];
            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = [
                    'poll_id' => (int)$object->getId(),
                    'store_id' => (int)$storeId
                ];
            }

            $this->getConnection()->insertMultiple($table, $data);
        }

        return parent::_afterSave($object);
    }

    /**
     * Add awnsers to poll
     *
     * @param \Magento\Framework\Model\AbstractModel $object AbstractModel.
     *
     * @return $this
     */
    private function afterSaveAnswers(\Magento\Framework\Model\AbstractModel $object)
    {
    	/*
        $oldCategories = $this->lookupCategoryIds($object->getId());
        $newCategories = (array)$object->getCategories();
        if (empty($newCategories)) {
            $newCategories = (array)$object->getCategoriesIds();
        }
        $table = $this->getTable('devester_poll_answer');
        $insert = array_diff($newCategories, $oldCategories);
        $delete = array_diff($oldCategories, $newCategories);

        if ($delete) {
            $where = [
                'group_id = ?' => (int)$object->getId(),
                'category_id IN (?)' => $delete
            ];
            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];
            foreach ($insert as $categoryId) {
                $data[] = [
                    'group_id' => (int)$object->getId(),
                    'category_id' => (int)$categoryId
                ];
            }

            $this->getConnection()->insertMultiple($table, $data);
        }

        return parent::_afterSave($object);
        */
    }


    /**
     * Perform operations after object load.
     *
     * @param \Magento\Framework\Model\AbstractModel $object AbstractModel.
     *
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $answers = $this->lookupAnswerIds($object->getId());

            $object->setData('answer_id', $stores);
            $object->setData('page_id', $answers);
        }

        return parent::_afterLoad($object);
    }

    /**
     * Retrieve select object for load object data.
     *
     * @param string                   $field  Field.
     * @param mixed                    $value  Value.
     * @param \Magento\Cms\Model\Group $object Group.
     *
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $storeIds = [
                \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                (int)$object->getStoreId()
            ];
            $select->join(
                ['devester_poll_store' => $this->getTable('devester_poll_store')],
                $this->getMainTable() . '.poll_id = devester_poll_store.poll_id',
                []
            )->where(
                'status = ?',
                1
            )->where(
                'devester_poll_store.store_id IN (?)',
                $storeIds
            )->order(
                'devester_poll_store.store_id DESC'
            )->limit(
                1
            );
        }
        if ($data = $object->getAnswerId()) {
            $select->join(
                ['devester_poll_answer' => $this->getTable('devester_poll_answer')],
                $this->getMainTable() . '.poll_id = devester_poll_answer.poll_id',
                []
                )->where(
                    'devester_poll_answer.answer_id in (?) ',
                    $data
                );
        }

        return $select;
    }


    /**
     * Retrieve load select with filter by identifier, store and activity.
     *
     * @param string    $identifier Identifier.
     * @param int|array $store      Store.
     * @param int       $isActive   IsActive.
     *
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadByIdentifierSelect(
        $identifier,
        $store,
        $isActive = null
    ) {
        $select = $this->getConnection()->select()->from(
            ['devester_poll_poll' => $this->getMainTable()]
        )->join(
            ['devester_poll_store' => $this->getTable('devester_poll_store')],
            'devester_poll_poll.poll_id = devester_poll_store.poll_id',
            []
        )->where(
            'devester_poll_poll.title = ?',
            $identifier
        )->where(
            'devester_poll_store.store_id IN (?)',
            $store
        );

        if ($isActive) {
            $select->where(
                'devester_poll_poll.status = ?',
                $isActive
            );
        }

        return $select;
    }

    /**
     * Check if group identifier exist for specific store.
     *
     * @param string $identifier Identifier.
     * @param int    $storeId    StoreID.
     *
     * @return int
     */
    public function checkIdentifier($identifier, $storeId)
    {
        $stores = [\Magento\Store\Model\Store::DEFAULT_STORE_ID, $storeId];
        $select = $this->_getLoadByIdentifierSelect($identifier, $stores, 1);
        $select->reset(\Magento\Framework\DB\Select::COLUMNS)
            ->columns('devester_poll_poll.poll_id')
            ->order('devester_poll_store.store_id DESC')
            ->limit(1);

        return $this->getConnection()->fetchOne($select);
    }

    /**
     * Retrieves group title from DB by passed id.
     *
     * @param string $id Group ID.
     *
     * @return string|false
     */
    public function getPollTitleById($id)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from($this->getMainTable(), 'title')
            ->where('poll_id = :poll_id');
        $binds = ['poll_id' => (int)$id];
        return $connection->fetchOne($select, $binds);
    }

    /**
     * Get store ids to which specified item is assigned.
     *
     * @param int $pollId Group ID.
     *
     * @return $select[]
     */
    public function lookupStoreIds($pollId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('devester_poll_store'),
            'store_id'
        )->where(
            'poll_id = ?',
            (int)$pollId
        );
        return $connection->fetchCol($select);
    }

    /**
     * Get page ids to which specified item is assigned.
     *
     * @param int $pollId Group ID.
     *
     * @return select[]
     */
    public function lookupAnswerIds($pollId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('devester_poll_answer'),
            'answer_id'
        )->where(
            'poll_id = ?',
            (int)$pollId
        );
        return $connection->fetchCol($select);
    }

    /**
     * Set store model.
     *
     * @param \Magento\Store\Model\Store $store Store.
     *
     * @return $this
     */
    public function setStore($store)
    {
        $this->_store = $store;
        return $this;
    }

    /**
     * Retrieve store model.
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        return $this->_storeManager->getStore($this->_store);
    }
}