<?php

class Blackbox_Checkout_Block_Adminhtml_Sales_Order_View extends Cmsmart_AdminTheme_Block_Adminhtml_Block_Sales_Order_View//Mage_Adminhtml_Block_Sales_Order_View
{
    public function __construct()
    {
        parent::__construct();
        $order = $this->getOrder();

        if ($order->getStatus() == Blackbox_Checkout_Helper_Preorder::STATUS_PREORDER && $order->getState() == Blackbox_Checkout_Helper_Preorder::STATE_PREORDER) {
            if ($this->_isAllowedAction('process_preorder')) {
                $_label = Mage::helper('sales')->__('Process Preorder');
                $this->_addButton('process_preorder', array(
                    'label' => $_label,
                    'onclick' => 'setLocation(\'' . $this->getProcessPreorderUrl() . '\')',
                    'class' => 'go'
                ));
            }
        }
    }

    public function getProcessPreorderUrl()
    {
        return $this->getUrl('adminhtml/preorder/process');
    }
}