<?php

abstract class Blackbox_Notification_Model_Rule_Abstract extends Mage_Rule_Model_Abstract
{
    const TYPE_PRODUCT_LOW_STOCK = 1;
    const TYPE_ORDER_EXCEED_QTY = 2;
    const TYPE_PRODUCT_ADD_TO_CART = 3;
    const TYPE_APPROVAL_ORDER_CREATE = 4;
    const TYPE_APPROVAL_ORDER_APPROVE = 5;
    const TYPE_ORDER_EMAIL = 6;
    const TYPE_ORDER_UPDATE_EMAIL = 7;
    const TYPE_INVOICE_EMAIL = 8;
    const TYPE_INVOICE_UPDATE_EMAIL = 9;
    const TYPE_SHIPMENT_EMAIL = 10;
    const TYPE_SHIPMENT_UPDATE_EMAIL = 11;
    const TYPE_CREDITMEMO_EMAIL = 12;
    const TYPE_CREDITMEMO_UPDATE_EMAIL = 13;
    const TYPE_APPROVAL_EMAIL = 14;
    const TYPE_APPROVAL_UPDATE_EMAIL = 15;
    const TYPE_DISAPPROVAL_EMAIL = 16;
    const TYPE_DISAPPROVAL_UPDATE_EMAIL = 17;
    const TYPE_ORDER_EDIT = 100;
    const TYPE_HELPDESK_NEW = 1488;
    const TYPE_HELPDESK_SUPPORT_RESPONSE = 1489;

    /**
     * Rule type actions
     */
    const ACTION_DENY = 'deny';
    const ACTION_ALLOW = 'allow';



    protected $_types = array(
        self::TYPE_PRODUCT_LOW_STOCK => 'Products Low Stock',
        self::TYPE_ORDER_EXCEED_QTY => 'Ordered Exceed Qty',
        self::TYPE_PRODUCT_ADD_TO_CART => 'Add Product To Cart',
        self::TYPE_APPROVAL_ORDER_CREATE => 'Create order requiring approval',
        self::TYPE_APPROVAL_ORDER_APPROVE => 'Order Approved',
        self::TYPE_ORDER_EMAIL => 'New Oder Confirmation Email',
        self::TYPE_ORDER_UPDATE_EMAIL => 'Order Comment Email',
        self::TYPE_INVOICE_EMAIL => 'Invoice Email',
        self::TYPE_INVOICE_UPDATE_EMAIL => 'Invoice Comment Email',
        self::TYPE_SHIPMENT_EMAIL => 'Shipment Email',
        self::TYPE_SHIPMENT_UPDATE_EMAIL => 'Shipment Comment Email',
        self::TYPE_CREDITMEMO_EMAIL => 'Credit Memo Email',
        self::TYPE_CREDITMEMO_UPDATE_EMAIL => 'Credit Memo Comment Email',
        self::TYPE_APPROVAL_EMAIL => 'Approval Email',
        self::TYPE_APPROVAL_UPDATE_EMAIL => 'Approval Comment Email',
        self::TYPE_DISAPPROVAL_EMAIL => 'Disapproval Email',
        self::TYPE_DISAPPROVAL_UPDATE_EMAIL => 'Disapproval Cancel Email',
        self::TYPE_ORDER_EDIT => 'Edit Order',
        self::TYPE_HELPDESK_NEW => 'Support New Ticket',
        self::TYPE_HELPDESK_SUPPORT_RESPONSE => 'Support Response',
    );

