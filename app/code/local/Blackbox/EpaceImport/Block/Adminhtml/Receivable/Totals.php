<?php
/**
 * Adminhtml receivable totals block
 */
class Blackbox_EpaceImport_Block_Adminhtml_Receivable_Totals extends Mage_Core_Block_Template
{
    /**
     * Associated array of totals
     * array(
     *  $totalCode => $totalObject
     * )
     *
     * @var array
     */
    protected $_totals;
    protected $_receivable = null;

    /**
     * Initialize self totals and children blocks totals before html building
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->_initTotals();
        foreach ($this->getChild() as $child) {
            if (method_exists($child, 'initTotals')) {
                $child->initTotals();
            }
        }

        $this->_totals['paid'] = new Varien_Object(array(
            'code'      => 'paid',
            'strong'    => true,
            'value'     => $this->getSource()->getGrandTotal() - $this->getSource()->getUnpaidAmount(),
            'base_value'=> $this->getSource()->getBaseGrandTotal() - $this->getSource()->getUnpaidAmount(),
            'label'     => $this->helper('epacei')->__('Total Paid'),
            'area'      => 'footer'
        ));
        return parent::_beforeToHtml();
    }

    /**
     * Get receivable object
     *
     * @return Blackbox_EpaceImport_Model_Receivable
     */
    public function getReceivable()
    {
        if ($this->_receivable === null) {
            if ($this->hasData('receivable')) {
                $this->_receivable = $this->_getData('receivable');
            } elseif (Mage::registry('current_receivable')) {
                $this->_receivable = Mage::registry('current_receivable');
            } elseif ($this->getParentBlock()->getReceivable()) {
                $this->_receivable = $this->getParentBlock()->getReceivable();
            }
        }
        return $this->_receivable;
    }

    public function setReceivable($receivable)
    {
        $this->_receivable = $receivable;
        return $this;
    }

    /**
     * Get totals source object
     *
     * @return Blackbox_EpaceImport_Model_Receivable
     */

    public function getSource()
    {
        return $this->getReceivable();
    }

    /**
     * Initialize receivable totals array
     *
     * @return $this
     */
    protected function _initTotals()
    {
        $source = $this->getSource();

        $this->_totals = array();
        $this->_totals['subtotal'] = new Varien_Object(array(
            'code'  => 'subtotal',
            'value' => $source->getSubtotal(),
            'label' => $this->__('Subtotal')
        ));


        /**
         * Add shipping
         */
        if (!$source->getIsVirtual() && ((float) $source->getShippingAmount() || $source->getShippingDescription()))
        {
            $this->_totals['shipping'] = new Varien_Object(array(
                'code'  => 'shipping',
                'field' => 'shipping_amount',
                'value' => $this->getSource()->getShippingAmount(),
                'label' => $this->__('Shipping & Handling')
            ));
        }

        /**
         * Add discount
         */
        if (((float)$this->getSource()->getDiscountAmount()) != 0) {
            if ($this->getSource()->getDiscountDescription()) {
                $discountLabel = $this->__('Discount (%s)', $source->getDiscountDescription());
            } else {
                $discountLabel = $this->__('Discount');
            }
            $this->_totals['discount'] = new Varien_Object(array(
                'code'  => 'discount',
                'field' => 'discount_amount',
                'value' => $source->getDiscountAmount(),
                'label' => $discountLabel
            ));
        }

        $this->_totals['grand_total'] = new Varien_Object(array(
            'code'  => 'grand_total',
            'field'  => 'grand_total',
            'strong'=> true,
            'value' => $source->getGrandTotal(),
            'label' => $this->__('Grand Total')
        ));

        /**
         * Base grandtotal
         */
        if ($this->getReceivable()->isCurrencyDifferent()) {
            $this->_totals['base_grandtotal'] = new Varien_Object(array(
                'code'  => 'base_grandtotal',
                'value' => $this->getReceivable()->formatBasePrice($source->getBaseGrandTotal()),
                'label' => $this->__('Grand Total to be Charged'),
                'is_formated' => true,
            ));
        }
        return $this;
    }

    /**
     * Add new total to totals array after specific total or before last total by default
     *
     * @param   Varien_Object $total
     * @param   null|string|last|first $after
     * @return  $this
     */
    public function addTotal(Varien_Object $total, $after=null)
    {
        if ($after !== null && $after != 'last' && $after != 'first') {
            $totals = array();
            $added = false;
            foreach ($this->_totals as $code => $item) {
                $totals[$code] = $item;
                if ($code == $after) {
                    $added = true;
                    $totals[$total->getCode()] = $total;
                }
            }
            if (!$added) {
                $last = array_pop($totals);
                $totals[$total->getCode()] = $total;
                $totals[$last->getCode()] = $last;
            }
            $this->_totals = $totals;
        } elseif ($after=='last')  {
            $this->_totals[$total->getCode()] = $total;
        } elseif ($after=='first')  {
            $totals = array($total->getCode()=>$total);
            $this->_totals = array_merge($totals, $this->_totals);
        } else {
            $last = array_pop($this->_totals);
            $this->_totals[$total->getCode()] = $total;
            $this->_totals[$last->getCode()] = $last;
        }
        return $this;
    }

    /**
     * Add new total to totals array before specific total or after first total by default
     *
     * @param   Varien_Object $total
     * @param   null|string $after
     * @return  $this
     */
    public function addTotalBefore(Varien_Object $total, $before=null)
    {
        if ($before !== null) {
            if (!is_array($before)) {
                $before = array($before);
            }
            foreach ($before as $beforeTotals) {
                if (isset($this->_totals[$beforeTotals])) {
                    $totals = array();
                    foreach ($this->_totals as $code => $item) {
                        if ($code == $beforeTotals) {
                            $totals[$total->getCode()] = $total;
                        }
                        $totals[$code] = $item;
                    }
                    $this->_totals = $totals;
                    return $this;
                }
            }
        }
        $totals = array();
        $first = array_shift($this->_totals);
        $totals[$first->getCode()] = $first;
        $totals[$total->getCode()] = $total;
        foreach ($this->_totals as $code => $item) {
            $totals[$code] = $item;
        }
        $this->_totals = $totals;
        return $this;
    }

    /**
     * Get Total object by code
     *
     * @@return Varien_Object
     */
    public function getTotal($code)
    {
        if (isset($this->_totals[$code])) {
            return $this->_totals[$code];
        }
        return false;
    }

    /**
     * Delete total by specific
     *
     * @param   string $code
     * @return  $this
     */
    public function removeTotal($code)
    {
        unset($this->_totals[$code]);
        return $this;
    }

    /**
     * Apply sort receivables to totals array.
     * Array should have next structure
     * array(
     *  $totalCode => $totalSortReceivable
     * )
     *
     *
     * @param   array $receivable
     * @return  $this
     */
    public function applySortReceivable($receivable)
    {
        return $this;
    }

    /**
     * get totals array for visualization
     *
     * @return array
     */
    public function getTotals($area=null)
    {
        $totals = array();
        if ($area === null) {
            $totals = $this->_totals;
        } else {
            $area = (string)$area;
            foreach ($this->_totals as $total) {
                $totalArea = (string) $total->getArea();
                if ($totalArea == $area) {
                    $totals[] = $total;
                }
            }
        }
        return $totals;
    }

    /**
     * Format total value based on receivable currency
     *
     * @param   Varien_Object $total
     * @return  string
     */
    public function formatValue($total)
    {
        if (!$total->getIsFormated()) {
            return $this->getReceivable()->formatPrice($total->getValue());
        }
        return $total->getValue();
    }
}
