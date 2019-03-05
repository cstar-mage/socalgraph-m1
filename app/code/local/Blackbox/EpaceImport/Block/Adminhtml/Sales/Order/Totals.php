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

        if ($order->getJobValue()) {
            $this->_totals = [
                    'job_value' => new Varien_Object(array(
                        'code'      => 'job_value',
                        'strong'    => false,
                        'value'     => $order->getJobValue(),
                        'base_value'=> $order->getJobValue(),
                        'label'     => $this->helper('sales')->__('Estimated Price'),
                        'area'      => ''
                    ))
            ] + $this->_totals;
        }
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