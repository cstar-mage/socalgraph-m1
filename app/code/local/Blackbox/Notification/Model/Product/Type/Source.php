<?php

class Blackbox_Notification_Model_Product_Type_Source
{
    protected $_options;

    public function getAllOptions($withEmpty = true)
    {
        if (!isset($this->_options)) {
            $this->_options = array();

            foreach (Mage_Catalog_Model_Product_Type::getTypes() as $code => $type) {
                $this->_options[] = array('value' => $code, 'label' => $type['label']);
            }
        }

        if ($withEmpty) {
            $options = $this->_options;
            array_unshift($options, array('value'=>'', 'label'=>Mage::helper('tax')->__('-- Please Select --')));
            return $options;
        } else {
            return $this->_options;
        }
    }
}