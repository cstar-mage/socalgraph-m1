<?php

/**
 * @method Mage_Oauth_Model_Token|null getToken()
 *
 * Class Blackbox_Api_Model_User
 */
class Blackbox_Api_Model_User extends Blackbox_RolesPermissions_Model_Admin_User
{
    public function loadByToken(Mage_Oauth_Model_Token $token)
    {
        $userType = $token->getUserType();

        if (Mage_Oauth_Model_Token::USER_TYPE_ADMIN == $userType) {
            $this->load($token->getAdminId());
        } else {
            $this->load($token->getCustomerId(), 'customer_id');
        }

        return $this->setToken($token);
    }

    public function loadBySessId($sessId)
    {
        $token = Mage::getResourceModel('oauth/token_collection')->addFieldToFilter('token', $sessId)->getFirstItem();
        if ($token) {
            return $this->loadByToken($token);
        } else {
            return $this->setData(array());
        }
    }
}