    protected $_typeConditions = array(
        self::TYPE_PRODUCT_LOW_STOCK => 'blackbox_notification/rule_condition_stock_item_qty',
        self::TYPE_ORDER_EXCEED_QTY => 'blackbox_notification/rule_condition_order_item_qty_exceed',
        self::TYPE_PRODUCT_ADD_TO_CART => 'blackbox_notification/rule_condition_product_combine',
        self::TYPE_APPROVAL_ORDER_CREATE => 'blackbox_notification/rule_condition_blank',
        self::TYPE_APPROVAL_ORDER_APPROVE => 'blackbox_notification/rule_condition_blank',
        self::TYPE_ORDER_EMAIL => 'blackbox_notification/rule_condition_order_attribute_customer_guest',
        self::TYPE_ORDER_UPDATE_EMAIL => 'blackbox_notification/rule_condition_order_attribute_customer_guest',
        self::TYPE_INVOICE_EMAIL => 'blackbox_notification/rule_condition_order_attribute_customer_guest',
        self::TYPE_INVOICE_UPDATE_EMAIL => 'blackbox_notification/rule_condition_order_attribute_customer_guest',
        self::TYPE_SHIPMENT_EMAIL => 'blackbox_notification/rule_condition_order_attribute_customer_guest',
        self::TYPE_SHIPMENT_UPDATE_EMAIL => 'blackbox_notification/rule_condition_order_attribute_customer_guest',
        self::TYPE_CREDITMEMO_EMAIL => 'blackbox_notification/rule_condition_order_attribute_customer_guest',
        self::TYPE_CREDITMEMO_UPDATE_EMAIL => 'blackbox_notification/rule_condition_order_attribute_customer_guest',
        self::TYPE_APPROVAL_EMAIL => 'blackbox_notification/rule_condition_order_attribute_customer_guest',
        self::TYPE_APPROVAL_UPDATE_EMAIL => 'blackbox_notification/rule_condition_order_attribute_customer_guest',
        self::TYPE_DISAPPROVAL_EMAIL => 'blackbox_notification/rule_condition_order_attribute_customer_guest',
        self::TYPE_DISAPPROVAL_UPDATE_EMAIL => 'blackbox_notification/rule_condition_order_attribute_customer_guest',
        self::TYPE_ORDER_EDIT => 'blackbox_notification/rule_condition_blank',
        self::TYPE_HELPDESK_NEW => 'blackbox_notification/rule_condition_blank',
        self::TYPE_HELPDESK_SUPPORT_RESPONSE => 'blackbox_notification/rule_condition_blank',
    );

    public function getTypes()
    {
        return $this->_types;
    }

    /**
     * Get rule condition combine model instance
     *
     * @return Mage_SalesRule_Model_Rule_Condition_Product_Combine
     */
    public function getConditionsInstance()
    {
        return Mage::getModel($this->_typeConditions[$this->getType()]);
    }

    /**
     * Get rule condition combine model instance
     *
     * @return Mage_SalesRule_Model_Rule_Condition_Product_Combine
     */
    public function getActionsInstance()
    {
        return Mage::getModel($this->_typeConditions[$this->getType()]);
    }

    /**
     * @return int
     */
    public function getType()
    {
        $type = parent::getType();
        if (!$type) {
            foreach ($this->_types as $_type => $label) {
                $type = $_type;
                break;
            }
            $this->setType($type);
        }
        return $type;
    }

    public function getTypeText()
    {
        return $this->getTypes()[$this->getType()];
    }

    /**
     * Check cached validation result for specific address
     *
     * @param   Mage_Core_Model_Abstract $item
     * @return  bool
     */
    public function hasIsValidForItem(Mage_Core_Model_Abstract $item)
    {
        $itemId = $item->getId();
        return isset($this->_validatedItems[$itemId]) ? true : false;
    }

    /**
     * Set validation result for specific address to results cache
     *
     * @param   Mage_Core_Model_Abstract $item
     * @param   bool $validationResult
     * @return  Blackbox_Notification_Model_Rule
     */
    public function setIsValidForItem(Mage_Core_Model_Abstract $item, $validationResult)
    {
        $itemId = $item->getId();
        $this->_validatedItems[$itemId] = $validationResult;
        return $this;
    }

    /**
     * Get cached validation result for specific address
     *
     * @param   Mage_Core_Model_Abstract $item
     * @return  bool
     */
    public function getIsValidForItem(Mage_Core_Model_Abstract $item)
    {
        $addressId = $item->getId();
        return isset($this->_validatedItems[$addressId]) ? $this->_validatedItems[$addressId] : false;
    }
}