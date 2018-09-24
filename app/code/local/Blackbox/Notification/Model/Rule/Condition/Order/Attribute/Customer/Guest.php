<?php

class Blackbox_Notification_Model_Rule_Condition_Order_Attribute_Customer_Guest extends Mage_Rule_Model_Condition_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('blackbox_notification/rule_condition_order_attribute_customer_guest');

        $this->setValueOption(array('0' => 'No', '1' => 'Yes', '2' => 'Any'));
        $this->setValue('0');
    }

    /**
     * Validate Customer Is Guest
     *
     * @param Varien_Object $object
     *
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = ($object instanceof Mage_Sales_Model_Order) ? $object : $object->getOrder();
        if (!($order instanceof Mage_Sales_Model_Order)) {
            $order = Mage::getModel('sales/order')->load($object->getOrderId());
        }

        $this->setOperator('==');

        return $this->validateAttribute($order->getCustomerIsGuest());
    }

    public function getValueElementType()
    {
        return 'select';
    }

    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml() . Mage::helper('blackbox_notification')->__("Customer Is Guest: %s", $this->getValueElement()->getHtml());
        if ($this->getId() != '1') {
            $html.= $this->getRemoveLinkHtml();
        }
        return $html;
    }

    public function validateAttribute($validatedValue)
    {
        return $this->getValueParsed() == '2' || parent::validateAttribute($validatedValue);
    }
}