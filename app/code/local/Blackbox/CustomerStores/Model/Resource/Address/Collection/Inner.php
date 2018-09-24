<?php

class Blackbox_CustomerStores_Model_Resource_Address_Collection_Inner extends Mage_Customer_Model_Resource_Address_Collection
{
    protected $_storeLocatorToAddressAttributeMap = [
        'storelocator_id' => null,
        'name' => null,
        'country' => 'country_id',
        'address' => 'street',
        'zipcode' => 'postcode',
        'state_id' => 'region_id',
        'state' => 'region',
        'phone' => 'telephone'
    ];

    protected function _construct()
    {
        $this->_init('customer/address');
    }

    public function init($customer)
    {
        //$addresses = Mage::getResourceModel('customer/address_collection');
        if ($customer->getId()) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $this->addAttributeToSelect('*')
                ->addAttributeToFilter('parent_id', $customer->getId());
            if ($customer->getVisibleStores()) {
                $stores = Mage::getResourceModel('storelocator/storelocator_collection');
                $stores->addFieldToFilter('storelocator_id', array('in' => explode(',', $customer->getVisibleStores())));
                $aSelect = $this->getSelect();
                $sSelect = $stores->getSelect();

                $sSelect->reset(Zend_Db_Select::COLUMNS);

                $attributes = Mage::getResourceModel('customer/address_attribute_collection');
                /** @var Mage_Customer_Model_Resource_Address $addressResource */
                $addressResource = $this->getResource();
                /** @var Magestore_Storelocator_Model_Mysql4_Storelocator $storelocatorResource */
                $storelocatorResource = Mage::getResourceModel('storelocator/storelocator');
                $aDescribe = $addressResource->getReadConnection()->describeTable($addressResource->getEntityTable());
                $sDescribe = $storelocatorResource->getReadConnection()->describeTable($storelocatorResource->getMainTable());
                $columns = [];

                foreach ($aDescribe as $aCol => $aDef) {
                    $found = false;
                    foreach ($sDescribe as $col => $def) {
                        $alias = $this->_getStorelocatorFieldAlias($col);
                        if ($alias == $aCol) {
                            if ($alias == $col) {
                                $columns[] = $aCol;
                            } else {
                                $columns[$alias] = $col;
                            }
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        $columns[] = new Zend_Db_Expr("NULL as `$aCol`");
                    }
                }

                foreach ($attributes as $attribute)
                {
                    $found = false;
                    foreach ($sDescribe as $col => $def) {
                        if ($col == $attribute->getAttributeCode()) {
                            $columns[] = $attribute->getAttributeCode();
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        $columns[] = new Zend_Db_Expr("NULL as `{$attribute->getAttributeCode()}`");
                    }
                }

                foreach ($this->_storeLocatorToAddressAttributeMap as $sAttr => $aAttr) {
                    if (!$aAttr) {
                        $found = false;

                        foreach ($attributes as $attribute) {
                            if ($attribute->getAttributeCode() == $sAttr) {
                                $found = true;
                                break;
                            }
                        }

                        if ($found) {
                            continue;
                        }

                        foreach ($aDef as $aCol => $aDef) {
                            if ($aCol == $sAttr) {
                                $found = true;
                                break;
                            }
                        }

                        if ($found) {
                            continue;
                        }

                        $columns[] = $sAttr;
                        $aSelect->columns([$sAttr => new Zend_Db_Expr('NULL')]);
                    }
                }

                $sSelect->columns($columns);

                $this->_select = $this->getResource()->getReadConnection()->select()->union(array($aSelect, $sSelect));

                $addresses->getSelect()->union(array($sSelect));
                $qwe = (string)$aSelect;
            }
        } else {
            $this->getSelect()->where('false');
        }
    }

    public function extractSelect($customer)
    {
        //$addresses = Mage::getResourceModel('customer/address_collection');
        if ($customer->getId()) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $this->addAttributeToSelect('*')
                ->addAttributeToFilter('parent_id', $customer->getId());
            if ($customer->getVisibleStores()) {
                $stores = Mage::getResourceModel('storelocator/storelocator_collection');
                $stores->addFieldToFilter('storelocator_id', array('in' => explode(',', $customer->getVisibleStores())));
                $aSelect = $this->getSelect();
                $sSelect = $stores->getSelect();

                $sSelect->reset(Zend_Db_Select::COLUMNS);

                $attributes = Mage::getResourceModel('customer/address_attribute_collection');
                /** @var Mage_Customer_Model_Resource_Address $addressResource */
                $addressResource = $this->getResource();
                /** @var Magestore_Storelocator_Model_Mysql4_Storelocator $storelocatorResource */
                $storelocatorResource = Mage::getResourceModel('storelocator/storelocator');
                $aDescribe = $addressResource->getReadConnection()->describeTable($addressResource->getEntityTable());
                $sDescribe = $storelocatorResource->getReadConnection()->describeTable($storelocatorResource->getMainTable());
                $columns = [];

                foreach ($aDescribe as $aCol => $aDef) {
                    $found = false;
                    foreach ($sDescribe as $col => $def) {
                        $alias = $this->_getStorelocatorFieldAlias($col);
                        if ($alias == $aCol) {
                            if ($alias == $col) {
                                $columns[] = $aCol;
                            } else {
                                $columns[$alias] = $col;
                            }
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        $columns[] = new Zend_Db_Expr("NULL as `$aCol`");
                    }
                }

                foreach ($attributes as $attribute)
                {
                    $this->joinAttribute($attribute->getAttributeCode(), Mage::getSingleton("eav/config")->getAttribute($attribute->getEntityType(), $attribute->getAttributeCode()), 'entity_id', null, 'left');
                    $found = false;
                    foreach ($sDescribe as $col => $def) {
                        $alias = $this->_getStorelocatorFieldAlias($col);
                        if ($alias == $attribute->getAttributeCode()) {
                            if ($alias == $col) {
                                $columns[] = $attribute->getAttributeCode();
                            } else {
                                $columns[$alias] = $col;
                            }
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        $columns[] = new Zend_Db_Expr("NULL as `{$attribute->getAttributeCode()}`");
                    }
                }

                foreach ($this->_storeLocatorToAddressAttributeMap as $sAttr => $aAttr) {
                    if (!$aAttr) {
                        $found = false;

                        foreach ($attributes as $attribute) {
                            if ($attribute->getAttributeCode() == $sAttr) {
                                $found = true;
                                break;
                            }
                        }

                        if ($found) {
                            continue;
                        }

                        foreach ($aDef as $aCol => $aDef) {
                            if ($aCol == $sAttr) {
                                $found = true;
                                break;
                            }
                        }

                        if ($found) {
                            continue;
                        }

                        $columns[] = $sAttr;
                        $aSelect->columns([$sAttr => new Zend_Db_Expr('NULL')]);
                    }
                }

                $sSelect->columns($columns);

                $this->_renderFilters()
                    ->_renderOrders()
                    ->_renderLimit();
                $preparedSelect = $this->_prepareSelect($this->getSelect());

                return $this->getResource()->getReadConnection()->select()->union([ $preparedSelect, $sSelect ]);
            }
        } else {
            $this->getSelect()->where('false');
        }

        $this->_renderFilters()
            ->_renderOrders()
            ->_renderLimit();
        return $this->getSelect();
    }

    protected function _getStorelocatorFieldAlias($sField)
    {
        return $this->_storeLocatorToAddressAttributeMap[$sField] ?: $sField;
    }
}