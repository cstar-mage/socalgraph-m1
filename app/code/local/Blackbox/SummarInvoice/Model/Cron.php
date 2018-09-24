<?php

class Blackbox_SummarInvoice_Model_Cron
{
    public function monthlyReport($schedule)
    {
        Mage::register('summar_invoice_monthly_report', true);
        return $this->send($schedule, [
            'from' => date('Y-m-d', strtotime('-1 month')),
            'to' => date('Y-m-d')
        ]);
        Mage::unregister('summar_invoice_monthly_report');
    }

    public function weeklyReport($schedule)
    {
        Mage::register('summar_invoice_weekly_report', true);
        return $this->send($schedule, [
            'from' => date('Y-m-d', strtotime('-7 days')),
            'to' => date('Y-m-d')
        ]);
        Mage::unregister('summar_invoice_weekly_report');
    }

    /**
     * @param Mage_Cron_Model_Schedule $schedule
     * @param array $filter
     */
    public function send($schedule, $filter)
    {
        try {
            $layout = Mage::app()->getLayout();
            $invoiceViewBlock = $layout->createBlock('summar_invoice/adminhtml_summar_invoice_view');

            $this->_initReportAction($invoiceViewBlock, $filter);

            $invoiceViewBlock->initInvoice();

            /** @var Blackbox_SummarInvoice_Model_Summar_Invoice $invoice */
            $invoice = Mage::registry('current_invoice');
            if ($invoice && $invoice->getOrder()) {
                if (!$invoice->sendEmail()) {
                    throw new Exception('Could not send the email.');
                }
            } else {
                throw new Exception('Invoices were not found.');
            }
        } catch (Exception $e) {
            $schedule->setMessages($e->getMessage());
        }

        return $this;
    }

    protected function _initReportAction($blocks, $filter)
    {
        if (!is_array($blocks)) {
            $blocks = array($blocks);
        }

        $params = new Varien_Object();

        foreach ($filter as $key => $value) {
            if (!empty($value)) {
                $params->setData($key, $value);
            }
        }

        foreach ($blocks as $block) {
            if ($block) {
                $block->setPeriodType($params->getData('period_type'));
                $block->setFilterData($params);
            }
        }

        return $this;
    }

    /**
     * Convert dates in array from localized to internal format
     *
     * @param   array $array
     * @param   array $dateFields
     * @return  array
     */
    protected function _filterDates($array, $dateFields)
    {
        if (empty($dateFields)) {
            return $array;
        }
        $filterInput = new Zend_Filter_LocalizedToNormalized(array(
            'date_format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
        ));
        $filterInternal = new Zend_Filter_NormalizedToLocalized(array(
            'date_format' => Varien_Date::DATE_INTERNAL_FORMAT
        ));

        foreach ($dateFields as $dateField) {
            if (array_key_exists($dateField, $array) && !empty($dateField)) {
                $array[$dateField] = $filterInput->filter($array[$dateField]);
                $array[$dateField] = $filterInternal->filter($array[$dateField]);
            }
        }
        return $array;
    }
}