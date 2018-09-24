<?php

class Blackbox_HelpDesk_Model_Observer
{
    public function initNotification($observer)
    {
        $observer->getTypeConditions()->setData(Blackbox_HelpDesk_Model_Cron::NOTIFICATION_TYPE_ZENDESK_UPDATE, 'blackbox_notification/rule_condition_blank');
        $observer->getTypes()->setData(Blackbox_HelpDesk_Model_Cron::NOTIFICATION_TYPE_ZENDESK_UPDATE, 'Zendesk Update');
    }

    public function initHeadNotification($observer)
    {
        $observer->getTypeConditions()->setData(Blackbox_HelpDesk_Model_Cron::NOTIFICATION_TYPE_ZENDESK_UPDATE, 'blackbox_notification/rule_condition_blank');
        $observer->getTypes()->setData(Blackbox_HelpDesk_Model_Cron::NOTIFICATION_TYPE_ZENDESK_UPDATE, 'Zendesk Update');
    }
}