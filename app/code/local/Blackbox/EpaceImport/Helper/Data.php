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

    public function getJobTypes()
    {
        $data = Mage::getStoreConfig('epace_import/job/types');
        if ($data) {
            $data = json_decode($data, true);
        }

        if (!$data || !$data['types']) {
            $types = Mage::getResourceModel('efi/job_type_collection')->toOptionHash();
            Mage::getConfig()->saveConfig('epace_import/job/types', json_encode(['types' => $types]));
            return $types;
        } else {
            return $data['types'];
        }
    }
}