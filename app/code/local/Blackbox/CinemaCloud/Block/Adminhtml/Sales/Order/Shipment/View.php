<?php

class Blackbox_CinemaCloud_Block_Adminhtml_Sales_Order_Shipment_View extends Mage_Adminhtml_Block_Sales_Order_Shipment_View
{
    public function __construct()
    {
        parent::__construct();

        if ($shipmentId = $this->getShipment()->getEpaceShipmentId()) {
            $this->addButton('go_to_epace', [
                'label' => Mage::helper('sales')->__('Go to Epace'),
                'onclick' => 'popWin(\'' . $this->getEpaceUrl($shipmentId) . '\', \'_blank\', null)'
            ]);

            $this->_addButton('deliveryReceipt', array(
                    'label'     => Mage::helper('sales')->__('Delivery Ticket'),
                    'class'     => 'save',
                    'onclick'   => 'popWin(\''.$this->getDeliveryReceiptUrl().'\', null, \'modal=yes,width=\' + (parseInt(window.innerWidth)) + \',height=\' + (parseInt(window.innerHeight)) + \',toolbar=0,menubar=0,location=0,status=0\')'
                )
            );

            $this->_addButton('shippingLabels', array(
                    'label'     => Mage::helper('sales')->__('Shipping Labels'),
                    'class'     => 'save',
                    'onclick'   => 'popWin(\''.$this->getShippingLabelsUrl().'\', null, \'modal=yes,width=\' + (parseInt(window.innerWidth)) + \',height=\' + (parseInt(window.innerHeight)) + \',toolbar=0,menubar=0,location=0,status=0\')'
                )
            );
        }
    }

    public function getEpaceUrl($shipmentId)
    {
        /** @var Blackbox_Epace_Helper_Api $api */
        $api = Mage::helper('epace/api');
        return $api->getHost() . '/epace/company:public/object/JobShipment/detail/' . $shipmentId;
    }

    protected function getDeliveryReceiptUrl()
    {
        return $this->getUrl('*/sales_shipment_pdf/deliveryReceipt', array(
            'shipment_id' => $this->getShipment()->getId()
        ));
    }

    protected function getShippingLabelsUrl()
    {
        return $this->getUrl('*/sales_shipment_pdf/shippingLabels', array(
            'shipment_id' => $this->getShipment()->getId()
        ));
    }
}