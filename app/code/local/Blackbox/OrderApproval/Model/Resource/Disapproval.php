<?php

/**
 * Flat sales order disapproval resource
 *
 * @category    Blackbox
 * @package     Blackbox_OrderApproval
 */
class Blackbox_OrderApproval_Model_Resource_Disapproval extends Mage_Sales_Model_Resource_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix                  = 'order_disapproval_resource';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('order_approval/disapproval', 'entity_id');
    }

    /**
     * @param Mage_Core_Model_Abstract $object
     * @param int $value
     * @return Blackbox_OrderApproval_Model_Resource_Disapproval
     */
    public function saveEmailSent(Mage_Core_Model_Abstract $object, $value)
    {
        if (!$object->getId()) {
            return $this;
        }
        $this->_getWriteAdapter()->update($this->getMainTable(), array('email_sent' => (int)$value), 'entity_id=' . $object->getId());
        return $this;
    }
}
