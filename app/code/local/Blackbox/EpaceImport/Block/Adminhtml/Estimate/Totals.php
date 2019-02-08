<?php
/**
 * Adminhtml estimate totals block
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Block_Adminhtml_Estimate_Totals extends Blackbox_EpaceImport_Block_Adminhtml_Totals
{
    /**
     * Initialize estimate totals array
     *
     * @return Mage_Sales_Block_Estimate_Totals
     */
    protected function _initTotals()
    {
        parent::_initTotals();
        $this->_totals['paid'] = new Varien_Object(array(
            'code'      => 'paid',
            'strong'    => true,
            'value'     => $this->getSource()->getTotalPaid(),
            'base_value'=> $this->getSource()->getBaseTotalPaid(),
            'label'     => $this->helper('epacei')->__('Total Paid'),
            'area'      => 'footer'
        ));
        $this->_totals['refunded'] = new Varien_Object(array(
            'code'      => 'refunded',
            'strong'    => true,
            'value'     => $this->getSource()->getTotalRefunded(),
            'base_value'=> $this->getSource()->getBaseTotalRefunded(),
            'label'     => $this->helper('epacei')->__('Total Refunded'),
            'area'      => 'footer'
        ));
        $this->_totals['due'] = new Varien_Object(array(
            'code'      => 'due',
            'strong'    => true,
            'value'     => $this->getSource()->getTotalDue(),
            'base_value'=> $this->getSource()->getBaseTotalDue(),
            'label'     => $this->helper('epacei')->__('Total Due'),
            'area'      => 'footer'
        ));
        return $this;
    }
}
