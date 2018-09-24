<?php

/**
 * Notification Validator Model
 *
 * Allows dispatching before and after events for each controller action
 *
 * @package    Blackbox_Notification
 */
class Blackbox_Notification_Model_Validator extends Blackbox_Notification_Model_Validator_Abstract
{
    /** @var Blackbox_Notification_Model_Validator_Abstract[]  */
    protected $validators = [];

    public function __construct()
    {
        parent::__construct();
        $this->validators = [
            Mage::getModel('blackbox_notification/email_validator'),
            Mage::getModel('blackbox_notification/notification_validator'),
        ];
    }

    protected function _getCollectionModel()
    {
    }

    public function init($websiteId, $type)
    {
        foreach ($this->validators as $validator)
        {
            $validator->init($websiteId, $type);
        }
    }

    public function processStockItem(Mage_CatalogInventory_Model_Stock_Item $item)
    {
        foreach ($this->validators as $validator) {
            $validator->processStockItem($item);
        }
    }

    public function processOrderedItem($item)
    {
        foreach ($this->validators as $validator) {
            $validator->processOrderedItem($item);
        }
    }

    public function processAddedToCartProduct(Mage_Catalog_Model_Product $product)
    {
        foreach ($this->validators as $validator) {
            $validator->processAddedToCartProduct($product);
        }
    }

    public function processCreateOrderRequiringApproval(Mage_Sales_Model_Order $order)
    {
        foreach ($this->validators as $validator) {
            $validator->processCreateOrderRequiringApproval($order);
        }
    }

    public function processOrderApproved(Mage_Sales_Model_Order $order)
    {
        foreach ($this->validators as $validator) {
            $validator->processOrderApproved($order);
        }
    }

    public function processSalesEmail($object, $params, $type, $customerEmail)
    {
        foreach ($this->validators as $validator) {
            $validator->processSalesEmail($object, $params, $type, $customerEmail);
        }
    }

    public function processEditOrder(Mage_Sales_Model_Order $order, Mage_Sales_Model_Order_Status_History $history)
    {
        foreach ($this->validators as $validator) {
            $validator->processEditOrder($order, $history);
        }
    }

    public function processHelpdeskTicketNew(Blackbox_HelpDesk_Model_Ticket $ticket)
    {
        foreach ($this->validators as $validator) {
            $validator->processHelpdeskTicketNew($ticket);
        }
    }

    public function processHelpdeskSupportResponse(Blackbox_HelpDesk_Model_Ticket $ticket, Blackbox_HelpDesk_Model_Comment $comment)
    {
        foreach ($this->validators as $validator) {
            $validator->processHelpdeskSupportResponse($ticket, $comment);
        }
    }

    public function processNotification($type, $validateObject, $params, $additionalEmail = null)
    {
        foreach ($this->validators as $validator) {
            $validator->processNotification($type, $validateObject, $params, $additionalEmail);
        }
    }

    /**
     * @param Blackbox_Notification_Model_Rule $rule
     * @param array $params
     */
    protected function _sendNotification(Blackbox_Notification_Model_Rule_Abstract $rule, $params, $additionalEmail = null)
    {
    }
}
