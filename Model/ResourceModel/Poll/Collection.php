<?php
/**
 * @author Jason Sylvester Devester
 * @copyright Copyright © 2019 Devester. All rights reserved.
 * @package Devester/Poll
 */

namespace Devester\Poll\Model\ResourceModel\Poll;

use \Devester\Poll\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection
{
	protected $_idFieldName = 'poll_id';
	protected $_eventPrefix = 'devester_poll_poll_collection';
	protected $_eventObject = 'poll_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Devester\Poll\Model\Poll',
					'Devester\Poll\Model\ResourceModel\Poll');
        $this->_map['fields']['poll_id'] = 'main_table.poll_id';
        $this->_map['fields']['store'] = 'store_table.store_id';
	}
	
    /**
     * Set first store flag.
     *
     * @param bool $flag Flag.
     *
     * @return $this
     */
    public function setFirstStoreFlag($flag = false)
    {
        $this->_previewFlag = $flag;
        return $this;
    }

    /**
     * Add filter by store.
     *
     * @param int|array|\Magento\Store\Model\Store $store     Store.
     * @param bool                                 $withAdmin WithAdmin.
     *
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            $this->performAddStoreFilter($store, $withAdmin);
        }
        return $this;
    }

    /**
     * Perform operations before rendering filters.
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $this->joinStoreRelationTable(
            'devester_poll_store',
            'poll_id'
        );
    }
}
