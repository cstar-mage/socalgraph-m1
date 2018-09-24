<?php

class Blackbox_Notification_Model_Notification_Validator extends Blackbox_Notification_Model_Validator_Abstract
{
    /** @var Mage_Customer_Model_Customer */
    protected $currentCustomer = null;

    protected  function _getCollectionModel()
    {
        return Mage::getResourceModel('blackbox_notification/notification_rule_collection');
    }

    public function processStockItem(Mage_CatalogInventory_Model_Stock_Item $item)
    {

    }

    /**
     * @param Mage_Sales_Model_Order_item $item
     */
    public function processOrderedItem($item)
    {
        if ($item->getOrder()) {
            $this->currentCustomer = Mage::getModel('customer/customer')->load($item->getOrder()->getCustomerId());
        }
        return parent::processOrderedItem($item);
    }

    public function processAddedToCartProduct(Mage_Catalog_Model_Product $product)
    {
        $this->currentCustomer = Mage::getSingleton('customer/session')->getCustomer();
        parent::processAddedToCartProduct($product);
    }

    public function processCreateOrderRequiringApproval(Mage_Sales_Model_Order $order)
    {
        $this->currentCustomer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        parent::processCreateOrderRequiringApproval($order);
    }

    public function processOrderApproved(Mage_Sales_Model_Order $order)
    {
        $this->currentCustomer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        parent::processOrderApproved($order);
    }

    public function processSalesEmail($object, $params, $type, $customerEmail)
    {
        if ($object instanceof Mage_Sales_Model_Order) {
            $customerId = $object->getCustomerId();
        } else if ($object instanceof Varien_Object || method_exists($object, 'getOrder')) {
            $order = $object->getOrder();
            if ($order instanceof Mage_Sales_Model_Order) {
                $customerId = $object->getCustomerId();
            }
        }

        $this->currentCustomer = isset($customerId) ? Mage::getModel('customer/customer')->load($customerId) : null;
        return parent::processSalesEmail($object, $params, $type, $customerEmail);
    }

    public function processEditOrder(Mage_Sales_Model_Order $order, Mage_Sales_Model_Order_Status_History $history)
    {
        $this->currentCustomer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        parent::processEditOrder($order, $history);
    }

    public function processHelpdeskTicketNew(Blackbox_HelpDesk_Model_Ticket $ticket)
    {
        $this->currentCustomer = $ticket->getCustomer();
        parent::processHelpdeskTicketNew($ticket);
    }

    public function processHelpdeskSupportResponse(Blackbox_HelpDesk_Model_Ticket $ticket, Blackbox_HelpDesk_Model_Comment $comment)
    {
        $this->currentCustomer = $ticket->getCustomer();
        parent::processHelpdeskSupportResponse($ticket, $comment);
    }

    public function processNotification($type, $validateObject, $params, $additionalEmail = null)
    {
        $this->currentCustomer = $params['customer'];
        parent::processNotification($type, $validateObject, $params, $additionalEmail);
    }

    /**
     * @param Blackbox_Notification_Model_Notification_Rule $rule
     * @param array $params
     */
    protected function _sendNotification(Blackbox_Notification_Model_Rule_Abstract $rule, $params, $additionalEmail = null)
    {
        foreach (explode(',', $rule->getCustomerIds()) as $customerId) {
            if ($customerId) {
                $customer = Mage::getModel('customer/customer')->load($customerId);
            } else {
                $customer = $this->currentCustomer;
            }
            if ($customer && $customer->getId()) {
                $this->_sendNotificationToCustomer($rule, $params, $customer);
            }
        }
    }

    protected function _sendNotificationToCustomer($rule, $params, $customer)
    {
        $params['customer'] = $customer;
        $content = $this->getProcessedContent($rule, $params);

        if (empty($content)) {
            return;
        }

        /** @var Blackbox_Notification_Model_Notification $notification */
        $notification = Mage::getModel('blackbox_notification/notification');
        $notification->setContent($content)
            ->setRule($rule)
            ->setCustomer($customer)
            ->save();
    }

    /**
     * @param Blackbox_Notification_Model_Notification_Rule $rule
     * @param array $params
     * @return string
     */
    protected function getProcessedContent($rule, $params)
    {
        /** @var Mage_Core_Model_Email_Template $template */
        $template = Mage::getModel('core/email_template');

        $template->setTemplateFilter(Mage::getModel('blackbox_notification/head_notification_email_filter_template'))
            ->setTemplateText($rule->getContentTemplate())
            ->setTemplateType(Mage_Core_Model_Email_Template::TYPE_HTML);

        return $template->getProcessedTemplate($params);
    }
}
