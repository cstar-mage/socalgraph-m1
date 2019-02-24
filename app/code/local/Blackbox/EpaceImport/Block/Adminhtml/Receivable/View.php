<?php
/**
 * Adminhtml epacei receivable view
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Block_Adminhtml_Receivable_View extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_blockGroup = 'epacei';
        $this->_objectId    = 'receivable_id';
        $this->_controller  = 'adminhtml_receivable';
        $this->_mode        = 'view';

        parent::__construct();

        $this->_removeButton('delete');
        $this->_removeButton('reset');
        $this->_removeButton('save');
        $this->setId('epacei_receivable_view');
        $receivable = $this->getReceivable();
        $coreHelper = Mage::helper('core');
    }

    /**
     * Retrieve receivable model object
     *
     * @return Blackbox_EpaceImport_Model_Receivable
     */
    public function getReceivable()
    {
        return Mage::registry('epacei_receivable');
    }

    /**
     * Retrieve Receivable Identifier
     *
     * @return int
     */
    public function getReceivableId()
    {
        return $this->getReceivable()->getId();
    }

    public function getHeaderText()
    {
        if ($_extReceivableId = $this->getReceivable()->getExtReceivableId()) {
            $_extReceivableId = '[' . $_extReceivableId . '] ';
        } else {
            $_extReceivableId = '';
        }
        return Mage::helper('epacei')->__('Receivable # %s %s | %s', $this->getReceivable()->getRealReceivableId(), $_extReceivableId, $this->formatDate($this->getReceivable()->getCreatedAtDate(), 'medium', true));
    }

    public function getUrl($params='', $params2=array())
    {
        $params2['receivable_id'] = $this->getReceivableId();
        return parent::getUrl($params, $params2);
    }

    public function getCancelUrl()
    {
        return $this->getUrl('*/*/cancel');
    }

    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('epacei/receivable/actions/' . $action);
    }

    /**
     * Return back url for view grid
     *
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->getReceivable()->getBackUrl()) {
            return $this->getReceivable()->getBackUrl();
        }

        return $this->getUrl('*/*/');
    }
}
