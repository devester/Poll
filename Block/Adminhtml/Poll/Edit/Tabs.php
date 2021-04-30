<?php
/**
 * @author Jason Sylvester Devester
 * @copyright Copyright © 2019 Devester. All rights reserved.
 * @package Devester/Poll
 */

namespace Devester\Poll\Block\Adminhtml\Poll\Edit;

/**
 * Admin flexslider group left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Construct.
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        
        $this->setId('poll_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Poll Settings'));
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
    	$this->addTab(
    		'poll_section',
    		[
    			'label' => __('Poll'),
    			'title' => __('Poll'),
    			'active' => true,
    			'content' => $this->getChildHtml('poll_section')
    		]
    	);
    
    	$this->addTab(
    		'answers_section',
    		[
    			'label' => __('Answers'),
    			'title' => __('Answers'),
    			//'active' => true,
    			'content' => $this->getChildHtml('answers_section')
    		]
    	);

    	return parent::_beforeToHtml();
    }
}
