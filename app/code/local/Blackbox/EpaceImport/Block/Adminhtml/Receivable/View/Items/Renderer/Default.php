<?php

/**
 * Adminhtml epacei receivable item renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Blackbox_EpaceImport_Block_Adminhtml_Receivable_View_Items_Renderer_Default extends Blackbox_EpaceImport_Block_Adminhtml_Receivable_Items_Abstract
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
        return 'receivable_item_' . $this->getItem()->getId() . '_';
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

        if($this->getItem()->getReceivable()) {
            return $this->getItem()->getReceivable()->getBillingAddress()->getName();
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

        if($this->getItem()->getReceivable()) {
            if ($this->getItem()->getReceivable()->getShippingAddress()) {
                return $this->getItem()->getReceivable()->getShippingAddress()->getName();
            } else if ($this->getItem()->getReceivable()->getBillingAddress()) {
                return $this->getItem()->getReceivable()->getBillingAddress()->getName();
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
     * @param Blackbox_EpaceImport_Model_Receivable_Item $item
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
     * @param Blackbox_EpaceImport_Model_Receivable_Item $item
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
