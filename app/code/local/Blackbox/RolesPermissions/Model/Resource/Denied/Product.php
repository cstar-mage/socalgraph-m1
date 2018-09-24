<?php

/**
 * RolesPermissions Model Resource Denied_Product
 *
 * @package     Blackbox_RolesPermissions
 */
class Blackbox_RolesPermissions_Model_Resource_Denied_Product extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Constructor
     *
     */
    protected function _construct()
    {
        $this->_init('rolespermissions/denied_product', 'id');
    }



    /**
     * @param Blackbox_RolesPermissions_Model_Denied_Category $deniedProduct
     * @param int $customerId
     * @param int $ruleId
     * @param int $productId
     * @return Blackbox_RolesPermissions_Model_Resource_Denied_Product
     */
    public function loadByCustomerAndProduct($deniedProduct, $customerId, $productId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()->from($this->getMainTable())
            ->where('customer_id = :customer_id')
            ->where('product_id = :product_id');
        $data = $read->fetchRow($select, array(':product_id' => $productId, ':customer_id' => $customerId));
        if (false === $data) {
            // set empty data, as an existing rule object might be used
            $data = array();
        }
        $deniedProduct->setData($data);
        return $this;
    }

    public function truncate()
    {
        $this->_getWriteAdapter()->query('TRUNCATE TABLE ' . $this->getMainTable());
        return $this;
    }
}
