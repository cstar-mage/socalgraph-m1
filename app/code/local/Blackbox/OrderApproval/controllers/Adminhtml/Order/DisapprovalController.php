<?php

/**
 * Adminhtml sales disapproval edit controller
 *
 * @category   Blackbox
 * @package    Blackbox_OrderApproval
 */
class Blackbox_OrderApproval_Adminhtml_Order_DisapprovalController extends Blackbox_OrderApproval_Controller_Disapproval
{
    /**
     * Get requested items qty's from request
     */
    protected function _getItemQtys()
    {
        $data = $this->getRequest()->getParam('disapproval');
        if (isset($data['items'])) {
            $qtys = $data['items'];
        } else {
            $qtys = array();
        }
        return $qtys;
    }

    /**
     * Initialize disapproval model instance
     *
     * @return Blackbox_OrderApproval_Model_Disapproval
     */
    protected function _initDisapproval($strict = true)
    {
        $this->_title($this->__('Sales'))->_title($this->__('Disapprovals'));

        $disapproval = false;
        $disapprovalId = $this->getRequest()->getParam('disapproval_id');
        $orderId = $this->getRequest()->getParam('order_id');

        if ($disapprovalId) {
            $disapproval = Mage::getModel('order_approval/disapproval')->load($disapprovalId);
            if (!$disapproval->getId()) {
                $this->_getSession()->addError($this->__('The disapproval no longer exists.'));
                return false;
            }
        } elseif ($orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
            /**
             * Check order existing
             */
            if (!$order->getId()) {
                $this->_getSession()->addError($this->__('The order no longer exists.'));
                return false;
            }

            $ruleId = $this->getRequest()->getParam('rule_id');
            if ($strict) {
                if (!$ruleId) {
                    $this->_getSession()->addError($this->__('Please, specify rule to approve.'));
                    return false;
                }
            }

            if ($ruleId) {
                $rule = Mage::getModel('order_approval/rule')->load($ruleId);
                if (!$rule->getId()) {
                    $this->_getSession()->addError($this->__('Wrong rule id.'));
                    return false;
                }

                /**
                 * Check disapproval create availability
                 */
                if (!$order->canApproveByRule($rule)) {
                    $this->_getSession()->addError($this->__('The order does not allow creating a disapproval.'));
                    return false;
                }
            } else {
                $rule = null;
            }
            $disapproval = Mage::getModel('order_approval/service_order', $order)->prepareDisapproval($rule, Mage::getSingleton('customer/session')->getCustomer());
        }

        Mage::register('current_disapproval', $disapproval);
        return $disapproval;
    }

    /**
     * Save data for disapproval and related order
     *
     * @param   Blackbox_OrderApproval_Model_Approval $disapproval
     * @return  Blackbox_OrderApproval_Adminhtml_Order_DisapprovalController
     */
    protected function _saveDisapproval($disapproval)
    {
        $transactionSave = Mage::getModel('core/resource_transaction')
            ->addObject($disapproval)
            ->addObject($disapproval->getOrder())
            ->save();

        return $this;
    }

    /**
     * Disapproval information page
     */
    public function viewAction()
    {
        $disapproval = $this->_initDisapproval();
        if ($disapproval) {
            $this->_title(sprintf("#%s", $disapproval->getIncrementId()));

            $this->loadLayout()
                ->_setActiveMenu('sales/order');
            $this->getLayout()->getBlock('sales_disapproval_view')
                ->updateBackButtonUrl($this->getRequest()->getParam('come_from'));
            $this->renderLayout();
        }
        else {
            $this->_forward('noRoute');
        }
    }

    /**
     * Start create disapproval action
     */
    public function startAction()
    {
        $this->_redirect('*/*/new', array('order_id'=>$this->getRequest()->getParam('order_id')));
    }

    /**
     * Disapproval create page
     */
    public function newAction()
    {
        try {
            $disapproval = $this->_initDisapproval(false);
            if ($disapproval) {
                $this->_title($this->__('New Disapproval'));

                if ($comment = Mage::getSingleton('adminhtml/session')->getCommentText(true)) {
                    $disapproval->setComment($comment);
                }

                $this->loadLayout()
                    ->_setActiveMenu('sales/order')
                    ->renderLayout();
                return;
            }
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Unable to create the disapproval.'));
            Mage::logException($e);
        }
        $this->_redirect('adminhtml/sales_order/view', array('order_id'=>$this->getRequest()->getParam('order_id')));
    }

