<?php

class Blackbox_RolesPermissions_Model_Admin_Acl extends Mage_Admin_Model_Acl
{
    public function getResourceChildren($resource)
    {
        return $this->_resources[$resource]['children'];
    }

    /**
     * @param string $resource
     * @return Mage_Admin_Model_Acl_Resource
     */
    public function getResourceParent($resource)
    {
        return $this->_resources[$resource]['parent'];
    }
}