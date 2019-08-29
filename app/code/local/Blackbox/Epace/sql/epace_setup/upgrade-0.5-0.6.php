<?php

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();
// echo $this->getTable('epace/flat_vendor');
// echo 'working';
// die;
if ($installer->getConnection()->isTableExists('epacei_flat_vendor')) {
    $this->getConnection()->addColumn('epacei_flat_vendor', 'name', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 64,
        'nullable' => true,
        'comment' => 'Epace Vendor Company Name'
    ));
	
	$this->getConnection()->addColumn('epacei_flat_vendor', 'address1', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => true,
        'comment' => 'Epace Vendor Company Address 1'
    ));
	
	$this->getConnection()->addColumn('epacei_flat_vendor', 'address2', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => true,
        'comment' => 'Epace Vendor Company Address 2'
    ));
	
	$this->getConnection()->addColumn('epacei_flat_vendor', 'city', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 64,
        'nullable' => true,
        'comment' => 'Epace Vendor Company Address (city)'
    ));
	
	$this->getConnection()->addColumn('epacei_flat_vendor', 'zip', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 16,
        'nullable' => true,
        'comment' => 'Epace Vendor Company Address (zip)'
    ));
	
	$this->getConnection()->addColumn('epacei_flat_vendor', 'state', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 16,
        'nullable' => true,
        'comment' => 'Epace Vendor Company Address (state)'
    ));
	
	$this->getConnection()->addColumn('epacei_flat_vendor', 'country', array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'length' => 16,
        'nullable' => true,
        'comment' => 'Epace Vendor Company Address (country)'
    ));
		
	$this->getConnection()->addColumn('epacei_flat_vendor', 'vendorType', array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'length' => 16,
        'nullable' => true,
        'comment' => 'Epace Vendor Company Terms'
    ));
	$this->getConnection()->addColumn('epacei_flat_vendor', 'terms', array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'length' => 16,
        'nullable' => true,
        'comment' => 'Epace Vendor Company Terms'
    ));
	
	$this->getConnection()->addColumn('epacei_flat_vendor', 'print1099', array(
        'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
        'length' => 1,
        'nullable' => true,
        'comment' => 'Print'
    ));

    $this->getConnection()->addColumn('epacei_flat_vendor', 'contactFirstName', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => true,
        'comment' => 'Epace Vendor  Contat First Name'
    ));

    $this->getConnection()->addColumn('epacei_flat_vendor', 'contactLastName', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => true,
        'comment' => 'Epace Vendor  Contact Last Name'
    ));
	
	$this->getConnection()->addColumn('epacei_flat_vendor', 'currentBalance', array(
        'type' => Varien_Db_Ddl_Table::TYPE_FLOAT,
        'nullable' => true,
        'comment' => 'Epace Vendor Company currentBalance'
    ));
	
	$this->getConnection()->addColumn('epacei_flat_vendor', 'setupDate', array(
        'type' => Varien_Db_Ddl_Table::TYPE_DATETIME,
        'nullable' => true,
        'comment' => 'Epace Vendor Company setupDate'
    ));

     $this->getConnection()->addColumn('epacei_flat_vendor', 'emailAddress', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => true,
        'comment' => 'Epace Vendor  Email Address'
    ));
	
	$this->getConnection()->addColumn('epacei_flat_vendor', 'phoneNumber', array(
        'type' => Varien_Db_Ddl_Table::TYPE_FLOAT,
        'nullable' => true,
        'comment' => 'Epace Vendor Company Phone Number'
    ));

	$this->getConnection()->addColumn('epacei_flat_vendor', 'shipVia', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 16,
        'nullable' => true,
        'comment' => 'shipVia'
    ));

    $this->getConnection()->addColumn('epacei_flat_vendor', 'shipToContact', array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'length' => 255,
        'nullable' => true,
        'comment' => 'Epace Vendor Company Ship To Contact'
    ));

	$this->getConnection()->addColumn('epacei_flat_vendor', 'shipFromContact', array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'length' => 16,
        'nullable' => true,
        'comment' => 'ShipFromContact'
    ));
	
	$this->getConnection()->addColumn('epacei_flat_vendor', 'ytdPurch', array(
        'type' => Varien_Db_Ddl_Table::TYPE_FLOAT,
        'length' => 255,
        'nullable' => true,
        'comment' => 'ytdPurch'
    ));
	$this->getConnection()->addColumn('epacei_flat_vendor', 'ytdPayments', array(
        'type' => Varien_Db_Ddl_Table::TYPE_FLOAT,
        'length' => 255,
        'nullable' => true,
        'comment' => 'ytdPayments'
    ));
	$this->getConnection()->addColumn('epacei_flat_vendor', 'priorYearPurchaseDollars', array(
        'type' => Varien_Db_Ddl_Table::TYPE_FLOAT,
        'length' => 255,
        'nullable' => true,
        'comment' => 'priorYearPurchaseDollars'
    ));
	$this->getConnection()->addColumn('epacei_flat_vendor', 'poLinesTaxable', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => true,
        'comment' => 'poLinesTaxable'
    ));
	$this->getConnection()->addColumn('epacei_flat_vendor', 'defaultFreightOnBoard', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => true,
        'comment' => 'defaultFreightOnBoard'
    ));
	$this->getConnection()->addColumn('epacei_flat_vendor', 'paperUseLastBracket', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => true,
        'comment' => 'paperUseLastBracket'
    ));
   
   $this->getConnection()->addColumn('epacei_flat_vendor', 'defaultCurrency', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => true,
        'comment' => 'defaultCurrency'
    ));

    $this->getConnection()->addColumn('epacei_flat_vendor', 'agingCurrent', array(
        'type' => Varien_Db_Ddl_Table::TYPE_FLOAT,
        'length' => 255,
        'nullable' => true,
        'comment' => 'agingCurrent'
    ));
    $this->getConnection()->addColumn('epacei_flat_vendor', 'aging1', array(
        'type' => Varien_Db_Ddl_Table::TYPE_FLOAT,
        'length' => 255,
        'nullable' => true,
        'comment' => 'aging1'
    ));
    $this->getConnection()->addColumn('epacei_flat_vendor', 'aging2', array(
        'type' => Varien_Db_Ddl_Table::TYPE_FLOAT,
        'length' => 255,
        'nullable' => true,
        'comment' => 'aging2'
    ));
    $this->getConnection()->addColumn('epacei_flat_vendor', 'aging3', array(
        'type' => Varien_Db_Ddl_Table::TYPE_FLOAT,
        'length' => 255,
        'nullable' => true,
        'comment' => 'aging3'
    ));
    $this->getConnection()->addColumn('epacei_flat_vendor', 'aging4', array(
        'type' => Varien_Db_Ddl_Table::TYPE_FLOAT,
        'length' => 255,
        'nullable' => true,
        'comment' => 'aging4'
    ));
    $this->getConnection()->addColumn('epacei_flat_vendor', 'paymentAlt', array(
        'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
        'length' => 1,
        'nullable' => true,
        'comment' => 'paymentAlt'
    ));

 
 $this->getConnection()->addColumn('epacei_flat_vendor', 'shipFromAlt', array(
        'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
        'length' => 1,
        'nullable' => true,
        'comment' => 'shipFromAlt'
    ));
     $this->getConnection()->addColumn('epacei_flat_vendor', 'shipToAlt', array(
        'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
        'length' => 1,
        'nullable' => true,
        'comment' => 'shipToAlt'
    ));
      $this->getConnection()->addColumn('epacei_flat_vendor', 'manufacturingLocation', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => true,
        'comment' => 'manufacturingLocation'
    ));
       $this->getConnection()->addColumn('epacei_flat_vendor', 'remittanceDeliveryMethod', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => true,
        'comment' => 'remittanceDeliveryMethod'
    ));
        $this->getConnection()->addColumn('epacei_flat_vendor', 'sageAccountingEnabled', array(
        'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
        'length' => 1,
        'nullable' => true,
        'comment' => 'sageAccountingEnabled'
    ));
         $this->getConnection()->addColumn('epacei_flat_vendor', 'jeevesAccountingEnabled', array(
        'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
        'length' => 1,
        'nullable' => true,
        'comment' => 'jeevesAccountingEnabled'
    ));
          $this->getConnection()->addColumn('epacei_flat_vendor', 'stateKey', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => true,
        'comment' => 'State Key'
    ));
    $this->getConnection()->addColumn('epacei_flat_vendor', 'active', array(
        'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
        'length' => 1,
        'nullable' => true,
        'comment' => 'Active'
    ));
    $this->getConnection()->addColumn('epacei_flat_vendor', '_created_at', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        'nullable' => true,
        'comment' => 'Crate At'
    ));
    $this->getConnection()->addColumn('epacei_flat_vendor', '_updated_at', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        'nullable' => true,
        'comment' => 'Update At'
    ));
}

$installer->endSetup();