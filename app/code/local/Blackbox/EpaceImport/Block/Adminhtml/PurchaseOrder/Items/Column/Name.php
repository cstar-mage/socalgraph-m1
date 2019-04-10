<?php

/**
 * Sales Order items name column renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Block_Adminhtml_PurchaseOrder_Items_Column_Name extends Blackbox_EpaceImport_Block_Adminhtml_PurchaseOrder_Items_Column_Default
{
    /**
     * Add line breaks and truncate value
     *
     * @param string $value
     * @return array
     */
    public function getFormattedOption($value)
    {
        $_remainder = '';
        $value = Mage::helper('core/string')->truncate($value, 55, '', $_remainder);
        $result = array(
            'value' => nl2br($value),
            'remainder' => nl2br($_remainder)
        );

        return $result;
    }
}
?>
