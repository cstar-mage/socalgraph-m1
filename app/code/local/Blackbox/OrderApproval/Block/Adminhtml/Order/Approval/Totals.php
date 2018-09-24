<?php

/**
 * Adminhtml order approval totals block
 *
 * @category    Blackbox
 * @package     Blackbox_OrderApproval
 */
class Blackbox_OrderApproval_Block_Adminhtml_Order_Approval_Totals extends Mage_Adminhtml_Block_Sales_Totals
{
    protected $_approval = null;

    public function getApproval()
    {
        if ($this->_approval === null) {
            if ($this->hasData('approval')) {
                $this->_approval = $this->_getData('approval');
            } elseif (Mage::registry('current_approval')) {
                $this->_approval = Mage::registry('current_approval');
            } elseif ($this->getParentBlock()->getApproval()) {
                $this->_approval = $this->getParentBlock()->getApproval();
            }
        }
        return $this->_approval;
    }
    
    public function getSource()
    {
        return $this->getApproval();
    }
    
    /**
     * Initialize order totals array
     *
     * @return Mage_Sales_Block_Order_Totals
     */
    protected function _initTotals()
    {
        parent::_initTotals();
        return $this;
    }
}
