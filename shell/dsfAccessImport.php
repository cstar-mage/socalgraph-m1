<?php

require_once 'abstract.php';

class Mage_Shell_DsfAccessImport extends Mage_Shell_Abstract
{
    protected $_csvDelimiter = ',';
    protected $_csvEnclosure = '"';
    protected $_csvEscape = '\\';

    protected $website;

    protected $storelocatorCollection;
    protected $customerNumber;

    protected $attributes = [
        'store_number' => 'STORE #',
        'position' => 'POSITION',
        'name' => 'NAME',
        'telephone' => 'PHONE',
        'email' => 'EMAIL',
        'address' => 'ADDRESS'
    ];

    public function _construct()
    {
        ini_set("memory_limit","2G");
        set_time_limit(0);
        ini_set('max_execution_time', 0);
    }

    public function run()
    {
        $file = $this->getArg('file');
        $website = $this->_getArg('website', 1);

        try {
            $this->website = Mage::app()->getWebsite($website);
            if (!$this->website) {
                throw new Exception('Wrong website.');
            }

            if (!$file) {
                throw new Exception('Specify file with --file option.');
            }

            $file = fopen($file, 'r');
            if (!$file) {
                throw new Exception('Couldn\'t open the file.');
            }

            $this->import($file);
        } catch (Exception $e) {
            $this->writeln($e->getMessage());
        } finally {
            if (is_resource($file)) {
                fclose($file);
            }
        }
    }

    protected function import($file)
    {
        $group = false;
        $state = false;
        $attributesIndexes = [];
        $this->customerNumber = 0;

        while (($row = fgetcsv($file, null, $this->_csvDelimiter, $this->_csvEnclosure, $this->_csvEscape)) !== FALSE) {
            if ($row[0] && !$row[1] && !$row[2] && !$row[3] && !$row[4]) {
                $row[0] = trim($row[0]);
                /** @var Mage_Customer_Model_Group $group */
                $group = Mage::getModel('customer/group')->load($row[0], 'customer_group_code');

                if (!$group->getId()) {
                    $group->setCode($row[0])->setTaxClassId(3)->save();
                    $this->writeln('Created customer group ' . $group->getCode());
                }
                $state = 'header';
            } else if ($state == 'header') {
                $attributesIndexes = $this->_getAttributesIndexes($this->attributes, $row);
                $state = 'customer';
            } else if ($state == 'customer') {
                if ($this->_isRowEmpty($row)) {
                    continue;
                }
                $this->_importCustomer($this->_prepareAttributes($row, $attributesIndexes), $group);
            }
        }
    }

    /**
     * @param array $data
     * @param Mage_Customer_Model_Group $group
     */
    protected function _importCustomer($data, $group)
    {
        $this->writeln('Importing customer ' . ++$this->customerNumber);
        $data = array_map('trim', $data);

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->writeln('Wrong email address: ' . $data['email']);
            return;
        }

        $address = $data['address'];
        unset($data['address']);

        if (strtolower($group->getCode()) == 'master') {
            $storelocatorIds = $this->_getStorelocators()->getAllIds();
        } else {
            $storelocatorIds = [];
            foreach ($this->_getStorelocators() as $storelocator) {
                if (rand(0, 1) == 1) {
                    $storelocatorIds[] = $storelocator->getId();
                }
            }
        }

        /** @var Mage_Customer_Model_Customer $customer */
        $customer = Mage::getModel('customer/customer')->setWebsiteId($this->website->getId())->loadByEmail($data['email']);
        if (!$customer->getId()) {
            if (preg_match('/^\s*(\S+)\s*(.+?)\s*$/', $data['name'], $match)) {
                $data['firstname'] = $match[1];
                $data['lastname'] = $match[2];
            } else {
                $data['firstname'] = $data['lastname'] = $data['name'];
            }
            unset($data['name']);

            $customer->setData($data)
                ->setWebsiteId($this->website->getId())
                ->setGroupId($group->getId())
                ->setPassword('qweqwe')
                ->setVisibleStores(implode(',', $storelocatorIds))
                ->save();
        } else {
            $customer->addData([
                'position' => $data['position'],
                'store_number' => $data['store_number'],
                'approved' => 1,
                'telephone' => $data['telephone'],
                'visible_stores' => implode(',', $storelocatorIds)
            ])->save();
        }

        if (!$address || $customer->getDefaultBillingAddress()) {
            return;
        }

        if (preg_match('/^(.*)(?:\n(.*))?\n(.*), (\S+) (\S+)$/', $address, $match)) {
            $data['street'] = [$match[1]];
            if ($match[2]) {
                $data['street'][] = $match[2];
            }
            $data['city'] = $match[3];
            $data['region'] = $match[4];
            $data['postcode'] = $match[5];
        } else {
            $data['street'] = $address;
        }

        /** @var Mage_Customer_Model_Address $address */
        $address = Mage::getModel('customer/address');
        $address->setData($data)
            ->setCustomer($customer)
            ->setCountryId('US')
            ->setIsDefaultBilling('1')
            ->setIsDefaultShipping('1')
            ->setSaveInAddressBook('1')
            ->save();
        $this->writeln('Imported customer address');
    }

    protected function &_prepareAttributes(&$csvRow, &$attributesIndexes)
    {
        $attributes = array();
        foreach ($attributesIndexes as $attribute => $index) {
            $attributes[$attribute] = $csvRow[$index];
        }
        return $attributes;
    }

    protected function _getAttributesIndexes(&$attributesMap, &$row)
    {
        $result = array();

        foreach ($attributesMap as $attribute => $headers) {
            if (is_array($headers)) {
                foreach ($headers as $header) {
                    $key = $this->_getValueKey($header, $row);
                    if ($key === false) {
                        continue;
                    }
                    $result[$attribute] = $key;
                    break;
                }
                if (!key_exists($attribute, $result)) {
                    return false;
                }
            } else {
                $header = $headers;
                $key = $this->_getValueKey($header, $row);
                if ($key === false) {
                    return false;
                }
                if (is_numeric($attribute)) {
                    $attribute = $header;
                }
                $result[$attribute] = $key;
            }
        }

        return $result;
    }

    protected function _getValueKey($search, &$array) {
        $search = strtolower($search);
        foreach ($array as $key => $value) {
            if (strtolower(trim($value)) == $search) {
                return $key;
            }
        }
        return false;
    }

    /**
     * @return Magestore_Storelocator_Model_Mysql4_Storelocator_Collection
     */
    protected function _getStorelocators()
    {
        if (!isset($this->storelocatorCollection)) {
            $this->storelocatorCollection = Mage::getResourceModel('storelocator/storelocator_collection')->load();
        }
        return $this->storelocatorCollection;
    }

    protected function _isRowEmpty(array &$row)
    {
        foreach ($row as $field) {
            if ($field) {
                return false;
            }
        }
        return true;
    }

    protected function writeln($message)
    {
        echo $message . PHP_EOL;
    }

    protected function _getArg($name, $default)
    {
        return isset($this->_args[$name]) ? $this->_args[$name] : $default;
    }
}

$shell = new Mage_Shell_DsfAccessImport();
$shell->run();
