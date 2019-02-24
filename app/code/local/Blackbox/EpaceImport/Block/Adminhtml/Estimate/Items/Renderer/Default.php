<?php

/**
 * Adminhtml sales estimate item renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Block_Adminhtml_Estimate_Items_Renderer_Default extends Blackbox_EpaceImport_Block_Adminhtml_Estimate_Items_Abstract
{
    public function getItem()
    {
        return $this->_getData('item');//->getEstimateItem();
    }
}
