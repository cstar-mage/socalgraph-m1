<?php

class Blackbox_Epace_Model_System_Config_Source_Mode
{
    /**
     * Returns array to be used in packages request type on back-end
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => '0', 'label' => Mage::helper('epace')->__('Test')),
            array('value' => '1', 'label' => Mage::helper('epace')->__('Live')),
        );
    }
}