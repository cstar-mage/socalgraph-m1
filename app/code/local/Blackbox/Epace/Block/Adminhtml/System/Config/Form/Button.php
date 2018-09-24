<?php

class Blackbox_Epace_Block_Adminhtml_System_Config_Form_Button extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /*
     * Set template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('blackbox/epace/button/test_connection.phtml');
    }

    /**
     * Return element html
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }

    /**
     * Generate button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'id'        => 'epace_test_connection_button',
                'label'     => $this->helper('adminhtml')->__('Test Connection'),
                'onclick'   => 'testConnection()'
            ));

        return $button->toHtml();
    }

    /*protected function _toHtml()
    {
        return $this->getButtonHtml();
    }*/
}