<?php

/**
 * RolesPermissions Denied Category Model Resource
 *
 * @package     Blackbox_RolesPermissions
 */
class Blackbox_RolesPermissions_Model_Resource_Denied_Category extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * constructor
     *
     */
    protected function _construct()
    {
        $this->_init('rolespermissions/denied_category', 'id');
    }

    /**
     * @param Blackbox_RolesPermissions_Model_Denied_Category $deniedCategory
     * @param int $customerId
     * @param int $ruleId
     * @return Blackbox_RolesPermissions_Model_Resource_Denied_Category
     */
    public function loadByCustomerAndCategory($deniedCategory, $customerId, $categoryId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()->from($this->getMainTable())
            ->where('customer_id = :customer_id')
            ->where('category_id = :category_id');
        $data = $read->fetchRow($select, array(':category_id' => $categoryId, ':customer_id' => $customerId));
        if (false === $data) {
            // set empty data, as an existing rule object might be used
            $data = array();
        }
        $deniedCategory->setData($data);
        return $this;
    }

    public function truncate() {
        $this->_getWriteAdapter()->query('TRUNCATE TABLE '.$this->getMainTable());
        return $this;
    }
}
