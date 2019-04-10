<?php
/**
 * Adminhtml epacei purchase order create
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Blackbox_EpaceImport_Block_Adminhtml_PurchaseOrder_Create extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'purchase_order_id';
        $this->_controller = 'epacei_purchaseOrdere';
        $this->_mode = 'create';

        parent::__construct();

        $this->setId('epacei_purchase_order_create');

        $customerId = $this->_getSession()->getCustomerId();
        $storeId    = $this->_getSession()->getStoreId();

        $this->_updateButton('save', 'label', Mage::helper('epacei')->__('Submit PurchaseOrder'));
        $this->_updateButton('save', 'onclick', "purchaseOrder.submit()");
        $this->_updateButton('save', 'id', 'submit_purchase_order_top_button');
        if (is_null($customerId) || !$storeId) {
            $this->_updateButton('save', 'style', 'display:none');
        }

        $this->_updateButton('back', 'id', 'back_purchase_order_top_button');
        $this->_updateButton('back', 'onclick', 'setLocation(\'' . $this->getBackUrl() . '\')');

        $this->_updateButton('reset', 'id', 'reset_purchase_order_top_button');

        if (!$this->_isCanCancel() || is_null($customerId)) {
            $this->_updateButton('reset', 'style', 'display:none');
        } else {
            $this->_updateButton('back', 'style', 'display:none');
        }

        $confirm = Mage::helper('epacei')->__('Are you sure you want to cancel this purchase order?');
        $this->_updateButton('reset', 'label', Mage::helper('epacei')->__('Cancel'));
        $this->_updateButton('reset', 'class', 'cancel');
        $this->_updateButton('reset', 'onclick', 'deleteConfirm(\''.$confirm.'\', \'' . $this->getCancelUrl() . '\')');
    }

    /**
     * Check access for cancel action
     *
     * @return boolean
     */
    protected function _isCanCancel()
    {
        return Mage::getSingleton('admin/session')->isAllowed('epacei/purchase_order/actions/cancel');
    }

    /**
     * Prepare header html
     *
     * @return string
     */
    public function getHeaderHtml()
    {
        $out = '<div id="purchase_order-header">'
            . $this->getLayout()->createBlock('adminhtml/sales_order_create_header')->toHtml()
            . '</div>';
        return $out;
    }

    /**
     * Prepare form html. Add block for configurable product modification interface
     *
     * @return string
     */
    public function getFormHtml()
    {
        $html = parent::getFormHtml();
        $html .= $this->getLayout()->createBlock('adminhtml/catalog_product_composite_configure')->toHtml();
        return $html;
    }

    public function getHeaderWidth()
    {
        return 'width: 70%;';
    }

    /**
     * Retrieve quote session object
     *
     * @return Mage_Adminhtml_Model_Session_Quote
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session_quote');
    }

    public function getCancelUrl()
    {
        if ($this->_getSession()->getPurchaseOrder()->getId()) {
            $url = $this->getUrl('*/epacei_purchaseOrder/view', array(
                'purchase_order_id' => Mage::getSingleton('adminhtml/session_quote')->getPurchaseOrder()->getId()
            ));
        } else {
            $url = $this->getUrl('*/*/cancel');
        }

        return $url;
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/' . $this->_controller . '/');
    }
}
