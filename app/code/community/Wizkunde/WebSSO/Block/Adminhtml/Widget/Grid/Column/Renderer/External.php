<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Grid input column renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Wizkunde_WebSSO_Block_Adminhtml_Widget_Grid_Column_Renderer_External
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected $_values;

    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $valueData = unserialize($row->getData($this->getColumn()->getIndex()));

        $randomId = $this->generateRandomString();

        $html = '<input type="text" name="external[]" value="' . $valueData['value'] . '" class="text" style="width:300px;" placeholder="External Attribute">';
        $html .= '&nbsp;&nbsp;<select name="transform[]" class="select" onchange="setInputBox(this, \'' . $randomId . '\');">';

        $selected = ($valueData['transform'] == 'string') ? 'selected="selected"' : '';
        $html .= '<option value="string" ' . $selected . '>Use value unmodified</option>';

        $selected = ($valueData['transform'] == 'default') ? 'selected="selected"' : '';
        $html .= '<option value="default" ' . $selected . '>Default value on empty</option>';

        $selected = ($valueData['transform'] == 'password') ? 'selected="selected"' : '';
        $html .= '<option value="password" ' . $selected . '>Magento Password Hash</option>';

        $selected = ($valueData['transform'] == 'before') ? 'selected="selected"' : '';
        $html .= '<option value="before" ' . $selected . '>Substring: Before occurance</option>';

        $selected = ($valueData['transform'] == 'after') ? 'selected="selected"' : '';
        $html .= '<option value="after" ' . $selected . '>Substring: After occurance</option>';

        $selected = ($valueData['transform'] == 'preg') ? 'selected="selected"' : '';
        $html .= '<option value="preg" ' . $selected . '>PERL regular expression</option>';

        $html .= '</select>&nbsp;&nbsp;';

        $display = (in_array($valueData['transform'], array('before', 'after', 'preg'))) ? '' : 'display: none;';
        $html .= '<input type="text" name="extra[]" id="extra-' . $randomId . '" class="text" style="' . $display . 'width:200px;" value="' . $valueData['extra'] . '">';

        return $html;
    }

    protected function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
