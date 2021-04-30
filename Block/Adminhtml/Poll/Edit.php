<?php
/**
 * @author Jason Sylvester Devester
 * @copyright Copyright © 2019 Devester. All rights reserved.
 * @package Devester/Poll
 */

namespace Devester\Poll\Block\Adminhtml\Poll;

// use Magento\Backend\Block\Widget\Context;
// use Magento\Framework\Registry;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry.
     *
     * @var Registry
     */
    protected $_coreRegistry = null;
	
    /**
     * Construct.
     *
     * @param Context      $context       Context.
     * @param Registry     $registry      CoreRegistry.
     * @param array        $data          Data.
     */
	public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
	) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
	}

    /**
     * Initialize flexslider group edit block.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'poll_Id';
        $this->_blockGroup = 'Devester_Poll';
        $this->_controller = 'adminhtml_poll';

        parent::_construct();

        if ($this->_isAllowedAction('Devester_Poll::polls')) {
            $this->buttonList->update('save', 'label', __('Save Poll'));
            $this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => [
                                'event' => 'saveAndContinueEdit',
                                'target' => '#edit_form'
                            ],
                        ],
                    ]
                ],
                -100
            );
        } else {
            $this->buttonList->remove('save');
        }

        if ($this->_isAllowedAction('Devester_Poll::polls')) {
            $this->buttonList->update('delete', 'label', __('Delete Poll'));
        } else {
            $this->buttonList->remove('delete');
        }
    }

    /**
     * Prepare collection.
     *
     * @param string $resourceId Resource ID.
     *
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * Getter of url for "Save and Continue" button.
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl(
            '*/*/save',
            ['_current' => true, 'back' => 'edit', 'tab' => '{{tab_id}}']
        );
    }
}