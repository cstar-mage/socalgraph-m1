<?php

class Blackbox_Notification_Model_Head_Notification_Customers_Source implements Mage_Eav_Model_Entity_Attribute_Source_Interface
{
    protected $options = null;

    public function getAllOptions()
    {
        $result = [];

        foreach ($this->getOptionHash() as $value => $label) {
            $result[] = [
                'label' => $label,
                'value' => $value
            ];
        }

        return $result;
    }

    public function getOptionText($value)
    {
        return $this->getOptionHash()[$value];
    }


    protected function getOptionHash()
    {
        if ($this->options === null) {
            $this->options = [
                0 => 'Self'
            ];

            $collection = Mage::getResourceModel('customer/customer_collection')
                ->addAttributeToSelect('prefix')
                ->addAttributeToSelect('firstname')
                ->addAttributeToSelect('middlename')
                ->addAttributeToSelect('lastname')
                ->addAttributeToSelect('suffix');

            /** @var Mage_Customer_Model_Customer $customer */
            foreach ($collection as $customer) {
                $this->options[$customer->getId()] = $customer->getName() . ' <' . $customer->getEmail() . '>';
            }
        }

        return $this->options;
    }
}