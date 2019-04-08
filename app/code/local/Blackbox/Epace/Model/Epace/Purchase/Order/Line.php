<?php

/**
 * @method int getId()
 * @method string getPoNumber()
 * @method string getUom()
 * @method string getQtyUom()
 * @method int getLineType()
 * @method float getUnitPrice()
 * @method string getDescription()
 * @method string getLineStatus()
 * @method float getQtyOrdered()
 * @method string getJob()
 * @method string getJobPart()
 * @method int getGlAccount()
 * @method bool getTaxable()
 * @method string getDateEntered()
 * @method bool getPrintStreamShared()
 * @method int getExternalLineNum()
 * @method bool getPaperSheet()
 * @method float getTaxBase1()
 * @method bool getTaxBase1Forced()
 * @method float getTaxAmount1()
 * @method bool getTaxAmount1Forced()
 * @method float getTaxBase2()
 * @method bool getTaxBase2Forced()
 * @method float getTaxAmount2()
 * @method bool getTaxAmount2Forced()
 * @method float getExtendedPrice()
 * @method float getQuantityToReceive()
 * @method string getTotalWeight()
 * @method string getJobPartKey()
 *
 * Class Blackbox_Epace_Model_Epace_Purchase_Order_Line
 */
class Blackbox_Epace_Model_Epace_Purchase_Order_Line extends Blackbox_Epace_Model_Epace_Job_Part_AbstractChild
{
    const LINE_TYPE_MANUAL = 1;
    const LINE_TYPE_INVENTORY = 3;
    const LINE_TYPE_DESCRIPTION = 4;

    public function _construct()
    {
        $this->_init('PurchaseOrderLine', 'id');
    }

    /**
     * @return int
     */
    public function getPurchaseOrderId()
    {
        return $this->getData('purchaseOrder');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Purchase_Order
     */
    public function getPurchaseOrder()
    {
        return $this->_getObject('purchaseOrder', 'purchaseOrder', 'efi/purchase_order');
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Purchase_Order $order
     * @return $this
     */
    public function setPurchaseOrder(Blackbox_Epace_Model_Epace_Purchase_Order $order)
    {
        return $this->_setObject('purchaseOrder', $order);
    }

    /**
     * @return int
     */
    public function getJobPartOutsidePurchId()
    {
        return $this->getData('jobPartOutsidePurch');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Job_Part_OutsidePurch
     */
    public function getJobPartOutsidePurch()
    {
        return $this->_getObject('jobPartOutsidePurch', 'jobPartOutsidePurch', 'efi/job_part_outsidePurch');
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Job_Part_OutsidePurch $jobPartOutsidePurch
     * @return $this
     */
    public function setJobPartOutsidePurch(Blackbox_Epace_Model_Epace_Job_Part_OutsidePurch $jobPartOutsidePurch)
    {
        return $this->_setObject('jobPartOutsidePurch', $jobPartOutsidePurch);
    }

    /**
     * @return string
     */
    public function getActivityCodeId()
    {
        return $this->getData('activityCode');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Activity_Code
     */
    public function getActivityCode()
    {
        return $this->_getObject('activityCode', 'activityCode', 'efi/activity_code');
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'purchaseOrder' => 'int',
            'poNumber' => 'string',
            'uom' => 'string',
            'qtyUom' => 'string',
            'lineType' => 'int',
            'unitPrice' => 'float',
            'description' => 'string',
            'lineStatus' => 'string',
            'qtyOrdered' => 'float',
            'job' => 'string',
            'jobPart' => 'string',
            'activityCode' => 'string',
            'glAccount' => 'int',
            'jobPartOutsidePurch' => 'int',
            'taxable' => 'bool',
            'dateEntered' => 'date',
            'printStreamShared' => 'bool',
            'externalLineNum' => 'int',
            'paperSheet' => 'bool',
            'taxBase1' => 'float',
            'taxBase1Forced' => 'bool',
            'taxAmount1' => 'float',
            'taxAmount1Forced' => 'bool',
            'taxBase2' => 'float',
            'taxBase2Forced' => 'bool',
            'taxAmount2' => 'float',
            'taxAmount2Forced' => 'bool',
            'extendedPrice' => 'float',
            'quantityToReceive' => 'float',
            'totalWeight' => 'string',
            'JobPartKey' => 'string',
        ];
    }
}