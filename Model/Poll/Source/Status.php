<?php
/**
 * @author Jason Sylvester Devester
 * @copyright Copyright © 2019 Devester. All rights reserved.
 * @package Devester/Poll
 */

namespace Devester\Poll\Model\Poll\Source;

use Devester\Poll\Model\Poll;

/**
 * Class IsActive
 */
class Status implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Variable.
     *
     * @var \Devester\Poll\Model\Poll
     */
    protected $poll;

    /**
     * Construct.
     *
     * @param \Devester\Poll\Model\Poll $poll poll.
     */
    public function __construct(Poll $poll)
    {
        $this->poll = $poll;
    }

    /**
     * Get options.
     *
     * @return $options[]
     */
    public function toOptionArray()
    {
        $options[] = ['label' => '', 'value' => ''];
        $availableOptions = $this->poll->getAvailableStatuses();
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }

}
