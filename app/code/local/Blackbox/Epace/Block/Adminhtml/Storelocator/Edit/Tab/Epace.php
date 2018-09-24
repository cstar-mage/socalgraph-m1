<?php

class Blackbox_Epace_Block_Adminhtml_Storelocator_Edit_Tab_Epace extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * prepare tab form's information
     *
     * @return Magestore_Storelocator_Block_Adminhtml_Storelocator_Edit_Tab_Generalinfo
     */
    protected function _prepareForm()
    {
        //prepare info form that this want to view

        $form = new Varien_Data_Form();
        $this->setForm($form);

        $dataObj = new Varien_Object(array(
            'store_id' => '',
            'name_in_store' => '',
            'status_in_store' => '',
            'description_in_store' => '',
            'address_in_store' => '',
            'city_in_store' => '',
            'sort_in_store' => ''
        ));

        if (Mage::getSingleton('adminhtml/session')->getFormData()) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData();
        } elseif (Mage::registry('storelocator_data'))
            $data = Mage::registry('storelocator_data')->getData();
        if (isset($data))
            $dataObj->addData($data);
        $data = $dataObj->getData();

        $inStore = $this->getRequest()->getParam('store');
        $defaultLabel = Mage::helper('storelocator')->__('Use Default');
        $defaultTitle = Mage::helper('storelocator')->__('-- Please Select --');
        $scopeLabel = Mage::helper('storelocator')->__('STORE VIEW');

        //Epace Info
        $fieldset = $form->addFieldset('storelocator_epace_info', array('legend' => Mage::helper('storelocator')->__('Epace Info')));

        $fieldset->addField('epace_customer_id', 'text', array(
            'label' => Mage::helper('storelocator')->__('Epace Customer Id'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'epace_customer_id',
            'disabled' => ($inStore && !$data['name_in_store']),
            'after_element_html' => $inStore ? '</td><td class="use-default">
                                      <input id="name_default" name="name_default" type="checkbox" value="1" class="checkbox config-inherit" ' . ($data['name_in_store'] ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
                                      <label for="name_default" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label>
                        </td><td class="scope-label">
                                      [' . $scopeLabel . ']
                        ' : '</td><td class="scope-label">
                                      [' . $scopeLabel . ']',
        ));

        $form->setValues($data);

        return parent::_prepareForm();
    }
}