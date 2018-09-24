<?php

class Blackbox_Epace_Model_Observer
{
    const JOB_STATE_ACTIVE = 0;
    const JOB_STATE_COMPLETE = 1;

    protected $_orderStates = array();

    public function createJob($observer)
    {
        $helper = Mage::helper('epace');

        if (!$helper->isEnabled()) {
            return;
        }

        $invoice = $observer->getEvent()->getInvoice(); /* @var Mage_Sales_Model_Order_Invoice $invoice */

        $canExportInvoice = false;
        foreach ($invoice->getAllItems() as $item) {
            if ($this->_canExportInvoiceItem($item)) {
                $canExportInvoice = true;
                break;
            }
        }
        if (!$canExportInvoice) {
            return;
        }

        if (!$helper->isLiveMode()) {
            Mage::getSingleton('adminhtml/session')->addSuccess('Epace create job method has fired');
            return;
        }

        $api = $this->_initApi('Create Job');
        $event = $api->getEvent();

        try {
            $address = $invoice->getOrder()->getShippingAddress();
            if ($address->getStorelocatorId()) {
                $storelocator = Mage::getModel('storelocator/storelocator')->load($address->getStorelocatorId());
                $customer = $storelocator->getEpaceCustomerId();
            } else {
                /** @var Mage_Customer_Model_Address $customerAddress */
//                $customerAddress = Mage::getModel('customer/address')->load($address->getCustomerAddressId());
//                $customer = $customerAddress->getEpaceCustomerId();
//                if (!$customer) {
//                    $result = $api->createObject([
//                        'custName' => $invoice->getOrder()->getCustomerName(),
//                        'address1' => $address->getStreetFull(),
//                        'city' => $address->getCity(),
//                        'customerType' => 1,
//                        'country' => $this->getEpaceCountryId($address->getCountryId()),
//                        'email' => $address->getEmail(),
//                        'faxNumber' => $address->getFax(),
//                        'phoneNumber' => $address->getTelephone(),
//                        'state' => $address->getRegionCode(),
//                        'zip' => $address->getPostcode(),
//                    ], 'customer');
//                }
            }

            if (!$customer) {
                $customer = Mage::getStoreConfig('epace/main_settings/default_customer_id');
            }
            $itemsErrors = array();
            $itemIds = array();
            $eventData = array();
            $order = $invoice->getOrder();

            $result = $api->createJob($customer, 'TG Test Order '. "{$order->getIncrementId()}", ['salesPerson' => Mage::getStoreConfig('epace/main_settings/sales_person')]);
            if (!$result) {
                throw new Exception('Can\t create job: not valid response');
            }

            $jobId = $result['job'];
            $invoice->setEpaceJob($jobId);

            $contacts = $api->findObjects('Contact', "@customer = '$customer' and @active");
            if (!empty($contacts)) {
                $contact = end($contacts);

                $jobContact = $api->createObject([
                    'job' => $jobId,
                    'contact' => $contact
                ], 'jobContact');

                $api->updateJob($jobId, null, [
                    'jobContact' => $jobContact['id'],
                    'shipToJobContact' => $jobContact['id']
                ]);
            }

            foreach($invoice->getAllItems() as $item) {
                try {
                    /* @var Mage_Sales_Model_Order_Invoice_Item $item */
                    if (!$this->_canExportInvoiceItem($item)) {
                        continue;
                    }
                    $finalPrice = $item->getRowTotalInclTax() - $item->getDiscountAmount();
                    $itemResult = $api->createJobProduct($jobId, $item->getSku(), $item->getQty(), 1, array(
                        'productValue' => $finalPrice,
                        'amountToInvoice' => $finalPrice
                    ));

                    if (!$itemResult) {
                        throw new Exception('Not valid response');
                    }

                    $itemIds[] = $itemResult['id'];
                } catch (Exception $e) {
                    $itemsErrors[] = 'Can\'t add item ' . $item->getSku() . ': ' . $e->getMessage() . '.';
                }
            }

            $event->setStatus(Blackbox_Epace_Model_Event::STATUS_SUCCESS);
        } catch (Exception $e) {
            $event->setStatus(Blackbox_Epace_Model_Event::STATUS_WITH_ERRORS);
            $eventData['error'] = $e->getMessage();
        }

        if (!empty($itemsErrors)) {
            $eventData['item errors'] = implode(' ', $itemsErrors);
        }
        if (!empty($itemIds)) {
            $eventData['created item ids'] = implode(', ', $itemIds);
        }
        $event->setSerializedData(serialize($eventData));
        $event->save();

    }

