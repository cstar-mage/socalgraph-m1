<?php

class Blackbox_Barcode_Block_Adminhtml_Catalog_Product_Edit_Barcode extends Mage_Adminhtml_Block_Catalog_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $form->setDataObject(Mage::registry('product'));

        $fieldset = $form->addFieldset('barcode', array(
            'legend' => Mage::helper('catalog')->__('Barcode'),
            'class' => 'fieldset-wide'
        ));

        $fieldset->addType('barcode_image', 'Blackbox_Barcode_Model_Form_Element_Renderer_Barcode');
        $fieldset->addField('barcode_image', 'barcode_image', [
            'name' => 'barcode_image',
            'label' => 'Barcode',
        ]);

        $fieldset->addField('barcode_data_source', 'select', [
            'name' => 'barcode_data_source',
            'label' => 'Data Source',
            'values' => Mage::getSingleton('barcode/eav_entity_attribute_source_data')->getOptionArray()
        ]);

//        $fieldset->addField('inventory_number', 'text', [
//            'name' => 'inventory_number',
//            'label' => 'Inventory Number'
//        ]);

        $form->addValues($form->getDataObject()->getData());
        $form->setFieldNameSuffix('product');
        $this->setForm($form);
    }
}
