<?php

/**
 * @method Mage_Sales_Model_Order getOrder()
 * @method array getTrackingInfo()
 *
 * Class Blackbox_TenderGreens_Block_Tracking_Progress
 */
class Blackbox_TenderGreens_Block_Tracking_Progress extends Mage_Core_Block_Template
{
    protected $currentProgressNumber;

    protected function _construct()
    {
        $this->setTemplate('blackbox/tendergreens/tracking/progress.phtml');
        parent::_construct();
    }

    public function getProgressBarStep()
    {
        switch ($this->getCurrentProgressNumber()) {
            case 0:
                return 'zero';
            case 1:
                return 'first';
            case 2:
                return 'second';
            case 3:
                return 'third';
            case 4:
                return 'fourth';
            case 5:
                return 'fifth';

        }
    }

    public function getProgressPoints()
    {
        return [
            [
                'label' => 'Order is Ready'
            ],
            [
                'label' => 'Delivery Service'
            ],
            [
                'label' => 'Accepted by Courier'
            ],
            [
                'label' => 'Order Delivery'
            ],
            [
                'label' => 'Order Issued'
            ],
        ];
    }

    protected function getCurrentProgressNumber()
    {
        if ($this->currentProgressNumber === null) {
            $this->currentProgressNumber = $this->_getCurrentProgressNumber();
        }
        return $this->currentProgressNumber;
    }

    protected function _getCurrentProgressNumber()
    {
        if ($this->getOrder()->getAccepted()) {
            return 5;
        }
        if ($this->getOrder()->hasShipments()) {
            if ($trackingInfo = $this->getTrackingInfo()) {
                if ($trackingInfo->getStatus() == 'DELIVERED') {
                    return 5;
                }
                if (!$trackingInfo->getErrorMessage()) {
                    return 3;
                }
            }
            return 2;
        } else {
            if ($this->getOrder()->hasInvoices()) {
                return 1;
            } else {
                return 0;
            }
        }
    }

    public function getProgressSteps()
    {
        return [
            [
                'label' => 'order placed'
            ],
            [
                'label' => 'order processing'
            ],
            [
                'label' => 'accepted by courier'
            ]
        ];
    }

    public function getCurrentProgressStep()
    {
        if ($this->getOrder()->getAccepted() || $this->getOrder()->hasShipments()) {
            return 3;
        } else if ($this->getOrder()->hasInvoices()) {
            return 2;
        } else {
            return 1;
        }
    }
}