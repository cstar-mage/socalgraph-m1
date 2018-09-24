<?php

/**
 * Order approval grid collection
 *
 * @category    Blackbox
 * @package     Blackbox_OrderApproval
 */
class Blackbox_OrderApproval_Model_Resource_Approval_Grid_Collection extends Blackbox_OrderApproval_Model_Resource_Approval_Collection
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'order_approval_grid_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject    = 'order_approval_grid_collection';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setMainTable('order_approval/approval_grid');
    }
}
