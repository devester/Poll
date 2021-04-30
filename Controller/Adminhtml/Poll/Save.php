<?php
/**
 * @author Jason Sylvester Devester
 * @copyright Copyright © 2019 Devester. All rights reserved.
 * @package Devester/Poll
 */

namespace Devester\Poll\Controller\Adminhtml\Poll;

// use Magento\Backend\App\Action;
// use Magento\Backend\Helper\Js;
// use Magento\Backend\App\Action\Context;

class Save extends \Magento\Backend\App\Action
{
    /**
     * Variable.
     *
     * @var \Magento\Backend\Helper\Js
     */
    protected $jsHelper;

    /**
     * Construct.
     *
     * @param \Magento\Backend\Helper\Js          $jsHelper JSHelper.
     * @param \Magento\Backend\App\Action\Context $context  Context.
     */
    public function __construct(
        \Magento\Backend\Helper\Js $jsHelper,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->jsHelper = $jsHelper;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization
            ->isAllowed('Devester_Poll::Poll');
    }

    /**
     * Save action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/saving.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
        // $params = $this->getRequest()->getParams();
		// $logger->info($params);


        $data = $this->getRequest()->getPostValue();
        $logger->info($data);
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data) {
        	/** @var \Devester\Poll\Model\Poll $model */
            $model = $this->_objectManager->create('Devester\Poll\Model\Poll');

            $id = $this->getRequest()->getParam('poll_id');
            // $id = $this->getRequest()->getParam('poll_id');
            // $params = $this->getRequest()->getParams();
            // if (isset($params['poll']['poll_id'])) {
           	// 	$id = $params['poll']['poll_id'];
           	// 	$logger->info($id); 
            // }
            if ($id) {
                $model->load($id);
            }

            $model->setData($data);

            $this->_eventManager->dispatch(
                'devester_poll_poll_prepare_save',
                ['post' => $model, 'request' => $this->getRequest()]
            );

            try {
                $model->save();
                $this->messageManager->addSuccess(__('You saved this poll.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')
                    ->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        ['poll_id' => $model->getId(), '_current' => true]
                    );
                }
                return $resultRedirect->setPath('*/*/');

            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError(
                	$e->getMessage()
                );
            } catch (\RuntimeException $e) {
                $this->messageManager->addError(
                	$e->getMessage()
                );
            } catch (\Exception $e) {
                $this->messageManager->addException(
                    $e, __('Something went wrong while saving the poll.')
                );
            }

            $this->_getSession()->setFormData($data);

            return $resultRedirect->setPath(
                '*/*/edit',
                ['poll_id' => $this->getRequest()->getParam('poll_id')]
            );
        }

        return $resultRedirect->setPath('*/*/');
    }
}