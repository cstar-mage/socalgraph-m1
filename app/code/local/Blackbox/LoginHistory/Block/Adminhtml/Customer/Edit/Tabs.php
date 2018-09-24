<?php

class Blackbox_LoginHistory_Block_Adminhtml_Customer_Edit_Tabs extends Mage_Adminhtml_Block_Customer_Edit_Tabs
{
    protected function _beforeToHtml()
    {
        /*
                if (Mage::registry('current_customer')->getId()) {
                    $this->addTab('view', array(
                        'label'     => Mage::helper('customer')->__('Customer View'),
                        'content'   => $this->getLayout()->createBlock('adminhtml/customer_edit_tab_view')->toHtml(),
                        'active'    => true
                    ));
                }
        */
        $this->addTab('account', array(
            'label'     => Mage::helper('customer')->__('Account Information'),
            'content'   => $this->getLayout()->createBlock('adminhtml/customer_edit_tab_account')->initForm()->toHtml(),
            'active'    => Mage::registry('current_customer')->getId() ? false : true
        ));

        $this->addTab('addresses', array(
            'label'     => Mage::helper('customer')->__('Addresses'),
            'content'   => $this->getLayout()->createBlock('adminhtml/customer_edit_tab_addresses')->initForm()->toHtml(),
        ));


        // load: Orders, Shopping Cart, Wishlist, Product Reviews, Product Tags - with ajax

        if (Mage::registry('current_customer')->getId()) {

            if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
                $this->addTab('orders', array(
                    'label'     => Mage::helper('customer')->__('Orders'),
                    'class'     => 'ajax',
                    'url'       => $this->getUrl('*/*/orders', array('_current' => true)),
                ));
            }

            $this->addTab('cart', array(
                'label'     => Mage::helper('customer')->__('Shopping Cart'),
                'class'     => 'ajax',
                'url'       => $this->getUrl('*/*/carts', array('_current' => true)),
            ));

            $this->addTab('wishlist', array(
                'label'     => Mage::helper('customer')->__('Wishlist'),
                'class'     => 'ajax',
                'url'       => $this->getUrl('*/*/wishlist', array('_current' => true)),
            ));

            if (Mage::getSingleton('admin/session')->isAllowed('newsletter/subscriber')) {
                $this->addTab('newsletter', array(
                    'label'     => Mage::helper('customer')->__('Newsletter'),
                    'content'   => $this->getLayout()->createBlock('adminhtml/customer_edit_tab_newsletter')->initForm()->toHtml()
                ));
            }

            if (Mage::getSingleton('admin/session')->isAllowed('catalog/reviews_ratings')) {
                $this->addTab('reviews', array(
                    'label'     => Mage::helper('customer')->__('Product Reviews'),
                    'class'     => 'ajax',
                    'url'       => $this->getUrl('*/*/productReviews', array('_current' => true)),
                ));
            }

            if (Mage::getSingleton('admin/session')->isAllowed('catalog/tag')) {
                $this->addTab('tags', array(
                    'label'     => Mage::helper('customer')->__('Product Tags'),
                    'class'     => 'ajax',
                    'url'       => $this->getUrl('*/*/productTags', array('_current' => true)),
                ));
            }

            if (Mage::getSingleton('admin/session')->isAllowed('customer/login_history')) {
                $this->addTab('login_history', array(
                    'label'     => Mage::helper('customer')->__('Login History'),
                    'class'     => 'ajax',
                    'url'       => $this->getUrl('*/*/loginHistory', array('_current' => true)),
                ));
            }
        }

        $this->_updateActiveTab();
        Varien_Profiler::stop('customer/tabs');
        return Mage_Adminhtml_Block_Widget_Tabs::_beforeToHtml();
    }
}