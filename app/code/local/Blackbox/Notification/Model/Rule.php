<?php

/**
 * Notification Rule data model
 *
 * @method Blackbox_Notification_Model_Resource_Rule _getResource()
 * @method Blackbox_Notification_Model_Resource_Rule getResource()
 * @method Blackbox_Notification_Model_Rule setType(string $value)
 * @method string getName()
 * @method Blackbox_Notification_Model_Rule setName(string $value)
 * @method string getDescription()
 * @method Blackbox_Notification_Model_Rule setDescription(string $value)
 * @method string getEmails()
 * @method Blackbox_Notification_Model_Rule setEmails(string $value)
 * @method string getEmailSender()
 * @method Blackbox_Notification_Model_Rule setEmailSender(string $value)
 * @method Blackbox_Notification_Model_Rule setEmailTemplateId(string $value)
 * @method int getIsActive()
 * @method Blackbox_Notification_Model_Rule setIsActive(int $value)
 * @method string getSettingsSerialized()
 * @method Blackbox_Notification_Model_Rule setSettingsSerialized(string $value)
 * @method string getWebsiteIds()
 * @method Blackbox_Notification_Model_Rule setWebsiteIds(string $value)
 * @method string getCopyMethod()
 * @method Blackbox_Notification_Model_Rule setCopyMethod(string $value)
 * @method Blackbox_Notification_Model_Rule setRedirectConfig(array $value)
 *
 * @package     Blackbox_Notification
 */
class Blackbox_Notification_Model_Rule extends Blackbox_Notification_Model_Rule_Abstract
{

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'blackbox_notification_rule';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getRule() in this case
     *
     * @var string
     */
    protected $_eventObject = 'rule';

    /**
     * Contain sores labels
     *
     * @deprecated after 1.6.2.0
     *
     * @var array
     */
    protected $_labels = array();

    /**
     * Store already validated addresses and validation results
     *
     * @var array
     */
    protected $_validatedItems = array();

    /**
     * Set resource model and Id field name
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('blackbox_notification/rule');
        $this->setIdFieldName('rule_id');
        $this->setCopyMethod('copy');

        $this->_initTypes();
    }

    /**
     * Save/delete coupon
     *
     * @return Blackbox_Notification_Model_Rule
     */
    protected function _afterSave()
    {
        if ($this->getRedirectConfig()) {
            $this->setRedirectConfig(json_decode($this->getRedirectConfig(), true));
        }
        parent::_afterSave();
        return $this;
    }

    /**
     * Initialize rule model data from array.
     * Set store labels if applicable.
     *
     * @param array $data
     *
     * @return Blackbox_Notification_Model_Rule
     */
    public function loadPost(array $data)
    {
        parent::loadPost($data);

        if (isset($data['store_labels'])) {
            $this->setStoreLabels($data['store_labels']);
        }

        return $this;
    }

    protected $_emailNodes = array(
        self::TYPE_ORDER_EMAIL => 'sales_email_order',
        self::TYPE_ORDER_UPDATE_EMAIL => 'sales_email_order_comment',
        self::TYPE_INVOICE_EMAIL => 'sales_email_invoice',
        self::TYPE_INVOICE_UPDATE_EMAIL => 'sales_email_invoice_comment',
        self::TYPE_SHIPMENT_EMAIL => 'sales_email_shipment',
        self::TYPE_SHIPMENT_UPDATE_EMAIL => 'sales_email_shipment_comment',
        self::TYPE_CREDITMEMO_EMAIL => 'sales_email_creditmemo',
        self::TYPE_CREDITMEMO_UPDATE_EMAIL => 'sales_email_creditmemo_comment',
    );

    public function getEmailTemplateNode()
    {
        if (isset($this->_emailNodes[$this->getType()])) {
            return $this->_emailNodes[$this->getType()] . '_template';
        }
        return null;
    }

    protected function _initTypes()
    {
        $typeConditions = new Varien_Object($this->_typeConditions);
        $types = new Varien_Object($this->_types);
        $emailNodes = new Varien_Object($this->_emailNodes);

        Mage::dispatchEvent('notification_types_init', [
            'type_conditions' => $typeConditions,
            'types' => $types,
            'email_nodes' => $emailNodes
        ]);
        $this->_typeConditions = $typeConditions->getData();
        $this->_types = $types->getData();
        $this->_emailNodes = $emailNodes->getData();
    }

    public function getEmailsArray()
    {
        if (!is_array(parent::getEmails())) {
            if (parent::getEmails()) {
                return array_map('trim', explode(',', parent::getEmails()));
            } else {
                return '';
            }
        }
        return parent::getEmails();
    }

    /**
     * @return array
     */
    public function getRedirectConfig()
    {
        if (is_string($config = $this->getData('redirect_config'))) {
            $this->setData('redirect_config', $config = json_decode($config, true));
        }
        return $config;
    }

    public function getEmailTemplateId()
    {
        if (!$templateId = parent::getEmailTemplateId()) {
            $this->setEmailTemplateId($templateId = $this->getEmailTemplateNode($this->getType()));
        }
        return $templateId;
    }

    protected function _beforeSave()
    {
        if (!is_numeric($this->getEmailTemplateId())) {
            $this->setEmailTemplateId(0);
        }
        if ($this->getRedirectConfig()) {
            $this->setRedirectConfig(json_encode(array_filter(array_values($this->getRedirectConfig()))));
        }
        return parent::_beforeSave();
    }

    protected function _afterLoad() {
        if ($this->getRedirectConfig() && is_string($this->getRedirectConfig())) {
            $this->setRedirectConfig(json_decode($this->getRedirectConfig(), true));
        }
    }
}
