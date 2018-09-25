<?php

class Blackbox_Barcode_Block_Adminhtml_Catalog_Product_Edit_Tab_Barcode extends Mage_Adminhtml_Block_Widget
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function canShowTab()
    {
        return true;
    }

    public function getTabLabel()
    {
        return $this->__('Barcode');
    }

    public function getTabTitle()
    {
        return $this->__('Barcode');
    }

    public function isHidden()
    {
        return false;
    }

    public function getTabUrl()
    {
        return $this->getUrl('*/*/barcode', array('_current' => true));
    }

    public function getTabClass()
    {
        return 'ajax';
    }
}