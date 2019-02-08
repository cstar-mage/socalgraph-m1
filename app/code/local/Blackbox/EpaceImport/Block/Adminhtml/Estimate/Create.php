<?php
/**
 * Adminhtml epacei estimate create
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Blackbox_EpaceImport_Block_Adminhtml_Estimate_Create extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'estimate_id';
        $this->_controller = 'epacei_estimate';
        $this->_mode = 'create';

        parent::__construct();

        $this->setId('epacei_estimate_create');

        $customerId = $this->_getSession()->getCustomerId();
        $storeId    = $this->_getSession()->getStoreId();

        $this->_updateButton('save', 'label', Mage::helper('epacei')->__('Submit Estimate'));
        $this->_updateButton('save', 'onclick', "estimate.submit()");
        $this->_updateButton('save', 'id', 'submit_estimate_top_button');
        if (is_null($customerId) || !$storeId) {
            $this->_updateButton('save', 'style', 'display:none');
        }

        $this->_updateButton('back', 'id', 'back_estimate_top_button');
        $this->_updateButton('back', 'onclick', 'setLocation(\'' . $this->getBackUrl() . '\')');

        $this->_updateButton('reset', 'id', 'reset_estimate_top_button');

        if (!$this->_isCanCancel() || is_null($customerId)) {
            $this->_updateButton('reset', 'style', 'display:none');
        } else {
            $this->_updateButton('back', 'style', 'display:none');
        }

        $confirm = Mage::helper('epacei')->__('Are you sure you want to cancel this estimate?');
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
        return Mage::getSingleton('admin/session')->isAllowed('epacei/estimate/actions/cancel');
    }

    /**
     * Prepare header html
     *
     * @return string
     */
    public function getHeaderHtml()
    {
        $out = '<div id="estimate-header">'
            . $this->getLayout()->createBlock('adminhtml/epacei_estimate_create_header')->toHtml()
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
        if ($this->_getSession()->getEstimate()->getId()) {
            $url = $this->getUrl('*/epacei_estimate/view', array(
                'estimate_id' => Mage::getSingleton('adminhtml/session_quote')->getEstimate()->getId()
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
