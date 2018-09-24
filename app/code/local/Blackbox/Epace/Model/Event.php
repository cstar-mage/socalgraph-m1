<?php
 
class Blackbox_Epace_Model_Event extends Mage_Core_Model_Abstract {

    const STATUS_SUCCESS = 'Success';
    const STATUS_WITH_ERRORS = 'With errors';
    const STATUS_CRITICAL = 'Critical';

    protected function _construct() {
        $this->_init('epace/event');
    }

    public function delete()
    {
        $files = Mage::getModel('epace/event_file')->getCollection()
            ->addFieldToFilter('event_id', $this->getId())
            ->addOrder('id', Varien_Data_Collection_Db::SORT_ORDER_ASC);

        foreach ($files as $file) {
            $file->delete();
        }

        return parent::delete();
    }
}
