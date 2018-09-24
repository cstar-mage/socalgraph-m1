<?php

class Blackbox_Checkout_Model_Checkout_Cart extends Mage_Checkout_Model_Cart
{
    public function init()
    {
        $quote = $this->getQuote()->setCheckoutMethod('');

        if ($this->getCheckoutSession()->getCheckoutState() !== Mage_Checkout_Model_Session::CHECKOUT_STATE_BEGIN) {
            $quote->removePayment();
            $this->getCheckoutSession()->resetCheckout();
        }

        if (!$quote->hasItems()) {
            foreach ($quote->getAllShippingAddresses() as $address) {
                $address->setCollectShippingRates(false)
                    ->removeAllShippingRates();
            }
        }

        return $this;
    }

    /**
     * Save cart
     *
     * @return Mage_Checkout_Model_Cart
     */
    public function save()
    {
        Mage::dispatchEvent('checkout_cart_save_before', array('cart'=>$this));

        $this->getQuote()->getBillingAddress();
        foreach ($this->getQuote()->getAllShippingAddresses() as $address) {
            $address->setCollectShippingRates(true);
        }
        $this->getQuote()->collectTotals();
        $this->getQuote()->save();
        $this->getCheckoutSession()->setQuoteId($this->getQuote()->getId());
        /**
         * Cart save usually called after changes with cart items.
         */
        Mage::dispatchEvent('checkout_cart_save_after', array('cart'=>$this));
        return $this;
    }
}