    /**
     * Save disapproval
     * We can save only new disapproval. Existing disapprovals are not editable
     */
    public function saveAction()
    {
        $comment = $this->getRequest()->getPost('comment');
        $orderId = $this->getRequest()->getParam('order_id');
        $ruleId = $this->getRequest()->getParam('rule_id');

        if (!$ruleId) {
            $this->_getSession()->addError($this->__('Please, specify rule to approve.'));
            if ($orderId) {
                $this->_redirect('adminhtml/sales_order/view', array('order_id' => $orderId));
            } else {
                $this->_redirect('adminhtml/sales/order');
            }
            return;
        }

        if (!empty($comment)) {
            Mage::getSingleton('adminhtml/session')->setCommentText($comment);
        }

        try {
            $disapproval = $this->_initDisapproval();
            if ($disapproval) {
                if (!empty($comment)) {
                    $disapproval->setComment($comment);
                }

                $disapproval->register();

                $notifyCustomer = true;

                if ($notifyCustomer) {
                    $disapproval->setEmailSent(true);
                }

                $disapproval->getOrder()->setCustomerNoteNotify($notifyCustomer);
                if ($notifyCustomer && $disapproval->getHistory()) {
                    $disapproval->getHistory()->setIsCustomerNotified(1);
                }
                //$disapproval->getOrder()->setIsInProcess(true);

                $transactionSave = Mage::getModel('core/resource_transaction')
                    ->addObject($disapproval)
                    ->addObject($disapproval->getOrder());

                $transactionSave->save();

                $this->_getSession()->addSuccess($this->__('The disapproval has been created.'));

                // send disapproval emails
                try {
                    $disapproval->sendEmail($notifyCustomer, $comment);
                } catch (Exception $e) {
                    Mage::logException($e);
                    $this->_getSession()->addError($this->__('Unable to send the disapproval email.'));
                }
                Mage::getSingleton('adminhtml/session')->getCommentText(true);
                $this->_redirect('adminhtml/sales_order/view', array('order_id' => $orderId));
            } else {
                $this->_redirect('*/*/new', array('order_id' => $orderId));
            }
            return;
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Unable to save the disapproval.'));
            Mage::logException($e);
        }
        $this->_redirect('*/*/new', array('order_id' => $orderId));
    }

    /**
     * Cancel disapproval action
     */
    public function cancelAction()
    {
        if ($disapproval = $this->_initDisapproval()) {
            try {
                $disapproval->cancel();
                $this->_saveDisapproval($disapproval);
                $this->_getSession()->addSuccess($this->__('The disapproval has been canceled.'));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError($this->__('Disapproval canceling error.'));
            }
            $this->_redirect('*/*/view', array('disapproval_id'=>$disapproval->getId()));
        } else {
            $this->_forward('noRoute');
        }
    }

    public function addCommentAction()
    {
        try {
            $this->getRequest()->setParam('disapproval_id', $this->getRequest()->getParam('id'));
            $data = $this->getRequest()->getPost('comment');
            if (empty($data['comment'])) {
                Mage::throwException($this->__('The Comment Text field cannot be empty.'));
            }
            $disapproval = $this->_initDisapproval();
            $disapproval->addComment(
                $data['comment'],
                isset($data['is_customer_notified']),
                isset($data['is_visible_on_front'])
            );
            $disapproval->sendUpdateEmail(!empty($data['is_customer_notified']), $data['comment']);
            $disapproval->save();

            $this->loadLayout();
            $response = $this->getLayout()->getBlock('approval_comments')->toHtml();
        } catch (Mage_Core_Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $e->getMessage()
            );
            $response = Mage::helper('core')->jsonEncode($response);
        } catch (Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $this->__('Cannot add new comment.')
            );
            $response = Mage::helper('core')->jsonEncode($response);
        }
        $this->getResponse()->setBody($response);
    }

    /**
     * Generate invoices grid for ajax request
     */
    public function disapprovalsAction()
    {
        $this->_initOrder();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('order_approval/adminhtml_sales_order_view_tab_disapprovals')->toHtml()
        );
    }
}
