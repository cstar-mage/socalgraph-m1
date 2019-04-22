<?php

class Blackbox_CinemaCloud_Customer_JobsController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        /** @var Blackbox_CinemaCloud_Helper_Data $helper */
        $helper = Mage::helper('cinemacloud');
        if ($helper->isCustomerSalesRep()) {
            $route = 'customer/salesRep';
        } else if ($helper->isCustomerCSR()) {
            $route = 'customer/csr';
        } else {
            $route = 'customer/account';
        }

        $this->_redirect($route);
    }
}