<?php
require_once(Mage::getModuleDir('controllers','Mage_Customer').DS.'AddressController.php');

class Blackbox_CustomerStores_AddressController extends Mage_Customer_AddressController
{
    public function searchAddressGridAction()
    {
        $html = $this->getLayout()->createBlock('blackbox_checkout/address_grid')->setTemplate('blackbox/addressbook/address/grid-customer.phtml')->setUrl('customer/address/searchAddressGrid')->toHtml();

        $this->getResponse()->setBody($html);
    }

    public function searchAddressGridModalAction()
    {
        $html = $this->getLayout()->createBlock('blackbox_checkout/address_grid')->setUrl('customer/address/searchAddressGridModal')->toHtml();

        $this->getResponse()->setBody($html);
    }

    public function changeDefaultStoreAction()
    {
        $type = $this->getRequest()->getParam('type');
        $addressId = $this->getRequest()->getParam('entity_id');
        $storelocatorId = $this->getRequest()->getParam('storelocator_id');

        try {
            /** @var Mage_Customer_Model_Customer $customer */
            $customer = Mage::getModel('customer/session')->getCustomer();

            if ($storelocatorId && !$addressId) {
                $storelocator = Mage::getModel('storelocator/storelocator')->load($storelocatorId);
                if (!$storelocator->getId()) {
                    Mage::throwException('Address no more exists.');
                }

                $customer->setData("default_{$type}_store", $storelocatorId)
                    ->setData('default_' . $type, null)
                    ->save();
            } else if (!$storelocatorId && $addressId) {
                $address = Mage::getModel('customer/address')->load($addressId);
                if (!$address->getId()) {
                    Mage::throwException('Address no more exists.');
                }

                $customer->setData('default_' . $type, $addressId)
                    ->setData("default_{$type}_store", null)
                    ->save();
            } else {
                throw new Exception();
            }
            Mage::getSingleton('customer/session')->addSuccess('The address was changed');
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('customer/session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('customer/session')->addError('An error has occured');
        }
        $this->_redirect('*/address/');
    }
}