<?php

/**
 * Source model for adminhtml to add the bindings to the configuration section
 *
 * Class Wizkunde_WebSSO_Model_Idps
 */
class Wizkunde_WebSSO_Model_Idps
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $collection = Mage::getModel('websso/idp')->getCollection();

        $returnData = array();
        foreach($collection as $item) {
            $returnData[] = array('value' => $item->getData('identifier'), 'label' => $item->getData('name'));
        }

        return $returnData;
    }
}