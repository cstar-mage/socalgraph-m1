<?php

/**
 * @method int getEntryType()
 * @method float getAmount()
 * @method float getCommissionBase()
 * @method float getTaxBase()
 *
 * Class Blackbox_Epace_Model_Epace_Receivable_Line
 */
class Blackbox_Epace_Model_Epace_Receivable_Line extends Blackbox_Epace_Model_Epace_AbstractObject
{
    const ENTRY_TYPE_SALES_DISTRIBUTION = 11;
    const ENTRY_TYPE_TAX_DISTRIBUTION = 7;
    const ENTRY_TYPE_COMISSION_DISTRIBUTION = 33;

    protected function _construct()
    {
        $this->_init('ReceivableLine', 'id');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Receivable|false
     */
    public function getReceivable()
    {
        return $this->_getObject('receivable', 'receivable', 'efi/receivable');
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Receivable $receivable
     * @return $this
     */
    public function setReceivable(Blackbox_Epace_Model_Epace_Receivable $receivable)
    {
        return $this->_setObject('receivable', $receivable);
    }

    /**
     * @return Blackbox_Epace_Model_Epace_SalesCategory|false
     */
    public function getSalesCategory()
    {
        return $this->_getObject('salesCategory', 'salesCategory', 'efi/salesCategory', true);
    }

    /**
     * @param Blackbox_Epace_Model_Epace_SalesCategory $salesCategory
     * @return $this
     */
    public function setSalesCategory(Blackbox_Epace_Model_Epace_SalesCategory $salesCategory)
    {
        return $this->_setObject('salesCategory', $salesCategory);
    }

    /**
     * @return Blackbox_Epace_Model_Epace_SalesTax
     */
    public function getSalesTax()
    {
        return $this->_getObject('salesTax', 'salesTax', 'efi/salesTax', true);
    }

    /**
     * @param Blackbox_Epace_Model_Epace_SalesTax $salesTax
     * @return $this
     */
    public function setSalesTax(Blackbox_Epace_Model_Epace_SalesTax $salesTax)
    {
        return $this->_setObject('salesTax', $salesTax);
    }

    /**
     * @return Blackbox_Epace_Model_Epace_SalesPerson|false
     */
    public function getSalesPerson()
    {
        return $this->_getObject('salesPerson', 'salesPerson', 'efi/salesPerson');
    }

    /**
     * @param Blackbox_Epace_Model_Epace_SalesPerson $salesPerson
     * @return $this
     */
    public function setSalesPerson(Blackbox_Epace_Model_Epace_SalesPerson $salesPerson)
    {
        return $this->_setObject('salesPerson', $salesPerson);
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'receivable' => 'int',
            'entryType' => 'int',
            'salesCategory' => 'int',
            'amount' => 'float',
            'salesTax' => 'string',
            'taxBase' => 'float',
            'salesPerson' => 'int',
            'comissionBase' => 'float'
        ];
    }
}