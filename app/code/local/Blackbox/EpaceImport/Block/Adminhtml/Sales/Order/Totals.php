<?php

class Blackbox_EpaceImport_Block_Adminhtml_Sales_Order_Totals extends Mage_Adminhtml_Block_Sales_Order_Totals
{
    public function _initTotals()
    {
        parent::_initTotals();
        $order = $this->getOrder();
        if ($order->getMarkup()) {
            $this->_totals = [
                'markup' => new Varien_Object(array(
                    'code'      => 'markup',
                    'strong'    => false,
                    'value'     => $order->getMarkup(),
                    'base_value'=> $order->getBaseMarkup(),
                    'label'     => $this->helper('sales')->__('Markup'),
                    'area'      => ''
                ))
            ] + $this->_totals;
        }
        if ($order->getJobValue()) {
            $this->_totals = [
                    'job_value' => new Varien_Object(array(
                        'code'      => 'job_value',
                        'strong'    => false,
                        'value'     => $order->getJobValue(),
                        'base_value'=> $order->getJobValue(),
                        'label'     => $this->helper('sales')->__('Job Value'),
                        'area'      => ''
                    ))
            ] + $this->_totals;
        }

        return $this;
    }
}