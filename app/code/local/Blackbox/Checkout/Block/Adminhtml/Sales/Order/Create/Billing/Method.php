<?php

class Blackbox_Checkout_Block_Adminhtml_Sales_Order_Create_Billing_Method extends Mage_Adminhtml_Block_Sales_Order_Create_Billing_Method
{
    public function setFormTemplate($template)
    {
        $this->getChild('form')->setTemplate($template);
    }
}