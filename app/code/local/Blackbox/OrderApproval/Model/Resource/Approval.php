<?php

/**
 * Flat sales order approval resource
 *
 * @category    Blackbox
 * @package     Blackbox_OrderApproval
 */
class Blackbox_OrderApproval_Model_Resource_Approval extends Mage_Sales_Model_Resource_Order_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix                  = 'order_approval_resource';

    /**
     * Is grid available
     *
     * @var bool
     */
    protected $_grid                         = true;

    /**
     * Flag for using of increment id
     *
     * @var bool
     */
    protected $_useIncrementId               = true;

    /**
     * Entity code for increment id (Eav entity code)
     *
     * @var string
     */
    protected $_entityTypeForIncrementId     = 'approval';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('order_approval/approval', 'entity_id');
    }

    /**
     * Init virtual grid records for entity
     *
     * @return Blackbox_OrderApproval_Model_Resource_Approval
     */
    protected function _initVirtualGridColumns()
    {
        parent::_initVirtualGridColumns();
        $adapter           = $this->_getReadAdapter();
        $checkedFirstname  = $adapter->getIfNullSql('{{table}}.firstname', $adapter->quote(''));
        $checkedMiddlename = $adapter->getIfNullSql('{{table}}.middlename', $adapter->quote(''));
        $checkedLastname   = $adapter->getIfNullSql('{{table}}.lastname', $adapter->quote(''));

        $firstnameAttributeId = Mage::getResourceModel('eav/entity_attribute')
            ->getIdByCode('customer', 'firstname');
        $middlenameAttributeId = Mage::getResourceModel('eav/entity_attribute')
            ->getIdByCode('customer', 'middlename');
        $lastnameAttributeId = Mage::getResourceModel('eav/entity_attribute')
            ->getIdByCode('customer', 'lastname');

        $customerTable = Mage::getSingleton('core/resource')->getTableName('customer/entity');

        $select = "(SELECT customer.entity_id as entity_id, firstname.value as firstname, middlename.value as middlename, lastname.value as lastname FROM {$customerTable} as customer
LEFT JOIN {$customerTable}_varchar as firstname ON firstname.attribute_id = $firstnameAttributeId AND firstname.entity_id = customer.entity_id
LEFT JOIN {$customerTable}_varchar as middlename ON middlename.attribute_id = $middlenameAttributeId AND middlename.entity_id = customer.entity_id
LEFT JOIN {$customerTable}_varchar as lastname ON lastname.attribute_id = $lastnameAttributeId AND lastname.entity_id = customer.entity_id)";

        $this->addVirtualGridColumn(
            'user_name',
            new Zend_Db_Expr($select),
            array('user_id' => 'entity_id'),
            $adapter->getConcatSql(array(
                $checkedFirstname,
                $adapter->quote(' '),
                $checkedMiddlename,
                $adapter->quote(' '),
                $checkedLastname
            ))
        )
        ->addVirtualGridColumn(
            'order_increment_id',
            'sales/order',
            array('order_id' => 'entity_id'),
            'increment_id'
        )
        ->addVirtualGridColumn(
            'order_created_at',
            'sales/order',
            array('order_id' => 'entity_id'),
            'created_at'
        );

        return $this;
    }

    /**
     * Add new virtual grid column
     *
     * @param string $alias
     * @param string $table
     * @param array $joinCondition
     * @param string $column
     * @return Mage_Sales_Model_Resource_Order_Abstract
     */
    public function addVirtualGridColumn($alias, $table, $joinCondition, $column)
    {
        if (!($table instanceof Zend_Db_Expr)) {
            $table = $this->getTable($table);
        }

        if (!in_array($alias, $this->getGridColumns())) {
            Mage::throwException(
                Mage::helper('sales')->__('Please specify a valid grid column alias name that exists in grid table.')
            );
        }

        $this->_virtualGridColumns[$alias] = array(
            $table, $joinCondition, $column
        );

        return $this;
    }
}
