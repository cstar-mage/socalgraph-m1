<?php

class Blackbox_CinemaCloud_Block_Customer_Address_Grid extends Mage_Core_Block_Template
{
    protected $_searchVarName = 'q';
    protected $_varPrefix = '';

    protected $_searchAttributes = array(
        'firstname',
        'lastname',
        'street',
        'city',
        'postcode',
        'region'
    );

    public function formatItem($item)
    {
        return Mage::helper('customer_stores')->formatAddressItem($item);
    }

    protected function _beforeToHtml()
    {
        $this->_prepareItems();
        parent::_beforeToHtml();

        $this->_applyFilter();
        $pager = $this->getChild('pager');
        if (!$pager instanceof Mage_Page_Block_Html_Pager) {
            /** @var Mage_Page_Block_Html_Pager $pager */
            $pager = $this->getLayout()->createBlock('page/html_pager');
            $this->setChild('pager', $pager);
        }
        if ($this->getVarPrefix()) {
            $pager->setPageVarName($this->getVarPrefix() . $pager->getPageVarName());
            $pager->setLimitVarName($this->getVarPrefix() . $pager->getLimitVarName());
        }
        $pager->setCollection($this->getItems());
        $this->getItems()->load();
        return $this;
    }

    protected function _prepareItems()
    {
        /** @var Mage_Customer_Model_Session $session */
        $session = Mage::getSingleton('customer/session');
        /** @var Mage_Customer_Model_Resource_Address_Collection $addresses */
        $addresses = Mage::getResourceModel('customer/address_collection');
        $addresses->addAttributeToSelect('*');

        $customer = $session->getCustomer();
        $addresses->setCustomerFilter($customer);
        if (!empty($primaryAdressIds = $customer->getPrimaryAddressIds())) {
            $addresses->addFieldToFilter('entity_id', ['nin' => $primaryAdressIds]);
        }

        $this->setItems($addresses);
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getSearchUrl()
    {
        $url = $this->getData('url');
        if (!$url) {
            $url = '*/*/*';
        }
        return $this->getUrl($url);
    }

    public function setVarPrefix($prefix)
    {
        $this->_varPrefix = $prefix;

        return $this;
    }

    public function getVarPrefix()
    {
        return $this->_varPrefix;
    }

    public function getSearchValue()
    {
        return $this->getRequest()->getParam($this->getSearchVarName());
    }

    public function getSearchVarName()
    {
        return $this->_varPrefix . $this->_searchVarName;
    }

    public function getSearchAttributes()
    {
        return $this->_searchAttributes;
    }

    protected function _applyFilter()
    {
        $search = $this->getSearchValue();
        if ($search) {
            $conditions = array();
            foreach ($this->getSearchAttributes() as $attribute) {
                $conditions[] = ['attribute' => $attribute, 'like' => '%%' . $search . '%%'];
            }

            $this->getItems()->addAttributeToFilter($conditions);
        }
    }

    public function getDeleteUrl()
    {
        return $this->getUrl('customer/address/delete',
            array(Mage_Core_Model_Url::FORM_KEY => Mage::getSingleton('core/session')->getFormKey()));
    }
}