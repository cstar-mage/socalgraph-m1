<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order information tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Block_Adminhtml_Estimate_View_Tab_Info
    extends Blackbox_EpaceImport_Block_Adminhtml_Estimate_Abstract
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Retrieve estimate model instance
     *
     * @return Blackbox_EpaceImport_Model_Estimate
     */
    public function getOrder()
    {
        return Mage::registry('current_estimate');
    }

    /**
     * Retrieve source model instance
     *
     * @return Blackbox_EpaceImport_Model_Estimate
     */
    public function getSource()
    {
        return $this->getOrder();
    }

    /**
     * Retrieve estimate totals block settings
     *
     * @return array
     */
    public function getOrderTotalData()
    {
        return array(
            'can_display_total_due'      => true,
            'can_display_total_paid'     => true,
            'can_display_total_refunded' => true,
        );
    }

    public function getOrderInfoData()
    {
        return array(
            'no_use_estimate_link' => true,
        );
    }

    public function getTrackingHtml()
    {
        return $this->getChildHtml('estimate_tracking');
    }

    public function getItemsHtml()
    {
        return $this->getChildHtml('estimate_items');
    }

    /**
     * Retrieve giftmessage block html
     *
     * @deprecated after 1.4.2.0, use self::getGiftOptionsHtml() instead
     * @return string
     */
    public function getGiftmessageHtml()
    {
        return $this->getChildHtml('estimate_giftmessage');
    }

    /**
     * Retrieve gift options container block html
     *
     * @return string
     */
    public function getGiftOptionsHtml()
    {
        return $this->getChildHtml('gift_options');
    }

    public function getPaymentHtml()
    {
        return $this->getChildHtml('estimate_payment');
    }

    public function getViewUrl($estimateId)
    {
        return $this->getUrl('*/*/*', array('estimate_id'=>$estimateId));
    }

    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return Mage::helper('epacei')->__('Information');
    }

    public function getTabTitle()
    {
        return Mage::helper('epacei')->__('Order Information');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
