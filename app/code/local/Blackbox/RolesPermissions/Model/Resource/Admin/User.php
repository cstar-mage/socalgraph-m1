<?php

class Blackbox_RolesPermissions_Model_Resource_Admin_User extends Mage_Admin_Model_Resource_User
{
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(
            array(
                'field' => 'username',
                'title' => Mage::helper('adminhtml')->__('User Name')
            ),
        );
        return $this;
    }
}