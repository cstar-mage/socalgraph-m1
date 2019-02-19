<?php

class Blackbox_Tracking_Model_Cron
{
    const TRACKING_STATUS_PROCESS = 1;
    const TRACKING_STATUS_COMPLETE = 2;

    protected $api;
    protected $requestTime = null;

    public function __construct()
    {
        $this->api = new Trackingmore();
    }

    public function updateTrackingInfo()
    {
        /** @var Mage_Sales_Model_Entity_Order_Shipment_Collection $shipmentCollection */
        $shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection');
        $tracksSelect = $shipmentCollection->getResource()->getReadConnection()->select()->from($shipmentCollection->getResource()->getTable('sales/shipment_track'), 'parent_id')
            ->where('tracking_status != ' . self::TRACKING_STATUS_COMPLETE . ' OR tracking_status IS NULL')
            ->group('parent_id');
        $shipmentCollection->getSelect()->where('entity_id IN ?', $tracksSelect);

        /** @var Mage_Sales_Model_Order_Shipment $shipment */
        foreach ($shipmentCollection as $shipment) {
            $tracks = $shipment->getTracksCollection()->addFieldToFilter('tracking_status', ['neq' => self::TRACKING_STATUS_COMPLETE])
                ->addFieldToFilter('carrier_code', ['in' => ['fedex', 'ups']]);
            foreach ($tracks as $track) {
                $this->processTrack($shipment, $track);
            }
            $shipment->save();
        }
    }

    protected function processTrack(Mage_Sales_Model_Order_Shipment $shipment, Mage_Sales_Model_Order_Shipment_Track $track)
    {
        $data = $this->getTrackData($track);

        if ($data['status'] != 'notfound' && $data['status'] != 'pending') {
            if ($track->getLastEvent() != $data['lastEvent']) {
                $events = $data['origin_info']['trackinfo'];
                $new = false;
                for ($i = count($events) - 1; $i >= 0; $i--) {
                    $event = $events[$i];
                    if (!$new) {
                        if (!$track->getLastEvent()) {
                            $new = true;
                        } else {
                            if (implode(',', [
                                    $event['StatusDescription'],
                                    $event['Details'],
                                    $event['Date']
                                ]) == $track->getLastEvent()
                            ) {
                                $new = true;
                            }
                            continue;
                        }
                    }

                    $comment = Mage::getModel('sales/order_shipment_comment')
                        ->setComment($track->getNumber() . ': ' . $event['Details'] . ' ' . $event['StatusDescription'])
                        ->setIsCustomerNotified(false)
                        ->setIsVisibleOnFront(true)
                        ->setCreatedAt(strtotime($event['Date']))
                        ->setShipment($shipment)
                        ->setParentId($shipment->getId())
                        ->setStoreId($shipment->getStoreId());
                    $shipment->getCommentsCollection()->addItem($comment);
                    $shipment->setDataChanges(true);
                }

                $track->setLastEvent($data['lastEvent']);
            }

            if ($data['status'] == 'delivered') {
                $track->setTrackingStatus(self::TRACKING_STATUS_COMPLETE);
            } else if ($track->getTrackingStatus() != self::TRACKING_STATUS_PROCESS) {
                $track->setTrackingStatus(self::TRACKING_STATUS_PROCESS);
            }

            $track->save();
        }

        //$this->api->deleteTrackingItem($track->getCarrierCode(), $track->getNumber());
    }

    protected function getTrackData(Mage_Sales_Model_Order_Shipment_Track $track)
    {
        $response = $this->api->createTracking($track->getCarrierCode(), $track->getNumber());
        if ($response['meta']['code'] != 200 && $response['meta']['code'] != 4016) {
            Mage::log(json_encode($response), null, 'trackingmore.log', true);
            throw new \Exception('Unable to create tracking.');
        }

        $i = 0;
        do {
            $response = $this->api->getSingleTrackingResult($track->getCarrierCode(), $track->getNumber());
            if (empty($response['data'])) {
                Mage::log(json_encode($response), null, 'trackingmore.log', true);
                throw new \Exception('Unable to get tracking info.');
            }
            if ($response['data']['status'] == 'pending' && $i < 10) {
                $i++;
                sleep(1);
                continue;
            }

            break;
        } while (true);

        return $response['data'];
    }

    protected function _apiCall($method, $params)
    {
        $time = microtime(true);
        $diff = $time - $this->requestTime;
        if ($diff < 1) {
            usleep((int)((1 - $diff) * 1000000));
        }
        $result = call_user_func_array([$this->api, $method], $params);
        $this->requestTime = microtime(true);
        return $result;
    }
}