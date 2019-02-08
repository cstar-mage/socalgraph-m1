<?php
/**
 * Adminhtml epacei estimate's status namagement block
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Block_Adminhtml_Estimate_Status extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_controller = 'epacei_estimate_status';
        $this->_headerText = Mage::helper('epacei')->__('Estimate Statuses');
        $this->_addButtonLabel = Mage::helper('epacei')->__('Create New Status');
        $this->_addButton('assign', array(
            'label'     => Mage::helper('epacei')->__('Assign Status to State'),
            'onclick'   => 'setLocation(\'' . $this->getAssignUrl() .'\')',
            'class'     => 'add',
        ));
        parent::__construct();
    }

    /**
     * Create url getter
     *
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/epacei_estimate_status/new');
    }

    /**
     * Assign url getter
     *
     * @return string
     */
    public function getAssignUrl()
    {
        return $this->getUrl('*/epacei_estimate_status/assign');
    }
}
