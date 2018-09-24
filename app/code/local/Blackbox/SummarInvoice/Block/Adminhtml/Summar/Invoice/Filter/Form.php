<?php

class Blackbox_SummarInvoice_Block_Adminhtml_Summar_Invoice_Filter_Form extends Mage_Adminhtml_Block_Report_Filter_Form
{
    /**
     * Add fieldset with general report fields
     *
     * @return $this;
     */
    protected function _prepareForm()
    {
        $actionUrl = $this->getUrl('*/*/sales');
        $form = new Varien_Data_Form(
            array('id' => 'filter_form', 'action' => $actionUrl, 'method' => 'get')
        );
        $htmlIdPrefix = 'sales_report_';
        $form->setHtmlIdPrefix($htmlIdPrefix);
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('reports')->__('Filter')));

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        $fieldset->addField('store_ids', 'hidden', array(
            'name'  => 'store_ids'
        ));

        $fieldset->addField('from', 'date', array(
            'name'      => 'from',
            'format'    => $dateFormatIso,
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'label'     => Mage::helper('reports')->__('From'),
            'title'     => Mage::helper('reports')->__('From'),
            'required'  => true
        ));

        $fieldset->addField('to', 'date', array(
            'name'      => 'to',
            'format'    => $dateFormatIso,
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'label'     => Mage::helper('reports')->__('To'),
            'title'     => Mage::helper('reports')->__('To'),
            'required'  => true
        ));

        $fieldset->addField('customer_id', 'select', array(
            'name'      => 'customer_id',
            'values'   => $this->_getCustomerOptions(),
            'label'     => Mage::helper('reports')->__('Customer Id'),
            'title'     => Mage::helper('reports')->__('Customer Id')
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        return $this;
    }

    protected function _getCustomerOptions()
    {
        $result = array();

        $customers = Mage::getModel('customer/customer')->getCollection()
            ->addAttributeToSelect('firstname')
            ->addAttributeToSelect('lastname');

        foreach ($customers as $customer) {
            $result[] = array(
                'label' => $customer->getName() . ' ' . $customer->getEmail(),
                'value' => $customer->getId()
            );
        }

        return $result;
    }
}