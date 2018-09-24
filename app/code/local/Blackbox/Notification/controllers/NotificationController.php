<?php

class Blackbox_Notification_NotificationController extends Mage_Core_Controller_Front_Action
{
    public function viewAction()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if (!$customer->getId()) {
            return;
        }

        /** @var Blackbox_Notification_Model_Resource_Notification $resource */
        $resource = Mage::getResourceModel('blackbox_notification/notification');
        $resource->markAsViewed($customer->getId());

        $this->getResponse()->setHeader('Content-type', 'application/json')
            ->setBody(json_encode(['succes' => true]));
    }

    public function deleteAction()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if (!$customer->getId()) {
            return;
        }

        $result = [];

        try {
            /** @var Blackbox_Notification_Model_Notification $notification */
            $notification = Mage::getModel('blackbox_notification/notification')->load($this->getRequest()->getParam('id'));
            if (!$notification->getId() || $notification->getCustomerId() != $customer->getId()) {
                throw new Exception('Notification does not exist.');
            }
            $notification->delete();
            $result['success'] = true;
        } catch (Exception $e) {
            $result['success'] = false;
            $result['message'] = $e->getMessage();
        }

        $this->getResponse()->setHeader('Content-type', 'application/json')
            ->setBody(json_encode($result));
    }
}