<?php
require_once(Mage::getModuleDir('controllers','Mage_Shipping') . DS . 'TrackingController.php');

class Blackbox_TenderGreens_Shipping_TrackingController extends Mage_Shipping_TrackingController
{
    public function progressAction()
    {
        if ($order = $this->_initOrderByIncrementId()) {
            $tracks = $order->getTracksCollection();

            $block = $this->getLayout()->createBlock('tendergreens/tracking_progress');
            $block->setOrder($order);

            foreach ($tracks as $track){
                $trackingInfo = $track->getNumberDetail();
                $block->setTrackingInfo($trackingInfo);
                break;
            }
            $response = $block->toHtml();

            $this->getResponse()->setBody($response);
        }
    }

    /**
     * Initialize order model instance
     *
     * @return Mage_Sales_Model_Order || false
     */
    protected function _initOrderByIncrementId()
    {
        $id = trim($this->getRequest()->getParam('order_id'));

        $order = Mage::getModel('sales/order')->loadByIncrementId($id);
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();

        if (!$order->getId() || !$customerId || $order->getCustomerId() != $customerId) {
            return false;
        }
        return $order;
    }
}