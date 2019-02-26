<?php

/**
 * @method string getName()
 * @method float getTaxableLimit()
 * @method float getRate1()
 * @method float getRate2()
 * @method float getRate3()
 * @method float getRate4()
 * @method float getRate5()
 * @method float getRate6()
 * @method bool getActive()
 * @method bool getActualCostBasedTaxing()
 * @method bool getCalculateCanadianSalesTax()
 * @method bool getSelfTax()
 * @method float getTaxRate()
 *
 * Class Blackbox_Epace_Model_Epace_SalesTax
 */
class Blackbox_Epace_Model_Epace_SalesTax extends Blackbox_Epace_Model_Epace_AbstractObject
{
    protected function _construct()
    {
        $this->_init('SalesTax', 'id');
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
            'id' => 'string',
            'name' => 'string',
            'taxableLimit' => 'float',
            'salesCategory' => 'int',
            'rate1' => 'float',
            'rate2' => 'float',
            'rate3' => 'float',
            'rate4' => 'float',
            'rate5' => 'float',
            'rate6' => 'float',
            'active' => 'bool',
            'actualCostBasedTaxing' => 'bool',
            'calculateCanadianSalesTax' => 'bool',
            'selfTax' => 'bool',
            'taxRate' => 'float',
        ];
    }
}