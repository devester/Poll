<?php
/**
 * @author Jason Sylvester Devester
 * @copyright Copyright © 2019 Devester. All rights reserved.
 * @package Devester/Poll
 */

namespace Devester\Poll\Model;

class Poll extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{

    const POLL_ID = 'poll_id';
    const TITLE = 'title';

    /**
     * Polls Statuses.
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * Polls cache tag.
     */
	const CACHE_TAG = 'devester_poll_poll';


    /**
     * Variable.
     *
     * @var string
     */
	protected $_cacheTag = 'devester_poll_poll';


    /**
     * Prefix of model events names.
     *
     * @var string
     */
	protected $_eventPrefix = 'devester_poll_poll';

	protected function _construct()
	{
		$this->_init('Devester\Poll\Model\ResourceModel\Poll');
	}

    /**
     * Prepare groups statuses.
     *
     * @return options[]
     */
    public function getAvailableStatuses()
    {
        return [
            self::STATUS_ENABLED => __('Enabled'),
            self::STATUS_DISABLED => __('Disabled')
        ];
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
        return $this->getData(self::POLL_ID);
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
     * Set ID.
     *
     * @param int $id Group ID.
     *
     * @return \SolideWebservices\Flexslider\Api\Data\GroupInterface
     */
    public function setId($id)
    {
        return $this->setData(self::POLL_ID, $id);
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

}