    public function completeOrder($observer)
    {
        $helper = Mage::helper('epace');

        if (!$helper->isEnabled()) {
            return;
        }

        if (!$helper->isLiveMode()) {
            Mage::getSingleton('adminhtml/session')->addSuccess('Epace update job method has fired');
            return;
        }

        $order = $observer->getEvent()->getOrder(); /* @var Mage_Sales_Model_Order $order */

        if ($order->getState() != Mage_Sales_Model_Order::STATE_COMPLETE)
        {
            return;
        }

        foreach ($order->getInvoiceCollection() as $invoice) {
            /* @var Mage_Sales_Model_Order_Invoice $invoice */

            if (!($job = $invoice->getEpaceJob()) || $invoice->getEpaceJobState() == self::JOB_STATE_COMPLETE) {
                continue;
            }

            $api = $this->_initApi('Close Job');
            $event = $api->getEvent();
            $eventData = array();

            try {
                $result = $api->updateJob($job, Blackbox_Epace_Helper_Api::JOB_STATUS_CLOSED);

                if ($result['job'] != $job || $result['adminStatus'] != Blackbox_Epace_Helper_Api::JOB_STATUS_CLOSED) {
                    continue;
                }

                $invoice->setEpaceJobState(self::JOB_STATE_COMPLETE);
                $invoice->getResource()->saveAttribute($invoice, 'epace_job_state');

                $event->setStatus(Blackbox_Epace_Model_Event::STATUS_SUCCESS);
            } catch (Exception $e) {
                $event->setStatus(Blackbox_Epace_Model_Event::STATUS_WITH_ERRORS);
                $eventData['error'] = $e->Message();
            }

            $event->setSerializedData(serialize($eventData));
            $event->save();
        }
    }

    /**
     * @param string $eventName
     * @return Blackbox_Epace_Helper_Api
     */
    protected function _initApi($eventName)
    {
        $api = Mage::helper('epace/api'); /* @var Blackbox_Epace_Helper_Api $api*/

        $event = Mage::getModel('epace/event')
            ->setData(array(
                'name' => $eventName,
                'processed_time' => time(),
                'status' => Blackbox_Epace_Model_Event::STATUS_CRITICAL,
                'username' => $api->getUsername(),
                'password' => $api->getPassword(),
                'host' => $api->getHost(),
            ));
        $event->save();
        $api->setEvent($event);

        return $api;
    }

    /**
     * @param Mage_Sales_Model_Order_Invoice_Item $item
     * @return bool
     */
    protected function _canExportInvoiceItem($item)
    {
        return $item->getOrderItem()->getProductType() != Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE
            && $item->getOrderItem()->getRealProductType() != Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE;
    }

    protected function getEpaceCountryId($country)
    {
        $countryMap = [
            'US' => 1,
            'CA' => 2,
            'MX' => 3,
            'AE' => 5,
            'AU' => 17,
            'BB' => 22,
            'BE' => 24,
            'BS' => 34,
            'CL' => 46,
            'CN' => 48,
            'DE' => 56,
            'DK' => 58,
            'FR' => 74,
            'GB' => 76,
            'HK' => 93,
            'IL' => 100,
            'IT' => 107,
            'JP' => 111,
            'KP' => 118,
            'MA' => 134,
            'NL' => 161,
            'PL' => 174,
            'QA' => 182,
            'SG' => 193
        ];

        return $countryMap[$country];
    }
}