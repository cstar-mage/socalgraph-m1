<?php
class Cmsmart_Onlinedesign_Block_Adminhtml_Renderer_Display  
extends Mage_Adminhtml_Block_Widget
 implements Varien_Data_Form_Element_Renderer_Interface
{

    /**
     * Initialize block
     */
    public function __construct()
    {
        $this->setTemplate('onlinedesign/system/config/jscolor.phtml');
    }

    /**
     * Render HTML
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

}