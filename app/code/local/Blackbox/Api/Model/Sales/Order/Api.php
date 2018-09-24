<?php

class Blackbox_Api_Model_Sales_Order_Api extends Mage_Sales_Model_Order_Api
{
    protected $itemAttributes = array(
        'sku',
        'name',
        'price' => 'row_total',
        'qty' => 'qty_ordered'
    );

    public function create($customerData, $productsData,  $addresses, $shippingMethod, $paymentMethod, $store = null, $agreements = null)
    {
        /** @var Mage_Checkout_Model_Cart_Api $cartApi */
        $cartApi = Mage::getModel('checkout/cart_api');
        $quoteId = $cartApi->create($store);

        try {
            /** @var Mage_Checkout_Model_Cart_Customer_Api $cartCustomerApi */
            $cartCustomerApi = Mage::getModel('checkout/cart_customer_api');
            $cartCustomerApi->set($quoteId, $customerData);
            $cartCustomerApi->setAddresses($quoteId, $addresses, $store);

            /** @var Mage_Checkout_Model_Cart_Product_Api $cartProductApi */
            $cartProductApi = Mage::getModel('checkout/cart_product_api');
            $cartProductApi->add($quoteId, $productsData, $store);

            /** @var Mage_Checkout_Model_Cart_Shipping_Api $cartShippingApi */
            $cartShippingApi = Mage::getModel('checkout/cart_shipping_api');
            $cartShippingApi->setShippingMethod($quoteId, $shippingMethod, $store);

            /** @var Mage_Checkout_Model_Cart_Payment_Api $cartPaymentApi */
            $cartPaymentApi = Mage::getModel('checkout/cart_payment_api');
            $cartPaymentApi->setPaymentMethod($quoteId, $paymentMethod, $store);

            return $cartApi->createOrder($quoteId, $store, $agreements);
        } catch (Exception $e) {
            $quote = Mage::getModel('sales/quote')->setStoreId($store)->load($quoteId);
            if ($quote->getId()) {
                $quote->delete()->save();
            }
            throw $e;
        }
    }
}
