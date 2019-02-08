<?php

/**
 * Adminhtml epacei estimate item renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Blackbox_EpaceImport_Block_Adminhtml_Estimate_View_Items_Renderer_Default extends Blackbox_EpaceImport_Block_Adminhtml_Estimate_Items_Abstract
{
    public function getItem()
    {
        return $this->_getData('item');
    }

    /**
     * Retrieve real html id for field
     *
     * @param string $name
     * @return string
     */
    public function getFieldId($id)
    {
        return $this->getFieldIdPrefix() . $id;
    }

    /**
     * Retrieve field html id prefix
     *
     * @return string
     */
    public function getFieldIdPrefix()
    {
        return 'estimate_item_' . $this->getItem()->getId() . '_';
    }

    /**
     * Indicate that block can display container
     *
     * @return boolean
     */
    public function canDisplayContainer()
    {
        return $this->getRequest()->getParam('reload') != 1;
    }

    /**
     * Retrive default value for giftmessage sender
     *
     * @deprecated after 1.4.2.0
     * @return string
     */
    public function getDefaultSender()
    {
        if(!$this->getItem()) {
            return '';
        }

        if($this->getItem()->getEstimate()) {
            return $this->getItem()->getEstimate()->getBillingAddress()->getName();
        }

        return $this->getItem()->getBillingAddress()->getName();
    }

    /**
     * Retrive default value for giftmessage recipient
     *
     * @deprecated after 1.4.2.0
     * @return string
     */
    public function getDefaultRecipient()
    {
        if(!$this->getItem()) {
            return '';
        }

        if($this->getItem()->getEstimate()) {
            if ($this->getItem()->getEstimate()->getShippingAddress()) {
                return $this->getItem()->getEstimate()->getShippingAddress()->getName();
            } else if ($this->getItem()->getEstimate()->getBillingAddress()) {
                return $this->getItem()->getEstimate()->getBillingAddress()->getName();
            }
        }

        if ($this->getItem()->getShippingAddress()) {
            return $this->getItem()->getShippingAddress()->getName();
        } else if ($this->getItem()->getBillingAddress()) {
            return $this->getItem()->getBillingAddress()->getName();
        }

        return '';
    }

    /**
     * Retrive block html id
     *
     * @deprecated after 1.4.2.0
     * @return string
     */
    public function getHtmlId()
    {
        return substr($this->getFieldIdPrefix(), 0, -1);
    }

    /**
     * Display susbtotal price including tax
     *
     * @param Blackbox_EpaceImport_Model_Estimate_Item $item
     * @return string
     */
    public function displaySubtotalInclTax($item)
    {
        return $this->displayPrices(
            $this->helper('checkout')->getBaseSubtotalInclTax($item),
            $this->helper('checkout')->getSubtotalInclTax($item)
        );
    }

    /**
     * Display item price including tax
     *
     * @param Blackbox_EpaceImport_Model_Estimate_Item $item
     * @return string
     */
    public function displayPriceInclTax(Varien_Object $item)
    {
        return $this->displayPrices(
            $this->helper('checkout')->getBasePriceInclTax($item),
            $this->helper('checkout')->getPriceInclTax($item)
        );
    }

}
