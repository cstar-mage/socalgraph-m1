<?php

/**
 * @method string getDescription()
 * @method int getExtraCategory()
 * @method bool getActive()
 *
 * Class Blackbox_Epace_Model_Epace_Invoice_Extra_Type
 */
class Blackbox_Epace_Model_Epace_Invoice_Extra_Type extends Blackbox_Epace_Model_Epace_Invoice_ChildAbstract
{
    const EXTRA_CATEGORY_FREIGHT = 1;
    const EXTRA_CATEGORY_MISC = 2;
    const EXTRA_CATEGORY_TYPE_DEPOSIT = 3;
    const EXTRA_CATEGORY_TYPE_DISCOUNT = 4;
    const EXTRA_CATEGORY_TYPE_POSTAGE = 5;

    protected function _construct()
    {
        $this->_init('InvoiceExtraType', 'id');
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

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'description' => 'string',
            'salesCategory' => 'int',
            'extraCategory' => 'int',
            'active' => 'bool',
        ];
    }
}