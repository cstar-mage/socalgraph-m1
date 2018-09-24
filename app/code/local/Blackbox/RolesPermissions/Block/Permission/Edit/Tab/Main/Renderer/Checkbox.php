<?php

/**
 * Renderer for specific checkbox that is used on Rule Information tab in Shopping cart price rules
 *
 * @package    Blackbox_RolesPermissions
 */
class Blackbox_RolesPermissions_Block_Permission_Edit_Tab_Main_Renderer_Checkbox
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Checkbox render function
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $checkbox = new Varien_Data_Form_Element_Checkbox($element->getData());
        $checkbox->setForm($element->getForm());

        $elementHtml = $checkbox->getElementHtml() . sprintf(
            '<label for="%s"><b>%s</b></label><p class="note">%s</p>',
            $element->getHtmlId(), $element->getLabel(), $element->getNote()
        );
        $html  = '<td class="label">&nbsp;</td>';
        $html .= '<td class="value">' . $elementHtml . '</td>';

        return $html;
    }

}
