<?php

class Blackbox_Checkout_Block_Address_Grid extends Mage_Core_Block_Template
{
    protected $_searchVarName = 'q';

    protected $_searchAttributes = array(
        'firstname',
        'lastname',
        'name',
        'street',
        'city',
        'postcode',
        'region'
    );

    public function _construct()
    {
        $this->setTemplate('blackbox/addressbook/address/grid.phtml');
        $this->setUrl('checkout/onepage/searchAddressGrid');
    }

    public function formatItem($item)
    {
        return Mage::helper('customer_stores')->formatAddressItem($item);
    }

    protected function _prepareLayout()
    {
        $this->_prepareItems();
        parent::_prepareLayout();

        if (!$this->getName()) {
            $this->setName($this->_getName());
        }

        $this->_applyFilter();
        $pager = $this->getLayout()->createBlock('blackbox_checkout/address_grid_pager', 'checkout.address.grid.pager')
            ->setCollection($this->getItems());
        $this->setChild('pager', $pager);
        $this->getItems()->load();
        return $this;
    }

    protected function _beforeToHtml()
    {
        $this->getChild('pager')->setUrl($this->getData('url'));
        return parent::_beforeToHtml();
    }

    protected function _prepareItems()
    {
        $session = Mage::getSingleton('customer/session');
        /** @var Blackbox_CustomerStores_Model_Resource_Address_Collection $addresses */
        $addresses = Mage::getResourceModel('customer_stores/address_collection');
        $addresses->init($session->getCustomer());

        $this->setItems($addresses);
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getInputPlaceholderText()
    {
        return $this->__('Search by Location, Account Number, Address');
    }

    public function getSearchUrl()
    {
        return $this->getUrl($this->getData('url'), array(
            'name' => $this->getName()
        ));
    }

    public function getSearchValue()
    {
        return $this->getRequest()->getParam($this->getSearchVarName());
    }

    public function getSearchVarName()
    {
        return $this->_searchVarName;
    }

    public function getSearchAttributes()
    {
        return $this->_searchAttributes;
    }

    /**
     * @param Magestore_Storelocator_Model_Storelocator $address
     * @return array
     */
    public function getAddressData($address)
    {
        return $address->getData();
    }

    protected function _applyFilter()
    {
        $search = $this->getSearchValue();
        if ($search) {
            $fields = array();
            $conditions = array();
            foreach ($this->getSearchAttributes() as $attribute) {
                $fields[] = $attribute;
                $conditions[] = array('like' => '%%' . $search . '%%');
            }

            $this->getItems()->addFieldToFilter($fields, $conditions);
        }
    }


    protected function _getName()
    {
        return $this->getRequest()->getParam('name', 'address-select-radio');
    }
}