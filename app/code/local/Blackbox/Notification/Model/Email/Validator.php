<?php

/**
 * Notification Validator Model
 *
 * Allows dispatching before and after events for each controller action
 *
 * @package    Blackbox_Notification
 */
class Blackbox_Notification_Model_Email_Validator extends Blackbox_Notification_Model_Validator_Abstract
{
    protected  function _getCollectionModel()
    {
        return Mage::getResourceModel('blackbox_notification/rule_collection');
    }

    /**
     * @param Blackbox_Notification_Model_Rule $rule
     * @param array $params
     */
    protected function _sendNotification(Blackbox_Notification_Model_Rule_Abstract $rule, $params, $additionalEmail = null)
    {
        $params['rule'] = $rule;

        $template = $rule->getEmailTemplateId();
        $sender = $rule->getEmailSender();
        $recipients = $rule->getEmailsArray();
        if ($additionalEmail) {
            array_unshift(
                $recipients,
                $additionalEmail
            );
        }
        $mailTemplate = Mage::getModel('core/email_template');/* @var $mailTemplate Mage_Core_Model_Email_Template */

        Mage::log("Start Email Sending by Rule".$rule->getName(),
                  null,
                  'blackbox_notifications.log',
                    true
        );

        if ($rule->getCopyMethod() == 'bcc' && $additionalEmail) {
            $firstSkipped = false;
            foreach ($recipients as $key => $recipient) {
                if ($firstSkipped) {
                    $mailTemplate->addBcc($recipient);
                    unset($recipients[$key]);
                } else {
                    $firstSkipped = true;
                }
            }
        }

        foreach ($recipients as $recipient) {
            Mage::log("Start Email Sending by Rule" . $rule->getName() . "to " . $recipient,
                null,
                'blackbox_notifications.log',
                true
            );

            $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                ->sendTransactional(
                    $template,
                    $sender,
                    $recipient,
                    null,
                    $params
                );
        }

        $this->_writeLog($rule, $params);
    }

    /**
     * @param Blackbox_Notification_Model_Rule $rule
     * @param array $params
     */
    protected function _writeLog($rule, $params)
    {
        $log = Mage::getModel('blackbox_notification/log');
        $log->setType($rule->getType());

        $par = array();
        foreach($params as $key => $param)
        {
            if (is_object($param)) {
                if ($param instanceof Varien_Object) {
                    $par[$key] = $param->getId();
                }
            } else if (is_scalar($param)) {
                $par[$key] = $param;
            }
        }

        $log->setParams($par)->save();
    }

    protected function _getOrderSummaryRedirect($order, $rule)
    {
        /** @var Blackbox_Notification_Model_Email_Redirect $redirect */
        $redirect = Mage::getModel('blackbox_notification/email_redirect');

        $redirect->setType($redirect::TYPE_ORDER_SUMMARY);
        $redirect->setParams(array('order_id' => $order->getId()));
        $redirect->setConfig($rule->getRedirectConfig());

        return $redirect;
    }
}
