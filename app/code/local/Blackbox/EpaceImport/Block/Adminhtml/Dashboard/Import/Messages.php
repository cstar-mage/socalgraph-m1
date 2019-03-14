<?php

class Blackbox_EpaceImport_Block_Adminhtml_Dashboard_Import_Messages extends Mage_Adminhtml_Block_Abstract
{
    protected function _prepareLayout()
    {
        $this->addMessages();
        return parent::_prepareLayout();
    }

    protected function addMessages()
    {
        /** @var Mage_Adminhtml_Model_Session $session */
        $session = Mage::getSingleton('adminhtml/session');

        $short = $this->getShortStatus();
        $long = $this->getLongStatus();
        $cron = $this->getCronStatus();

        $grouped = [];
        foreach ([$short, $long, $cron] as $status) {
            if (isset($grouped[$status])) {
                $grouped[$status]++;
            } else {
                $grouped[$status] = 1;
            }
        }

        if ($grouped['success'] + $grouped['running'] == 3) {
            $session->addSuccess('Epace synchronization is running.');
        } else if ($grouped['error'] || $grouped['undefined']) {
            $session->addError('Epace synchronization has errors.');
        } else if ($grouped['too_long']) {
            if ($short == 'too_long' || $long == 'too_long') {
                $session->addNotice('Synchronization with epace is running for too long.');
            } else if ($cron == 'too_long') {
                $session->addNotice('Epace synchronization by cron is running for too long.');
            }
        }

        if ($grouped['not_running']) {
            if ($grouped['not_running'] < 3) {
                $session->addError('Some of epace synchronization is not running.');
            } else {
                $session->addError('Epace synchronization is not running.');
            }
        }
    }

    protected function getShortStatus()
    {
        return $this->getMongoStatus('short', 3600);
    }

    protected function getLongStatus()
    {
        return $this->getMongoStatus('long', 3600 * 6);
    }

    protected function getMongoStatus($key, $timeout)
    {
        $mongoStatus = Mage::getStoreConfig('epace_import/mongo/' . $key);
        if (empty($mongoStatus)) {
            return 'not_running';
        }

        $mongoStatus = json_decode($mongoStatus, true);
        if (!$mongoStatus) {
            return 'undefined';
        }

        $time = time();

        if ($time - $mongoStatus['time'] > $timeout) {
            if ($mongoStatus['status'] == 'running') {
                return 'too_long';
            } else {
                return 'not_running';
            }
        }

        switch ($mongoStatus['status']) {
            case 'success':
            case 'error':
            case 'running':
                return $mongoStatus['status'];
            default:
                return 'undefined';
        }
    }

    protected function getCronStatus()
    {
        /** @var Mage_Cron_Model_Resource_Schedule_Collection $collection */
        $collection = Mage::getResourceModel('cron/schedule_collection');
        $collection->addFieldToFilter('job_code', 'epace_import')
            ->setOrder('executed_at', 'DESC')
            ->addFieldToFilter('executed_at', ['notnull' => true])
            ->setPageSize(1);

        /** @var Mage_Cron_Model_Schedule $schedule */
        $schedule = $collection->getFirstItem();
        if (!$schedule->getId()) {
            return 'not_running';
        }

        $time = null;
        foreach (['finished_at', 'executed_at'] as $field) {
            if (!empty($value = $schedule->getData($field))) {
                $time = strtotime($value);
                break;
            }
        }

        if (!$time) {
            return 'undefined';
        }

        $now = time();
        $timeout = 3600;
        if ($now - $time > $timeout) {
            if ($schedule->getStatus() == 'running') {
                return 'too_long';
            } else {
                return 'not_running';
            }
        }

        switch ($schedule->getStatus()) {
            case 'success':
            case 'running':
            case 'error':
                return $schedule->getStatus();
            case 'died':
                return 'error';
            default:
                return 'undefined';
        }
    }
}