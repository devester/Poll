<?php
/**
 * @author Jason Sylvester Devester
 * @copyright Copyright © 2019 Devester. All rights reserved.
 * @package Devester/Poll
 */

namespace Devester\Poll\Model\ResourceModel;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Event\ManagerInterface;
use Psr\Log\LoggerInterface;



/**
 * Polls content block
 */
abstract class AbstractCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Store manager.
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Date.
     *
     * @var DateTime
     */
    protected $_date;

    /**
     * Construct.
     *
     * @param EntityFactoryInterface $entityFactory          EntityFactory.
     * @param LoggerInterface        $logger                 Logger.
     * @param FetchStrategyInterface $fetchStrategy          FetchStrategy.
     * @param ManagerInterface       $eventManager           EventManager.
     * @param StoreManagerInterface  $storeManager           Store Manager.
     * @param DateTime               $date                   Date.
     * @param AdapterInterface|null  $connection             Connection.
     * @param AbstractDb|null        $resource               Resource.
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        DateTime $date,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
        $this->storeManager = $storeManager;
        $this->_date = $date;
    }


    /**
     * Add field filter to collection.
     *
     * @param array|string          $field     Field.
     * @param string|int|array|null $condition Condition.
     *
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'store_id') {
            return $this->addStoreFilter($condition, false);
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add filter by store.
     *
     * @param int|array|\Magento\Store\Model\Store $store     Store.
     * @param bool                                 $withAdmin WithAdmin.
     *
     * @return $this
     */
    abstract public function addStoreFilter($store, $withAdmin = true);

    /**
     * Perform adding filter by store.
     *
     * @param int|array|\Magento\Store\Model\Store $store     Store.
     * @param bool                                 $withAdmin WithAdmin.
     *
     * @return void
     */
    protected function performAddStoreFilter($store, $withAdmin = true)
    {
        if ($store instanceof \Magento\Store\Model\Store) {
            $store = [$store->getId()];
        }

        if (!is_array($store)) {
            $store = [$store];
        }

        if ($withAdmin) {
            $store[] = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
        }

        $this->addFilter('store', ['in' => $store], 'public');
    }

    /**
     * Perform adding filter by Poll.
     *
     * @param int $pollpId Poll ID.
     *
     * @return void
     */
    protected function performAddPollFilter($pollpId)
    {
        $this->addFilter('poll_id', ['in' => $pollpId], 'public');
    }

    /**
     * Perform adding filter by status.
     *
     * @param bool $status Status.
     *
     * @return $this
     */
    public function addEnabledFilter($status = 1)
    {
        $this->getSelect()->where('main_table.status = ?', $status);
        return $this;
    }

    /**
     * Perform adding filter by date for Pols.
     *
     * @return $this
     */
    public function addPollDateFilter()
    {
        return $this->addFieldToFilter(
            'date_posted',
            [
                ['to' => $this->_date->gmtDate('Y-m-d H:i:s')],
                ['date_posted', 'null'=>'']

            ]
        )->addFieldToFilter(
            'date_closed',
            [
                ['gteq' => $this->_date->gmtDate('Y-m-d H:i:s')],
                ['date_closed', 'null'=>'']
            ]
        );
    }

    /**
     * Perform  order for Polls.
     *
     * @param string $answerOrder Answer Order.
     *
     * @return $this
     */
    public function addAnswerSortFilter($answerOrder = 'ASC')
    {
        $this->getSelect()
            ->order('main_table.answer_order ' . $answerOrder);
        return $this;
    }

    /**
     * Perform random sort order for Answers.
     *
     * @param string $dir Direction.
     *
     * @return $this
     */
    public function addOrderByRandom($dir = 'ASC')
    {
        $this->getSelect()->order('RAND() ' . $dir);
        return $this;
    }

    /**
     * Perform filter by Poll ID.
     *
     * @param string $pollId Poll ID.
     *
     * @return $this
     */
    public function addPollIdFilter($pollId)
    {
        $this->getSelect()->where('main_table.poll_id = ?', $pollId);
        return $this;
    }
    
    /**
     * Join store relation table if there is store filter.
     *
     * @param string $tableName  TableName.
     * @param string $columnName ColumnName.
     *
     * @return void
     */
    protected function joinStoreRelationTable($tableName, $columnName)
    {
        if ($this->getFilter('store')) {
            $this->getSelect()->join(
                ['store_table' => $this->getTable($tableName)],
                'main_table.' . $columnName . ' = store_table.' . $columnName,
                []
            )->group(
                'main_table.' . $columnName
            );
        }
        parent::_renderFiltersBefore();
    }
}