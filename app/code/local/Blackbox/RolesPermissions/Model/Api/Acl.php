<?php

class Blackbox_RolesPermissions_Model_Api_Acl extends Mage_Api_Model_Acl
{
    public function getResourceChildren($resource)
    {
        return $this->_resources[$resource]['children'];
    }
}