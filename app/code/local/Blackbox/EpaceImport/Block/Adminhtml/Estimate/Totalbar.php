<?php
/**
 * Adminhtml creditmemo bar
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Blackbox_EpaceImport_Block_Adminhtml_Estimate_Totalbar extends Blackbox_EpaceImport_Block_Adminhtml_Estimate_Abstract
{
    protected $_totals = array();

    /**
     * Retrieve required options from parent
     */
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            Mage::throwException(Mage::helper('adminhtml')->__('Invalid parent block for this block.'));
        }
        $this->setEstimate($this->getParentBlock()->getEstimate());
        $this->setSource($this->getParentBlock()->getSource());
        $this->setCurrency($this->getParentBlock()->getEstimate()->getEstimateCurrency());

        foreach ($this->getParentBlock()->getEstimateTotalbarData() as $v) {
            $this->addTotal($v[0], $v[1], $v[2]);
        }

        parent::_beforeToHtml();
    }

    protected function getTotals()
    {
        return $this->_totals;
    }

    public function addTotal($label, $value, $grand = false)
    {
        $this->_totals[] = array(
            'label' => $label,
            'value' => $value,
            'grand' => $grand
        );
        return $this;
    }
}
