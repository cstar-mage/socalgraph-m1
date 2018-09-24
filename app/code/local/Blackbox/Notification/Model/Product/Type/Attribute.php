<?php

class Blackbox_Notification_Model_Product_Type_Attribute extends Varien_Object
{
    public function _construct()
    {
        parent::_construct();

        $this->setData(array(
            'attribute_code' => 'type_id',
            'frontend_input' => 'multiselect',
            'source' => Mage::getSingleton('blackbox_notification/product_type_source')
        ));
    }

    public function usesSource()
    {
        return true;
    }
}