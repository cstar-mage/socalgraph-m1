<?php

/**
 * Customer actions upon logging in via SSO
 *
 * Class Wizkunde_WebSSO_Model_Customer
 */
class Wizkunde_WebSSO_Model_Customer
{
    /**
     * Identity field of magento
     */
    const XTYPE_IDENTITY_FIELD         = 'websso/advanced/identity_field';

    /**
     * Setting which determines if the address creation enabled
     */
    const XTYPE_ADDRESS_CREATE        = 'websso/customer/allow_address_create';

    /**
     * Setting which determines if the address creation enabled
     */
    const XTYPE_ADDRESS_UPDATE        = 'websso/customer/allow_address_update';

    /**
     * @var Claims object
     */
    protected $oClaims = null;

    /**
     * Create a customer in Magento if it doesnt exist yet
     *
     * @param Varien_Event_Observer $claims
     * @return bool
     * @throws Exception
     */
    public function createCustomer(Varien_Event_Observer $observer)
    {
        $this->oClaims = $observer->getData('claims');

        /**
         * This is called before creating a new customer
         */
        Mage::dispatchEvent(
            'wizkunde_websso_customer_create_before',
            array(
                'claims' => $this->oClaims
            )
        );

        $identity = $this->oClaims->getClaim('email');

        if($identity === false || $identity === null) {
            throw new Exception('Identity field email is required to be set in the incoming claim information');
        }

        $customer = Mage::getModel('customer/customer');

        foreach($this->oClaims->getClaims() as $key => $value) {
            $customer->addData(array(
                $key  => $value
            ));
        }

        if($this->oClaims->getClaim('password') != null) {
            $customer->password_hash = $this->oClaims->getClaim('password');
        } else {
            $customer->password_hash = md5(uniqid());
        }

        $customer->save();
        $customer->setConfirmation(null);
        $customer->setStatus(1);
	    $customer->setIsApproved(1);
        $customer->save();

        /**
         * This is called after creating a new customer
         */
        Mage::dispatchEvent(
            'wizkunde_websso_customer_create_after',
            array(
                'claims' => $this->oClaims
            )
        );

        if (Mage::getStoreConfig(self::XTYPE_ADDRESS_CREATE, Mage::app()->getStore()->getId()) === true) {
            Mage::dispatchEvent(
                'wizkunde_websso_address_update',
                array(
                    'claims' => $this->oClaims,
                    'customer'  => $customer
                )
            );
        }

        return true;
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function updateCustomer(Varien_Event_Observer $observer)
    {
        $this->oClaims = $observer->getData('claims');
        $customer = $observer->getData('customer');

        $customer->addData($this->oClaims->getClaims());

        if($this->oClaims->getClaim('password') != null) {
            $customer->password_hash = $this->oClaims->getClaim('password');
        } else {
            $customer->password_hash = md5(uniqid());
        }

        $customer->save();

        if (Mage::getStoreConfig(self::XTYPE_ADDRESS_UPDATE, Mage::app()->getStore()->getId()) == true) {
            Mage::dispatchEvent(
                'wizkunde_websso_address_update',
                array(
                    'claims' => $this->oClaims,
                    'customer'  => $customer
                )
            );
        }
    }
}
