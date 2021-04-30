<?php
/**
 * @author Jason Sylvester Devester
 * @copyright Copyright © 2019 Devester. All rights reserved.
 * @package Devester/Poll
 */

namespace Devester\Poll\Controller\Adminhtml\Poll;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    /**
     * Variable.
     *
     * @var PageFactory
     */
    protected $resultPageFactory;
	
    /**
     * Construct.
     *
     * @param Context     $context           Context.
     * @param PageFactory $resultPageFactory ResultPageFactory.
     */
	public function __construct(
        Context $context,
        PageFactory $resultPageFactory
	) {
		parent::__construct($context);
		$this->resultPageFactory = $resultPageFactory;
	}

    /**
     * Check the permission to run it.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization
            ->isAllowed('Devester_Poll::polls');
    }

    /**
     * Index action.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
	public function execute()
	{

		$resultPage = $this->resultPageFactory->create();
		$resultPage->setActiveMenu('Devester_Poll::polls');
		// $resultPage->getConfig()->getTitle()->prepend((__('Polls')));

		return $resultPage;
		/*
		// $polls = $this->_pollFactory->create();


		// $answerCollection = $answers->getCollection();
		$answers = $this->_answerFactory->create();

		$pollCollection = $polls->getCollection();
		foreach($pollCollection as $poll){

			$answerCollection = $answers->getCollection()->addFieldToFilter('poll_id', $poll->getData('poll_id'));

			echo "<pre>";
			echo "<hr>";
			print_r($poll->getData());
			echo "<br><br>";
			foreach($answerCollection as $answer){
				print_r($answer->getData());
			}
			echo "<hr>";
			echo "</pre>";
		}
		exit();
		return $this->_pageFactory->create();*/
	}
}