<?php

/**
 * RolesPermissions Model Resource Denied Category_Collection
 *
 * @package     Blackbox_RolesPermissions
 */
class Blackbox_RolesPermissions_Model_Resource_Denied_Category_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Collection constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('rolespermissions/denied_category');
    }
}
