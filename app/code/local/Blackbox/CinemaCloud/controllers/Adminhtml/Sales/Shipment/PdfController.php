<?php

class Blackbox_CinemaCloud_Adminhtml_Sales_Shipment_PdfController extends Mage_Adminhtml_Controller_Action
{
    protected $_publicActions = ['deliveryReceipt'];

    public function deliveryReceiptAction()
    {
        $shipmentId = $this->getRequest()->getParam('shipment_id');
        $shipment = Mage::getModel('sales/order_shipment')->load($shipmentId);
        if (!$shipment->getId()) {
            Mage::throwException('Shipment does not exist.');
        }

        Mage::register('current_shipment', $shipment);

        $this->loadLayout('pdf_sales_order_shipment_delivery_receipt');
        $html = $this->getLayout()->getBlock('pdf_content')->toHtml();

        $height = 792;
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'c',
            'format' => [612 / \Mpdf\Mpdf::SCALE, $height / \Mpdf\Mpdf::SCALE],
            'margin_left' => 25 / \Mpdf\Mpdf::SCALE,
            'margin_right' => 25 / \Mpdf\Mpdf::SCALE,
            'margin_top' => 318.3 / \Mpdf\Mpdf::SCALE,
            'margin_bottom' => 25 / \Mpdf\Mpdf::SCALE,
        ]);
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->WriteHTML($html);

        $curY = $mpdf->y * \Mpdf\Mpdf::SCALE;
        if ($curY > $height - 163) {
            $mpdf->AddPage();
        }

        $block = $this->getLayout()->getBlock('pdf_end');
        $mpdf->WriteHTML($block->toHtml());

        $pdf = $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);

        $this->_prepareDownloadResponse('Delivery Receipt ' . $shipment->getIncrementId() . '.pdf', $pdf, 'application/pdf');
    }

    public function shippingLabelsAction()
    {
        $shipmentId = $this->getRequest()->getParam('shipment_id');
        $shipment = Mage::getModel('sales/order_shipment')->load($shipmentId);
        if (!$shipment->getId()) {
            Mage::throwException('Shipment does not exist.');
        }

        Mage::register('current_shipment', $shipment);

        $this->loadLayout('pdf_sales_order_shipment_shipping_labels');
        $html = $this->getLayout()->getBlock('pdf_content')->toHtml();
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'c',
            'format' => [612 / \Mpdf\Mpdf::SCALE, 792 / \Mpdf\Mpdf::SCALE],
            'margin_left' => 35 / \Mpdf\Mpdf::SCALE,
            'margin_right' => 35 / \Mpdf\Mpdf::SCALE,
            'margin_top' => 35 / \Mpdf\Mpdf::SCALE,
            'margin_bottom' => 35 / \Mpdf\Mpdf::SCALE,
        ]);
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->WriteHTML($html);
        $pdf = $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);

        $this->_prepareDownloadResponse('Shipping Labels ' . $shipment->getIncrementId() . '.pdf', $pdf, 'application/pdf');
    }
}