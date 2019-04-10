<?php
/**
 * Adminhtml epacei purchase order view
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Block_Adminhtml_PurchaseOrder_View extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_blockGroup = 'epacei';
        $this->_objectId    = 'purchase_order_id';
        $this->_controller  = 'adminhtml_purchaseOrder';
        $this->_mode        = 'view';

        parent::__construct();

        $this->_removeButton('delete');
        $this->_removeButton('reset');
        $this->_removeButton('save');
        $this->setId('epacei_purchase_order_view');

        $this->addButton('go_to_epace', [
            'label' => Mage::helper('sales')->__('Go to Epace'),
            'onclick' => 'popWin(\'' . $this->getEpaceUrl($this->getPurchaseOrder()->getEpacePurchaseOrderId()) . '\', \'_blank\', null)'
        ]);
    }

    /**
     * Retrieve purchase order model object
     *
     * @return Blackbox_EpaceImport_Model_PurchaseOrder
     */
    public function getPurchaseOrder()
    {
        return Mage::registry('purchase_order');
    }

    /**
     * Retrieve PurchaseOrder Identifier
     *
     * @return int
     */
    public function getPurchaseOrderId()
    {
        return $this->getPurchaseOrder()->getId();
    }

    public function getHeaderText()
    {
        if ($_extPurchaseOrderId = $this->getPurchaseOrder()->getExtPurchaseOrderId()) {
            $_extPurchaseOrderId = '[' . $_extPurchaseOrderId . '] ';
        } else {
            $_extPurchaseOrderId = '';
        }
        return Mage::helper('epacei')->__('Purchase Order # %s %s | %s', $this->getPurchaseOrder()->getRealPurchaseOrderId(), $_extPurchaseOrderId, $this->formatDate($this->getPurchaseOrder()->getCreatedAtDate(), 'medium', true));
    }

    public function getUrl($params='', $params2=array())
    {
        $params2['purchase_order_id'] = $this->getPurchaseOrderId();
        return parent::getUrl($params, $params2);
    }

    public function getCancelUrl()
    {
        return $this->getUrl('*/*/cancel');
    }

    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('epacei/purchase_order/actions/' . $action);
    }

    /**
     * Return back url for view grid
     *
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->getPurchaseOrder()->getBackUrl()) {
            return $this->getPurchaseOrder()->getBackUrl();
        }

        return $this->getUrl('*/*/');
    }

    public function getEpaceUrl($purchaseOrderId)
    {
        /** @var Blackbox_Epace_Helper_Api $api */
        $api = Mage::helper('epace/api');
        return $api->getHost() . '/epace/company:public/object/PurchaseOrder/detail/' . $purchaseOrderId;
    }
}
