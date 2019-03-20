<?php

class Blackbox_EpaceImport_Block_Adminhtml_Sales_Order_Totals extends Mage_Adminhtml_Block_Sales_Order_Totals
{
    public function _initTotals()
    {
        parent::_initTotals();
        $order = $this->getOrder();
        if (!$order->getEpaceJobId()) {
            return $this;
        }

        $this->_totals = [
                'estimate_price' => new Varien_Object(array(
                    'code'      => 'estimate_price',
                    'strong'    => false,
                    'value'     => $order->getOriginalQuotedPrice(),
                    'base_value'=> $order->getOriginalQuotedPrice(),
                    'label'     => $this->helper('sales')->__('Estimated Price'),
                    'area'      => ''
                ))
            ] + $this->_totals;

        $this->_totals['subtotal']->setLabel($this->helper('sales')->__('Sold For'));

        $this->_totals['shipping'] = new Varien_Object(array(
            'code'      => 'shipping',
            'value'     => $this->getSource()->getShippingInclTax(),
            'base_value'=> $this->getSource()->getBaseShippingInclTax(),
            'label' => $this->helper('sales')->__('Shipping & Handling + (40%)')
        ));

        return $this;
    }
}