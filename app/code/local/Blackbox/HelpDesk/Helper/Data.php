<?php

class Blackbox_HelpDesk_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getCommentRenderType(Blackbox_HelpDesk_Model_Comment $comment)
    {
        if ($comment->getIsOp()) {
            return 'op';
        }
        return 'default';
    }

    public function getZendeskRequesterId($customer)
    {
        if (!$customer->getZendeskRequesterId()) {
            try {
                $user = Mage::getModel('zendesk/api_requesters')->find($customer->getEmail());
            } catch (Exception $e) {
                // Continue on, no need to show an alert for this
                $user = null;
            }

            if($user) {
                $requesterId = $user['id'];
            } else {
                $requesterName = $customer->getName();
                // Create the requester as they obviously don't exist in Zendesk yet
                try {
                    // First check if the requesterName has been provided, since we need that to create a new
                    // user (but if one exists already then it doesn't need to be filled out in the form)
                    if(strlen($requesterName) == 0) {
                        throw new Exception('Requester name not provided for new user');
                    }

                    // All the data we need seems to exist, so let's create a new user
                    $user = Mage::getModel('zendesk/api_requesters')->create($customer->getEmail(), $requesterName);
                    $requesterId = $user['id'];
                } catch(Exception $e) {
                }
            }
            if (isset($requesterId)) {
                $customer->setZendeskRequesterId($requesterId);
            }
        }
        return $customer->getZendeskRequesterId();
    }

    public function getCustomerByZendeskUserId($userId)
    {
        /** @var Zendesk_Zendesk_Model_Api_Users $api */
        $api = Mage::getModel('zendesk/api_requesters');
        $user = $api->get($userId);
        $customer = Mage::getModel('customer/customer');
        $websiteId = Mage::app()->getStore()->getWebsiteId();
        if (!is_null($websiteId)) {
            $customer->setWebsiteId($websiteId);
        }
        return $customer->loadByEmail($user['email']);
    }

    public function getUploadedFiles()
    {
        $files = array();
        foreach ($_FILES as $FILE) {
            if (isset($FILE['name']) /*and (file_exists($FILE['tmp_name']))*/) {
                if (is_array($FILE['name'])) {
                    $count = count($FILE['name']);
                    if ($count == 1 && empty($FILE['name'][0])) {
                        continue;
                    }
                    for ($key = 0; $key < $count; $key++) {
                        /** @var Blackbox_HelpDesk_Model_File $file */
                        $file = Mage::getModel('helpdesk/file');
                        $file->setFileName($FILE['name'][$key]);
                        $file->setPath($FILE['tmp_name'][$key]);
                        $files[] = $file;
                    }
                } else {
                    /** @var Blackbox_HelpDesk_Model_File $file */
                    $file = Mage::getModel('helpdesk/file');
                    $file->setFileName($FILE['name']);
                    $file->setPath($FILE['tmp_name']);
                    $files[] = $file;
                }
            }
        }
        return $files;
    }
}