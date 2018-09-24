<?php

class Blackbox_SummarInvoice_Model_Observer
{
    public function initNotification($observer)
    {
        $observer->getTypeConditions()->setData(Blackbox_SummarInvoice_Model_Summar_Invoice::NOTIFICATION_TYPE_MULTIINVOICE, 'summar_invoice/rule_condition_combine');
        $observer->getTypes()->setData(Blackbox_SummarInvoice_Model_Summar_Invoice::NOTIFICATION_TYPE_MULTIINVOICE, 'Summar Invoice');
        $observer->getEmailNodes()->setData(Blackbox_SummarInvoice_Model_Summar_Invoice::NOTIFICATION_TYPE_MULTIINVOICE, 'newsletter_summar_invoice');
    }

    public function initHeadNotification($observer)
    {
        $observer->getTypeConditions()->setData(Blackbox_SummarInvoice_Model_Summar_Invoice::NOTIFICATION_TYPE_MULTIINVOICE, 'summar_invoice/rule_condition_combine');
        $observer->getTypes()->setData(Blackbox_SummarInvoice_Model_Summar_Invoice::NOTIFICATION_TYPE_MULTIINVOICE, 'Summar Invoice');
    }
}