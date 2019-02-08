<?php

/**
 * Abstract items renderer
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Block_Adminhtml_Estimate_Items_Abstract extends Mage_Adminhtml_Block_Template
{
    /**
     * Renderers with render type key
     * block    => the block name
     * template => the template file
     * renderer => the block object
     *
     * @var array
     */
    protected $_itemRenders = array();

    /**
     * Renderers for other column with column name key
     * block    => the block name
     * template => the template file
     * renderer => the block object
     *
     * @var array
     */
    protected $_columnRenders = array();

    /**
     * Flag - if it is set method canEditQty will return value of it
     *
     * @var boolean | null
     */
    protected $_canEditQty = null;

    /**
     * Init block
     *
     */
    protected function _construct()
    {
        $this->addColumnRender('qty', 'adminhtml/sales_items_column_qty', 'sales/items/column/qty.phtml');
        $this->addColumnRender('name', 'adminhtml/sales_items_column_name', 'sales/items/column/name.phtml');
        parent::_construct();
    }

    /**
     * Add item renderer
     *
     * @param string $type
     * @param string $block
     * @param string $template
     * @return Blackbox_EpaceImport_Block_Adminhtml_Estimate_Items_Abstract
     */
    public function addItemRender($type, $block, $template)
    {
        $this->_itemRenders[$type] = array(
            'block'     => $block,
            'template'  => $template,
            'renderer'  => null
        );
        return $this;
    }

    /**
     * Add column renderer
     *
     * @param string $column
     * @param string $block
     * @param string $template
     * @return Blackbox_EpaceImport_Block_Adminhtml_Estimate_Items_Abstract
     */
    public function addColumnRender($column, $block, $template, $type=null)
    {
        if (!is_null($type)) {
            $column .= '_' . $type;
        }
        $this->_columnRenders[$column] = array(
            'block'     => $block,
            'template'  => $template,
            'renderer'  => null
        );
        return $this;
    }

    /**
     * Retrieve item renderer block
     *
     * @param string $type
     * @return Mage_Core_Block_Abstract
     */
    public function getItemRenderer($type)
    {
        if (!isset($this->_itemRenders[$type])) {
            $type = 'default';
        }
        if (is_null($this->_itemRenders[$type]['renderer'])) {
            $this->_itemRenders[$type]['renderer'] = $this->getLayout()
                ->createBlock($this->_itemRenders[$type]['block'])
                ->setTemplate($this->_itemRenders[$type]['template']);
            foreach ($this->_columnRenders as $columnType=>$renderer) {
                $this->_itemRenders[$type]['renderer']->addColumnRender($columnType, $renderer['block'], $renderer['template']);
            }
        }
        return $this->_itemRenders[$type]['renderer'];
    }

    /**
     * Retrieve column renderer block
     *
     * @param string $column
     * @param string $compositePart
     * @return Mage_Core_Block_Abstract
     */
    public function getColumnRenderer($column, $compositePart='')
    {
        if (isset($this->_columnRenders[$column . '_' . $compositePart])) {
            $column .= '_' . $compositePart;
        }
        if (!isset($this->_columnRenders[$column])) {
            return false;
        }
        if (is_null($this->_columnRenders[$column]['renderer'])) {
            $this->_columnRenders[$column]['renderer'] = $this->getLayout()
                ->createBlock($this->_columnRenders[$column]['block'])
                ->setTemplate($this->_columnRenders[$column]['template'])
                ->setRenderedBlock($this);
        }
        return $this->_columnRenders[$column]['renderer'];
    }

    /**
     * Retrieve rendered item html content
     *
     * @param Varien_Object $item
     * @return string
     */
    public function getItemHtml(Varien_Object $item)
    {
        if ($item->getEstimateItem()) {
            $type = $item->getEstimateItem()->getProductType();
        } else {
            $type = $item->getProductType();
        }

        return $this->getItemRenderer($type)
            ->setItem($item)
            ->setCanEditQty($this->canEditQty())
            ->toHtml();
    }

    /**
     * Retrieve rendered item extra info html content
     *
     * @param Varien_Object $item
     * @return string
     */
    public function getItemExtraInfoHtml(Varien_Object $item)
    {
        $extraInfoBlock = $this->getChild('estimate_item_extra_info');
        if ($extraInfoBlock) {
            return $extraInfoBlock
                ->setItem($item)
                ->toHtml();
        }
        return '';
    }

    /**
     * Retrieve rendered column html content
     *
     * @param Varien_Object $item
     * @param string $column the column key
     * @param string $field the custom item field
     * @return string
     */
    public function getColumnHtml(Varien_Object $item, $column, $field = null)
    {
        if ($item->getEstimateItem()) {
            $block = $this->getColumnRenderer($column, $item->getEstimateItem()->getProductType());
        } else {
            $block = $this->getColumnRenderer($column, $item->getProductType());
        }

        if ($block) {
            $block->setItem($item);
            if (!is_null($field)) {
                $block->setField($field);
            }
            return $block->toHtml();
        }
        return '&nbsp;';
    }

    public function getCreditmemo()
    {
        return Mage::registry('current_creditmemo');
    }

    /**
     * ######################### SALES ##################################
     */

    /**
     * Retrieve available estimate
     *
     * @return Blackbox_EpaceImport_Model_Estimate
     */
    public function getEstimate()
    {
        if ($this->hasEstimate()) {
            return $this->getData('estimate');
        }
        if (Mage::registry('current_estimate')) {
            return Mage::registry('current_estimate');
        }
        if (Mage::registry('estimate')) {
            return Mage::registry('estimate');
        }
        if ($this->getInvoice())
        {
            return $this->getInvoice()->getEstimate();
        }
        if ($this->getCreditmemo())
        {
            return $this->getCreditmemo()->getEstimate();
        }
        if ($this->getItem()->getEstimate())
        {
            return $this->getItem()->getEstimate();
        }

        Mage::throwException(Mage::helper('sales')->__('Cannot get estimate instance'));
    }

    /**
     * Retrieve price data object
     *
     * @return Blackbox_EpaceImport_Model_Estimate
     */
    public function getPriceDataObject()
    {
        $obj = $this->getData('price_data_object');
        if (is_null($obj)) {
            return $this->getEstimate();
        }
        return $obj;
    }

    /**
     * Retrieve price attribute html content
     *
     * @param string $code
     * @param bool $strong
     * @param string $separator
     * @return string
     */
    public function displayPriceAttribute($code, $strong = false, $separator = '<br />')
    {
        if ($code == 'tax_amount' && $this->getEstimate()->getRowTaxDisplayPrecision()) {
            return $this->displayRoundedPrices(
                $this->getPriceDataObject()->getData('base_'.$code),
                $this->getPriceDataObject()->getData($code),
                $this->getEstimate()->getRowTaxDisplayPrecision(),
                $strong,
                $separator
            );
        } else {
            return $this->displayPrices(
                $this->getPriceDataObject()->getData('base_'.$code),
                $this->getPriceDataObject()->getData($code),
                $strong,
                $separator
            );
        }
    }

    /**
     * Retrieve price formated html content
     *
     * @param float $basePrice
     * @param float $price
     * @param bool $strong
     * @param string $separator
     * @return string
     */
    public function displayPrices($basePrice, $price, $strong = false, $separator = '<br />')
    {
        return $this->displayRoundedPrices($basePrice, $price, 2, $strong, $separator);
    }

    /**
     * Display base and regular prices with specified rounding precision
     *
     * @param   float $basePrice
     * @param   float $price
     * @param   int $precision
     * @param   bool $strong
     * @param   string $separator
     * @return  string
     */
    public function displayRoundedPrices($basePrice, $price, $precision=2, $strong = false, $separator = '<br />')
    {
        if ($this->getEstimate()->isCurrencyDifferent()) {
            $res = '';
            $res.= $this->getEstimate()->formatBasePricePrecision($basePrice, $precision);
            $res.= $separator;
            $res.= $this->getEstimate()->formatPricePrecision($price, $precision, true);
        }
        else {
            $res = $this->getEstimate()->formatPricePrecision($price, $precision);
            if ($strong) {
                $res = '<strong>'.$res.'</strong>';
            }
        }
        return $res;
    }

    /**
     * Retrieve include tax html formated content
     *
     * @param Varien_Object $item
     * @return string
     */
    public function displayPriceInclTax(Varien_Object $item)
    {
        $qty = ($item->getQty() ? $item->getQty() : ($item->getQty() ? $item->getQty() : 1));
        $baseTax = ($item->getTaxBeforeDiscount() ? $item->getTaxBeforeDiscount() : ($item->getTaxAmount() ? $item->getTaxAmount() : 0));
        $tax = ($item->getBaseTaxBeforeDiscount() ? $item->getBaseTaxBeforeDiscount() : ($item->getBaseTaxAmount() ? $item->getBaseTaxAmount() : 0));

        $basePriceTax = 0;
        $priceTax = 0;

        if (floatval($qty)) {
            $basePriceTax = $item->getBasePrice()+$baseTax/$qty;
            $priceTax = $item->getPrice()+$tax/$qty;
        }

        return $this->displayPrices(
            $this->getEstimate()->getStore()->roundPrice($basePriceTax),
            $this->getEstimate()->getStore()->roundPrice($priceTax)
        );
    }

    /**
     * Retrieve subtotal price include tax html formated content
     *
     * @param Varien_Object $item
     * @return string
     */
    public function displaySubtotalInclTax($item)
    {
        $baseTax = ($item->getTaxBeforeDiscount() ? $item->getTaxBeforeDiscount() : ($item->getTaxAmount() ? $item->getTaxAmount() : 0));
        $tax = ($item->getBaseTaxBeforeDiscount() ? $item->getBaseTaxBeforeDiscount() : ($item->getBaseTaxAmount() ? $item->getBaseTaxAmount() : 0));

        return $this->displayPrices(
            $item->getBaseRowTotal()+$baseTax,
            $item->getRowTotal()+$tax
        );
    }

    /**
     * Retrieve tax calculation html content
     *
     * @param Varien_Object $item
     * @return string
     */
    public function displayTaxCalculation(Varien_Object $item)
    {
        if ($item->getTaxPercent() && $item->getTaxString() == '') {
            $percents = array($item->getTaxPercent());
        } else if ($item->getTaxString()) {
            $percents = explode(Mage_Tax_Model_Config::CALCULATION_STRING_SEPARATOR, $item->getTaxString());
        } else {
            return '0%';
        }

        foreach ($percents as &$percent) {
            $percent = sprintf('%.2f%%', $percent);
        }
        return implode(' + ', $percents);
    }

    /**
     * Retrieve tax with persent html content
     *
     * @param Varien_Object $item
     * @return string
     */
    public function displayTaxPercent(Varien_Object $item)
    {
        if ($item->getTaxPercent()) {
            return sprintf('%s%%', $item->getTaxPercent() + 0);
        } else {
            return '0%';
        }
    }

    /**
     *  INVOICES
     */

    /**
     * Check shipment availability for current invoice
     *
     * @return bool
     */
    public function canCreateShipment()
    {
        foreach ($this->getInvoice()->getAllItems() as $item) {
            if ($item->getEstimateItem()->getQtyToShip()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Setter for flag _canEditQty
     *
     * @return Blackbox_EpaceImport_Block_Adminhtml_Estimate_Items_Abstract
     * @see self::_canEditQty
     * @see self::canEditQty
     */
    public function setCanEditQty($value) {
        $this->_canEditQty = $value;
        return $this;
    }

    /**
     * Check availability to edit quantity of item
     *
     * @return boolean
     */
    public function canEditQty()
    {
        return false;
    }

    public function canCapture()
    {
        return false;
    }

    public function formatPrice($price)
    {
        return $this->getEstimate()->formatPrice($price);
    }

    /**
     * Retrieve source
     *
     * @return Blackbox_EpaceImport_Model_Estimate
     */
    public function getSource()
    {
        return $this->getEstimate();
    }

}
