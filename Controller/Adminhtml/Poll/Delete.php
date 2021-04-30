<?php
/**
 * @author Jason Sylvester Devester
 * @copyright Copyright © 2019 Devester. All rights reserved.
 * @package Devester/Poll
 */

namespace Devester\Poll\Controller\Adminhtml\Poll;

use Magento\Backend\App\Action;
use Magento\Backend\Helper\Js;
use Magento\Backend\App\Action\Context;

class Delete extends Action
{
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization
            ->isAllowed('Devester_Poll::Poll');
    }

    /**
     * Delete action.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('poll_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $group = "";
            try {
                // init model and delete
                $model = $this->_objectManager->create('Devester\Poll\Model\Poll');
                $model->load($id);
                $title = $model->getTitle();
                
                if($model->countSlideIds($id) == 0) {
                    $model->delete();
                    $this->messageManager
                            ->addSuccess(__('The group has been deleted.'));
                    $this->_eventManager->dispatch(
                        'adminhtml_devester_poll_poll_on_delete',
                        ['title' => $title, 'status' => 'success']
                    );
                    return $resultRedirect->setPath('*/*/');
                } else {
                    $this->_eventManager->dispatch(
                        'adminhtml_devester_poll_poll_on_delete',
                        ['title' => $title, 'status' => 'fail']
                    );
                    $this->messageManager->addError(__('This group has slides connected, please disconnect all slides before deleting the group.'));
                    return $resultRedirect->setPath(
                                                '*/*/edit',
                                                ['poll_id' => $id]
                                            );
                }
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_devester_poll_poll_on_delete',
                    ['title' => $title, 'status' => 'fail']
                );
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath(
                                            '*/*/edit',
                                            ['poll_id' => $id]
                                        );
            }
        }
        $this->messageManager
            ->addError(__('We can\'t find a group to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}