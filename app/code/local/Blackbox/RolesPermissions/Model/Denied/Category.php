<?php

/**
 * RolesPermissions Rule Category Model
 *
 * @method Blackbox_RolesPermissions_Model_Resource_Denied_Category _getResource()
 * @method Blackbox_RolesPermissions_Model_Resource_Denied_Category getResource()
 * @method int getCategoryId()
 * @method Blackbox_RolesPermissions_Model_Denied_Category setCategoryId(int $value)
 * @method int getCustomerId()
 * @method Blackbox_RolesPermissions_Model_Denied_Category setCustomerId(int $value)
 *
 * @package     Blackbox_RolesPermissions
 */
class Blackbox_RolesPermissions_Model_Denied_Category extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('rolespermissions/denied_category');
    }
}
