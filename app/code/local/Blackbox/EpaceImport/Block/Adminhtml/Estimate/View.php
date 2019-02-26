<?php
/**
 * Adminhtml epacei estimate view
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Block_Adminhtml_Estimate_View extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_blockGroup = 'epacei';
        $this->_objectId    = 'estimate_id';
        $this->_controller  = 'adminhtml_estimate';
        $this->_mode        = 'view';

        parent::__construct();

        $this->_removeButton('delete');
        $this->_removeButton('reset');
        $this->_removeButton('save');
        $this->setId('epacei_estimate_view');
        $estimate = $this->getEstimate();
        $coreHelper = Mage::helper('core');

        $this->addButton('go_to_epace', [
            'label' => Mage::helper('sales')->__('Go to Epace'),
            'onclick' => 'popWin(\'' . $this->getEpaceUrl($this->getEstimate()->getEpaceEstimateId()) . '\', \'_blank\', null)'
        ]);
    }

    /**
     * Retrieve estimate model object
     *
     * @return Blackbox_EpaceImport_Model_Estimate
     */
    public function getEstimate()
    {
        return Mage::registry('epacei_estimate');
    }

    /**
     * Retrieve Estimate Identifier
     *
     * @return int
     */
    public function getEstimateId()
    {
        return $this->getEstimate()->getId();
    }

    public function getHeaderText()
    {
        if ($_extEstimateId = $this->getEstimate()->getExtEstimateId()) {
            $_extEstimateId = '[' . $_extEstimateId . '] ';
        } else {
            $_extEstimateId = '';
        }
        return Mage::helper('epacei')->__('Estimate # %s %s | %s', $this->getEstimate()->getRealEstimateId(), $_extEstimateId, $this->formatDate($this->getEstimate()->getCreatedAtDate(), 'medium', true));
    }

    public function getUrl($params='', $params2=array())
    {
        $params2['estimate_id'] = $this->getEstimateId();
        return parent::getUrl($params, $params2);
    }

    public function getCancelUrl()
    {
        return $this->getUrl('*/*/cancel');
    }

    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('epacei/estimate/actions/' . $action);
    }

    /**
     * Return back url for view grid
     *
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->getEstimate()->getBackUrl()) {
            return $this->getEstimate()->getBackUrl();
        }

        return $this->getUrl('*/*/');
    }

    public function getEpaceUrl($estimateId)
    {
        /** @var Blackbox_Epace_Helper_Api $api */
        $api = Mage::helper('epace/api');
        return $api->getHost() . '/epace/company:public/object/Estimate/detail/' . $estimateId;
    }
}
