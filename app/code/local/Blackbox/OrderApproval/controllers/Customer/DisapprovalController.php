<?php

class Blackbox_OrderApproval_Customer_DisapprovalController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirect('customer/account/login');
            return $this;
        }

        return $this;
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    public function viewAction()
    {
        $disapprovalId = $this->getRequest()->getParam('disapproval_id');

        if (!$disapprovalId) {
            $this->_redirect('*/*/');
            return;
        }

        $disapproval = Mage::getModel('order_approval/disapproval')->load($disapprovalId); /* @var Blackbox_OrderApproval_Model_Disapproval $disapproval */

        $session = Mage::getSingleton('customer/session');
        if (!$disapproval->getId()||
            $disapproval->getState() != Blackbox_OrderApproval_Model_Disapproval::STATE_OPEN ||
            $disapproval->getCustomerId() != $session->getCustomerId() ||
            $disapproval->getStoreId() != Mage::app()->getStore()->getId())
        {
            $session->addError('The disapproval no longer exists');
            $this->_redirect('*/*/');
            return;
        }

        Mage::register('current_disapproval', $disapproval);

        $this->loadLayout();
        $this->initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    public function reapproveAction()
    {
        $disapprovalId = $this->getRequest()->getParam('disapproval_id');

        if (!$disapprovalId) {
            $this->_redirect('*/*/');
            return;
        }

        if (!$this->getRequest()->isPost()) {
            $this->_redirect('*/*/view', array('dissaproval_id' => $disapprovalId));
            return;
        }

        $disapproval = Mage::getModel('order_approval/disapproval')->load($disapprovalId); /* @var Blackbox_OrderApproval_Model_Disapproval $disapproval */

        $session = Mage::getSingleton('customer/session');
        if (!$disapproval->getId()||
            $disapproval->getState() != Blackbox_OrderApproval_Model_Disapproval::STATE_OPEN ||
            $disapproval->getCustomerId() != $session->getCustomerId() ||
            $disapproval->getStoreId() != Mage::app()->getStore()->getId())
        {
            $session->addError('The disapproval no longer exists');
            $this->_redirect('*/*/');
            return;
        }

        $order = $disapproval->getOrder();
        $quote = Mage::getModel('sales/quote')->load($order->getQuoteId()); /* @var Mage_Sales_Model_Quote $quote */

        $itemsData = $this->getRequest()->getPost('item');

        try {
            foreach ($order->getAllItems() as $item) {
                /* @var Mage_Sales_Model_Order_Item $item */
                $data = $itemsData[$item->getId()];

                if (!$data) {
                    throw new Mage_Exception('Missing item data');
                }

                if ($data['qty'] < $item->getQtyInvoiced()) {
                    throw new Mage_Exception('Modified item qty less than already invoiced qty');
                }

                if ($data['qty'] < $item->getQtyShipped()) {
                    throw new Mage_Exception('Modified item qty less than already shipped qty');
                }

                $quoteItem = $quote->getItemById($item->getQuoteItemId());
                $quoteItem->setQty($data['qty']);
                $item->setQtyOrdered($data['qty']);

                Mage::dispatchEvent('order_approval_disapproval_order_item_edit', array('order' => $order, 'item' => $item, 'item_info' => $data));
            }


            Mage::dispatchEvent('order_approval_disapproval_order_edit', array('order' => $order, 'request' => $this->getRequest()));

            $quote->collectTotals()->save();
            Mage::helper('core')->copyFieldset(
                'sales_convert_quote',
                'to_order',
                $quote,
                $order
            );

            $comment = $this->getRequest()->getPost('comment');
            if (!empty($comment)) {
                $order->addStatusHistoryComment('Comment from customer to disapproval (id ' . $disapprovalId . '):' . PHP_EOL . $comment);
            }

            if ($order->getStatus() == Blackbox_OrderApproval_Model_Sales_Order::STATUS_WAITING_CUSTOMER && $order->canUnhold()) {
                $order->unhold();
            }

            $order->save();

            $disapproval->setState(Blackbox_OrderApproval_Model_Disapproval::STATE_REAPPROVE)->save();

            $session->addSuccess('The order was sent to reapprove.');
            $this->_redirect('*/*/');
        } catch (Mage_Exception $e) {
            $session->addError($e->getMessage());
            $this->_redirect('*/*/view', array('disapproval_id' => $disapprovalId));
            return;
        }
    }

    public function cancelAction()
    {
        $disapprovalId = $this->getRequest()->getParam('disapproval_id');

        if (!$disapprovalId) {
            $this->_redirect('*/*/');
        }

        $disapproval = Mage::getModel('order_approval/disapproval')->load($disapprovalId); /* @var Blackbox_OrderApproval_Model_Disapproval $disapproval */


        $session = Mage::getSingleton('customer/session');
        if (!$disapproval->getId()||
            $disapproval->getState() != Blackbox_OrderApproval_Model_Disapproval::STATE_OPEN ||
            $disapproval->getCustomerId() != $session->getCustomerId() ||
            $disapproval->getStoreId() != Mage::app()->getStore()->getId())
        {
            $session->addError('This disapproval no longer exists.');
            $this->_redirect('*/*/');
            return;
        }

        $order = $disapproval->getOrder(); /* @var Blackbox_OrderApproval_Model_Sales_Order $order */

        if (!$order->canCancel()) {
            $session->addError('The cannot be canceled.');
            $this->_redirect('*/*/');
            return;
        }

        $comment = 'Order was cancelled by customer. Related disapproval id: ' . $disapprovalId;
        $order->cancel($comment)->save();

        $session->addSuccess('The order has been cancelled.');
        $this->_redirect('*/*/');
    }
}