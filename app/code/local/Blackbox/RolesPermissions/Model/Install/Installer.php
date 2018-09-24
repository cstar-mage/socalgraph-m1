<?php

class Blackbox_RolesPermissions_Model_Install_Installer extends Mage_Install_Model_Installer
{
    public function createAdministrator($data)
    {
        $customer = Mage::getModel('customer/customer')->setData(array(
            'firstname' => $data->getFirstname(),
            'lastname' => $data->getLastname(),
            'email' => $data->getEmail(),
            'password' => $data->getNewPassword()
            ))->save();

        $data->setCustomerId((int)$customer->getId());
        return parent::createAdministrator($data);
    }
}