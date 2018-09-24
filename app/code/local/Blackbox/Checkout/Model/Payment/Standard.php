<?php

class Blackbox_Checkout_Model_Payment_Standard extends Mage_Payment_Model_Method_Abstract
{
    const CODE = 'nopayment';

    protected $_code = self::CODE;

    protected $_isInitializeNeeded      = true;
    protected $_canUseInternal          = true;
    protected $_canUseForMultishipping  = false;
    protected $_canUseCheckout          = true;

    /**
     * Check whether payment method is applicable to quote
     * Purposed to allow use in controllers some logic that was implemented in blocks only before
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param int|null $checksBitMask
     * @return bool
     */
    public function isApplicableToQuote($quote, $checksBitMask)
    {
        if ($checksBitMask & self::CHECK_USE_FOR_COUNTRY) {
            if (!$this->canUseForCountry($quote->getBillingAddress()->getCountry())) {
                return false;
            }
        }
        if ($checksBitMask & self::CHECK_USE_FOR_CURRENCY) {
            if (!$this->canUseForCurrency($quote->getStore()->getBaseCurrencyCode())) {
                return false;
            }
        }
        if ($checksBitMask & self::CHECK_USE_CHECKOUT) {
            if (!$this->canUseCheckout()) {
                return false;
            }
        }
        if ($checksBitMask & self::CHECK_USE_FOR_MULTISHIPPING) {
            if (!$this->canUseForMultishipping()) {
                return false;
            }
        }
        if ($checksBitMask & self::CHECK_USE_INTERNAL) {
            if (!$this->canUseInternal()) {
                return false;
            }
        }
        if ($checksBitMask & self::CHECK_ORDER_TOTAL_MIN_MAX) {
            $total = $quote->getBaseGrandTotal();
            $minTotal = $this->getConfigData('min_order_total');
            $maxTotal = $this->getConfigData('max_order_total');
            if (!empty($minTotal) && $total < $minTotal || !empty($maxTotal) && $total > $maxTotal) {
                return false;
            }
        }
        if ($checksBitMask & self::CHECK_RECURRING_PROFILES) {
            if (!$this->canManageRecurringProfiles() && $quote->hasRecurringItems()) {
                return false;
            }
        }
        return true;
    }
}