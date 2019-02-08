<?php
 /**
  * Totals item block
  */
class Blackbox_EpaceImport_Block_Adminhtml_Estimate_Totals_Item extends Blackbox_EpaceImport_Block_Adminhtml_Estimate_Totals
{
    /**
     * Determine display parameters before rendering HTML
     *
     * @return Blackbox_EpaceImport_Block_Adminhtml_Estimate_Totals_Item
     */
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();

        $this->setCanDisplayTotalPaid($this->getParentBlock()->getCanDisplayTotalPaid());
        $this->setCanDisplayTotalRefunded($this->getParentBlock()->getCanDisplayTotalRefunded());
        $this->setCanDisplayTotalDue($this->getParentBlock()->getCanDisplayTotalDue());

        return $this;
    }

    /**
     * Initialize totals object
     *
     * @return Blackbox_EpaceImport_Block_Adminhtml_Estimate_Totals_Item
     */
    public function initTotals()
    {
        $total = new Varien_Object(array(
            'code'      => $this->getNameInLayout(),
            'block_name'=> $this->getNameInLayout(),
            'area'      => $this->getDisplayArea(),
            'strong'    => $this->getStrong()
        ));
        if ($this->getBeforeCondition()) {
            $this->getParentBlock()->addTotalBefore($total, $this->getBeforeCondition());
        } else {
            $this->getParentBlock()->addTotal($total, $this->getAfterCondition());
        }
        return $this;
    }

    /**
     * Price HTML getter
     *
     * @param float $baseAmount
     * @param float $amount
     * @return string
     */
    public function displayPrices($baseAmount, $amount)
    {
        return $this->helper('adminhtml/epacei')->displayPrices($this->getEstimate(), $baseAmount, $amount);
    }

    /**
     * Price attribute HTML getter
     *
     * @param string $code
     * @param bool $strong
     * @param string $separator
     * @return string
     */
    public function displayPriceAttribute($code, $strong = false, $separator = '<br/>')
    {
        return $this->helper('adminhtml/epacei')->displayPriceAttribute($this->getSource(), $code, $strong, $separator);
    }

    /**
     * Source estimate getter
     *
     * @return Blackbox_EpaceImport_Model_Estimate
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }
}
