<?php

/**
 * description
 *
 * @package    Blackbox_OrderApproval
 */
class Blackbox_OrderApproval_Block_Rule_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('promo_catalog_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('order_approval')->__('Order Approval Rule'));
    }
}
