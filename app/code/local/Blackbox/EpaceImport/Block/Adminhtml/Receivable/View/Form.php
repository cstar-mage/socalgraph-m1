<?php
/**
 * Adminhtml epacei receivable view plane
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Blackbox_EpaceImport_Block_Adminhtml_Receivable_View_Form extends Mage_Adminhtml_Block_Template
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('blackbox/epacei/receivable/view/form.phtml');
    }
}
