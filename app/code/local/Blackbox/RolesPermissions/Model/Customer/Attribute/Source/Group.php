<?php

/**
 * Customer group attribute source
 */
class Blackbox_RolesPermissions_Model_Customer_Attribute_Source_Group extends Mage_Customer_Model_Customer_Attribute_Source_Group
{
    protected $_allOptions = null;
    protected $_includeNotLoggedGroup = false;

    public function setIncludeNotLoggedGroup($include)
    {
        $this->_includeNotLoggedGroup = $include;
        return $this;
    }

    public function getAllOptions()
    {
        if (!$this->_includeNotLoggedGroup) {
            if (!$this->_options) {
                $this->_options = Mage::getResourceModel('customer/group_collection')
                    ->setRealGroupsFilter()
                    ->load()
                    ->toOptionArray()
                ;
            }
            return $this->_options;
        } else {
            if (!$this->_allOptions) {
                $this->_allOptions = Mage::getResourceModel('customer/group_collection')
                    ->load()
                    ->toOptionArray()
                ;
            }
            return $this->_allOptions;
        }
    }
}