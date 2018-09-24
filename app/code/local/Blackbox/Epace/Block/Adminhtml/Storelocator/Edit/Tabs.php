<?php

class Blackbox_Epace_Block_Adminhtml_Storelocator_Edit_Tabs extends Magestore_Storelocator_Block_Adminhtml_Storelocator_Edit_Tabs
{
    /**
     * prepare before render block to html
     *
     * @return Magestore_Storelocator_Block_Adminhtml_Storelocator_Edit_Tabs
     */
    protected function _beforeToHtml(){
        $generalTab = new Varien_Object();
        $generalTab->setContent($this->getLayout()->createBlock('storelocator/adminhtml_storelocator_edit_tab_generalinfo')->toHtml());
        Mage::dispatchEvent('storelocator_general_information_tab_before',
            array('tab' => $generalTab,
                'store_id' => $this->getRequest()->getParam('id')));

        $this->addTab('form_section', array(
            'label'	 => Mage::helper('storelocator')->__('General Information'),
            'title'	 => Mage::helper('storelocator')->__('General Information'),
            'content'	 => $generalTab->getContent(),
        ));

        $this->addTab('gmap_section', array(
            'label'     => Mage::helper('storelocator')->__('Google Map'),
            'title'     => Mage::helper('storelocator')->__('Google Map'),
            'content'   => $this->getLayout()->createBlock('storelocator/adminhtml_storelocator_edit_tab_gmap')->toHtml(),

        ));
//            $this->addTab('product', array(
//                'label' => Mage::helper('storelocator')->__('Products'),
//                'url' => $this->getUrl('*/*/product', array('_current' => true)),
//                'content' => $this->getLayout()->createBlock('storelocator/adminhtml_storelocator_edit_tab_product')->toHtml(),
//                'class' => 'ajax',
//            ));

        $this->addTab('timeschedule_section', array(
            'label' => Mage::helper('storelocator')->__('Time Schedule'),
            'title' => Mage::helper('storelocator')->__('Time Schedule'),
            'content' => $this->getLayout()->createBlock('storelocator/adminhtml_storelocator_edit_tab_timeschedule')->toHtml(),
        ));
        $this->addTab('epace', array(
            'label' => Mage::helper('storelocator')->__('Epace'),
            'title' => Mage::helper('storelocator')->__('Epace'),
            'content' => $this->getLayout()->createBlock('epace/adminhtml_storelocator_edit_tab_epace')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
}