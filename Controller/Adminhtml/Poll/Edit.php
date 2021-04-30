<?php
/**
 * @author Jason Sylvester Devester
 * @copyright Copyright © 2019 Devester. All rights reserved.
 * @package Devester/Poll
 */

namespace Devester\Poll\Controller\Adminhtml\Poll;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * Core registry.
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

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
     * @param Registry    $registry          Registry.
     */
	public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry
	) {
		$this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
		parent::__construct($context);
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
     * Init actions.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }

    /**
     * Edit Poll.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
	public function execute()
	{
		$id = $this->getRequest()->getParam('poll_id');


		// $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/editing.log');
		// $logger = new \Zend\Log\Logger();
		// $logger->addWriter($writer);
		// $logger->info($id);
		$model = $this->_objectManager->create('Devester\Poll\Model\Poll');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager
                    ->addError(__('This poll no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_objectManager
            ->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->_coreRegistry->register('devester_poll_poll', $model);

        $resultPage = $this->_initAction();

        $resultPage->getConfig()
            ->getTitle()->prepend(__('Poll - Management'));
        $resultPage->getConfig()->getTitle()
        ->prepend($model->getId() ? sprintf(__('Poll - Edit "%s"'),
                    $model->getTitle()) : __('Poll - New'));
		return $resultPage;
	}
}