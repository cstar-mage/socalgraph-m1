<?php

class Blackbox_Checkout_Block_Address_Grid_Pager extends Mage_Page_Block_Html_Pager
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('blackbox/addressbook/address/grid/pager.phtml');
        $this->setUrl('checkout/onepage/searchAddressGrid');
    }

    public function getPagerUrl($params=array())
    {
        $urlParams = array();
        $urlParams['_current']  = true;
        $urlParams['_escape']   = true;
        $urlParams['_query']    = $params;
        $urlParams['name']  = $this->getParentBlock()->getName();
        return $this->getUrl($this->getData('url'), $urlParams);
    }
}