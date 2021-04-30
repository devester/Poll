<?php
/**
 * @author Jason Sylvester Devester
 * @copyright Copyright © 2019 Devester. All rights reserved.
 * @package Devester/Poll
 */

namespace Devester\Poll\Model\ResourceModel\Answer;

use \Devester\Poll\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Variable.
     *
     * @var string
     */
	protected $_idFieldName = 'answer_id';

	protected $_eventPrefix = 'devester_poll_answer_collection';
	protected $_eventObject = 'answer_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Devester\Poll\Model\Answer',
					'Devester\Poll\Model\ResourceModel\Answer');
		$this->_map['fields']['answer_id'] = 'main_table.answer_id';
	}

    /**
     * Add filter by store.
     *
     * @param int|array|\Magento\Store\Model\Store $store     Store.
     * @param bool                                 $withAdmin WithAdmin.
     *
     * @return void
     */
    public function addStoreFilter($store, $withAdmin = true)
    {

    }

    /**
     * Add filter by Poll.
     *
     * @param int $pollId PollID.
     *
     * @return $this
     */
    public function addPollFilter($pollId)
    {
        $this->performAddPollFilter($pollId);
        return $this;
    }
}
