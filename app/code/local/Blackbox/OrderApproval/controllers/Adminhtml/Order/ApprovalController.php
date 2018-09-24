<?php

/**
 * Adminhtml sales approval edit controller
 *
 * @category   Blackbox
 * @package    Blackbox_OrderApproval
 */
class Blackbox_OrderApproval_Adminhtml_Order_ApprovalController extends Blackbox_OrderApproval_Controller_Approval
{
    /**
     * Get requested items qty's from request
     */
    protected function _getItemQtys()
    {
        $data = $this->getRequest()->getParam('approval');
        if (isset($data['items'])) {
            $qtys = $data['items'];
        } else {
            $qtys = array();
        }
        return $qtys;
    }

    /**
     * Initialize approval model instance
     *
     * @return Blackbox_OrderApproval_Model_Approval|false
     */
    protected function _initApproval($update = false)
    {
        $this->_title($this->__('Sales'))->_title($this->__('Approvals'));

        $approval = false;
        $approvalId = $this->getRequest()->getParam('approval_id');
        $orderId = $this->getRequest()->getParam('order_id');

        if ($approvalId) {
            $approval = Mage::getModel('order_approval/approval')->load($approvalId);
            if (!$approval->getId()) {
                $this->_getSession()->addError($this->__('The approval no longer exists.'));
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

            $rules = Mage::helper('order_approval')->getAvailableRules($order);
            if (empty($rules)) {
                $this->_getSession()->addError($this->__('No rules you can approve this order by.'));
                return false;
            }

            $ruleId = $this->getRequest()->getParam('rule_id');
            if (!$ruleId  && !($ruleId = $this->_getSession()->getApprovalRuleId())) {
                $this->_getSession()->setApprovalRuleId($ruleId = $rules[0]->getId());
            }
            Mage::register('approval_rules', $rules);

            $rule = Mage::getModel('order_approval/rule')->load($ruleId);
            if (!$rule->getId()) {
                $this->_getSession()->addError($this->__('Wrong rule id.'));
                return false;
            }

            /**
             * Check approval create availability
             */
            if (!$order->canApprove()) {
                $this->_getSession()->addError($this->__('The order does not allow creating an approval.'));
                return false;
            }
            $savedQtys = $this->_getItemQtys();
            $approval = Mage::getModel('order_approval/service_order', $order)->prepareApproval($savedQtys, $rule, Mage::getSingleton('customer/session')->getCustomer());
            if (!$approval->getTotalQty()) {
                Mage::throwException($this->__('Cannot create an approval without products.'));
            }
        }

        Mage::register('current_approval', $approval);
        return $approval;
    }

    /**
     * Save data for approval and related order
     *
     * @param   Blackbox_OrderApproval_Model_Approval $approval
     * @return  Blackbox_OrderApproval_Adminhtml_Order_ApprovalController
     */
    protected function _saveApproval($approval)
    {
        //$approval->getOrder()->setIsInProcess(true);
        $transactionSave = Mage::getModel('core/resource_transaction')
            ->addObject($approval)
            ->addObject($approval->getOrder())
            ->save();

        return $this;
    }

    /**
     * Approval information page
     */
    public function viewAction()
    {
        $approval = $this->_initApproval();
        if ($approval) {
            $this->_title(sprintf("#%s", $approval->getIncrementId()));

            $this->loadLayout()
                ->_setActiveMenu('sales/order');
            $this->getLayout()->getBlock('sales_approval_view')
                ->updateBackButtonUrl($this->getRequest()->getParam('come_from'));
            $this->renderLayout();
        }
        else {
            $this->_forward('noRoute');
        }
    }

    /**
     * Start create approval action
     */
    public function startAction()
    {
        $session = $this->_getSession();

        /**
         * Clear old values for approval qty's
         */
        $session->getApprovalItemQtys(true);

        /**
         * Clear old value for approval rule
         */
        $session->getApprovalRuleId(true);

        $this->_redirect('*/*/new', array('order_id' => $this->getRequest()->getParam('order_id')));
    }

    /**
     * Approval create page
     */
    public function newAction()
    {
        try {
            $approval = $this->_initApproval();
            if ($approval) {
                $this->_title($this->__('New Approval'));

                if ($comment = Mage::getSingleton('adminhtml/session')->getCommentText(true)) {
                    $approval->setCommentText($comment);
                }

                $this->loadLayout()
                    ->_setActiveMenu('sales/order')
                    ->renderLayout();
                return;
            }
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Unable to create the approval.'));
            Mage::logException($e);
        }
        $this->_redirect('adminhtml/sales_order/view', array('order_id'=>$this->getRequest()->getParam('order_id')));
    }

    /**
     * Update items qty action
     */
    public function updateQtyAction()
    {
        try {
            $approval = $this->_initApproval(true);
            // Save approval comment text in current approval object in order to display it in corresponding view
            $approvalRawData = $this->getRequest()->getParam('approval');
            $approvalRawCommentText = $approvalRawData['comment_text'];
            $approval->setCommentText($approvalRawCommentText);

            $this->loadLayout();
            $response = $this->getLayout()->getBlock('order_items')->toHtml();
        } catch (Mage_Core_Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $e->getMessage()
            );
            $response = Mage::helper('core')->jsonEncode($response);
        } catch (Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $this->__('Cannot update item quantity.')
            );
            $response = Mage::helper('core')->jsonEncode($response);
        }
        $this->getResponse()->setBody($response);
    }

    /**
     * Update items qty action
     */
    public function updateRuleAction()
    {
        try {
            $approval = $this->_initApproval(true);
            // Save approval comment text in current approval object in order to display it in corresponding view
            $approvalRawCommentText = $this->getRequest()->getParam('comment_text');
            $approval->setCommentText($approvalRawCommentText);

            $this->loadLayout();
            $response = $this->getLayout()->getBlock('order_items')->toHtml();

            $this->_getSession()->setApprovalRuleId($approval->getRuleId());
        } catch (Mage_Core_Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $e->getMessage(),
                'rule_id'   => $this->_getSession()->getApprovalRuleId()
            );
            $response = Mage::helper('core')->jsonEncode($response);
        } catch (Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $this->__('Cannot update approval rule.'),
                'rule_id'   => $this->_getSession()->getApprovalRuleId()
            );
            $response = Mage::helper('core')->jsonEncode($response);
        }
        $this->getResponse()->setBody($response);
    }

    /**
     * Save approval
     * We can save only new approval. Existing approvals are not editable
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost('approval');
        $orderId = $this->getRequest()->getParam('order_id');

        if (!empty($data['comment_text'])) {
            Mage::getSingleton('adminhtml/session')->setCommentText($data['comment_text']);
        }

        $ruleId = $this->getRequest()->getParam('rule_id');

        if (!$ruleId) {
            $this->_getSession()->addError($this->__('Please, specify rule to approve.'));
            if ($orderId) {
                $this->_redirect('*/*/new', array('order_id' => $orderId));
            } else {
                $this->_redirect('adminhtml/sales/order');
            }
            return false;
        }

        try {
            $approval = $this->_initApproval();
            if ($approval) {
                if (!empty($data['comment_text'])) {
                    $approval->addComment(
                        $data['comment_text'],
                        isset($data['comment_customer_notify']),
                        isset($data['is_visible_on_front'])
                    );
                }

                $approval->register();

                if (!empty($data['send_email'])) {
                    $approval->setEmailSent(true);
                }

                $approval->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));
                //$approval->getOrder()->setIsInProcess(true);

                $transactionSave = Mage::getModel('core/resource_transaction')
                    ->addObject($approval)
                    ->addObject($approval->getOrder());

                $transactionSave->save();

                $this->_getSession()->addSuccess($this->__('The approval has been created.'));

                // send approval emails
                $comment = $data['comment_text'];
                try {
                    $approval->sendEmail(!empty($data['send_email']), $comment);
                } catch (Exception $e) {
                    Mage::logException($e);
                    $this->_getSession()->addError($this->__('Unable to send the approval email.'));
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
            $this->_getSession()->addError($this->__('Unable to save the approval.'));
            Mage::logException($e);
        }
        $this->_redirect('*/*/new', array('order_id' => $orderId));
    }

    /**
     * Cancel approval action
     */
    public function cancelAction()
    {
        if ($approval = $this->_initApproval()) {
            try {
                $approval->cancel();
                $this->_saveApproval($approval);
                $this->_getSession()->addSuccess($this->__('The approval has been canceled.'));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError($this->__('Approval canceling error.'));
            }
            $this->_redirect('*/*/view', array('approval_id'=>$approval->getId()));
        } else {
            $this->_forward('noRoute');
        }
    }

    public function addCommentAction()
    {
        try {
            $this->getRequest()->setParam('approval_id', $this->getRequest()->getParam('id'));
            $data = $this->getRequest()->getPost('comment');
            if (empty($data['comment'])) {
                Mage::throwException($this->__('The Comment Text field cannot be empty.'));
            }
            $approval = $this->_initApproval();
            $approval->addComment(
                $data['comment'],
                isset($data['is_customer_notified']),
                isset($data['is_visible_on_front'])
            );
            $approval->sendUpdateEmail(!empty($data['is_customer_notified']), $data['comment']);
            $approval->save();

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
     * Create pdf for current approval
     */
    public function printAction()
    {
        $this->_initApproval();
        parent::printAction();
    }

    /**
     * Generate invoices grid for ajax request
     */
    public function approvalsAction()
    {
        $this->_initOrder();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('order_approval/adminhtml_sales_order_view_tab_approvals')->toHtml()
        );
    }
}
