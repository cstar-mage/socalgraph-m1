<?php

//class Blackbox_Checkout_Model_Resource_Sales_Order_Collection extends Mage_Sales_Model_Resource_Order_Collection
//{
//    protected function _beforeLoad()
//    {
//        parent::_beforeLoad();
//        $this->addAttributeToFilter('entity_id', array('neq' => 0));
//        return $this;
//    }
//
//    protected function _addAddressFields()
//    {
//        parent::_addAddressFields();
//        $this->getSelect()->group('entity_id');
//        return $this;
//    }
//}