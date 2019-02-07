<?php

class Blackbox_EpaceImport_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getShipViaMethodCode(Blackbox_Epace_Model_Epace_Ship_Via $shipVia)
    {
        switch ($shipVia->getShipProvider()->getName()) {
            case 'FedEx':
                $methodCode = $shipVia->getData('fedExMethod');
                break;
            case 'UPS':
                $methodCode = $shipVia->getData('uPSMethod');
                break;
        }
        if (empty($methodCode)) {
            $methodCode = $shipVia->getDescription();
        }

        return strtolower(str_replace(' ', '_', $shipVia->getShipProvider()->getName() . '_' . strtolower($methodCode)));
    }
}