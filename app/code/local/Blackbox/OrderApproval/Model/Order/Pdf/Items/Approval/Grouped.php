<?php

/**
 * Sales Order Approval Pdf grouped items renderer
 *
 * @category   Blackbox
 * @package    Blackbox_OrderApproval
 */
class Blackbox_OrderApproval_Model_Order_Pdf_Items_Approval_Grouped extends Blackbox_OrderApprovaal_Model_Order_Pdf_Items_Approval_Default
{
    /**
     * Draw process
     */
    public function draw()
    {
        $type = $this->getItem()->getOrderItem()->getRealProductType();
        $renderer = $this->getRenderedModel()->getRenderer($type);
        $renderer->setOrder($this->getOrder());
        $renderer->setItem($this->getItem());
        $renderer->setPdf($this->getPdf());
        $renderer->setPage($this->getPage());

        $renderer->draw();
    }
}
