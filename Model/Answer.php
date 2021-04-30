<?php
/**
 * @author Jason Sylvester Devester
 * @copyright Copyright © 2019 Devester. All rights reserved.
 * @package Devester/Poll
 */

namespace Devester\Poll\Model;

class Answer extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{

	const CACHE_TAG = 'devester_poll_answer';

	const ANSWER_ID = 'answer_id';
    const TITLE = 'title';
    const VOTES = 'votes_count';
    const ORDER = 'answer_order';


    /**
     * Variable.
     *
     * @var string
     */
	protected $_cacheTag = 'devester_poll_answer';


    /**
     * Prefix of model events names.
     *
     * @var string
     */
	protected $_eventPrefix = 'devester_poll_answer';

	protected function _construct()
	{
		$this->_init('Devester\Poll\Model\ResourceModel\Answer');
	}

	public function getIdentities()
	{
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	public function getDefaultValues()
	{
		$values = [];

		return $values;
	}

    /**
     * Get ID.
     *
     * @return int
     */
    public function getId()
    {
        return $this->getData(self::ANSWER_ID);
    }

    /**
     * Get Title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * Get VOTES.
     *
     * @return int
     */
    public function getVotesCount()
    {
        return $this->getData(self::VOTES);
    }

    /**
     * Get answer_order.
     *
     * @return int
     */
    public function getAnswerOrder()
    {
        return $this->getData(self::ORDER);
    }

    /**
     * Set ID.
     *
     * @param int $id Group ID.
     *
     * @return \SolideWebservices\Flexslider\Api\Data\GroupInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ANSWER_ID, $id);
    }

    /**
     * Set title.
     *
     * @param string $title Title.
     *
     * @return \SolideWebservices\Flexslider\Api\Data\GroupInterface
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }


    /**
     * set VOTES.
     *
     * @return int
     */
    public function setVotesCount($votes)
    {
        return $this->setData(self::VOTES, $votes);
    }

    /**
     * set answer_order.
     *
     * @return int
     */
    public function setAnswerOrder($order)
    {
        return $this->setData(self::ORDER, $order);
    }
}