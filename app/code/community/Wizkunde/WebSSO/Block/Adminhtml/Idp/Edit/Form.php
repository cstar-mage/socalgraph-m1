<?php

/**
 * Class Wizkunde_WebSSO_Block_Adminhtml_Idp_Edit_Form
 */
class Wizkunde_WebSSO_Block_Adminhtml_Idp_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Init class
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('websso_idp_form');
        $this->setTitle($this->__('Identity Provider Information'));
    }

    /**
     * Setup form fields for inserts/updates
     *
     * return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $model = Mage::registry('websso');

        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method'    => 'post'
        ));

        $baseFieldset = $form->addFieldset('base_fieldset', array(
            'legend'    => Mage::helper('checkout')->__('Identity Provider Information'),
            'class'     => 'fieldset-wide np hor-scroll',
        ));

        $infoFieldset = $baseFieldset->addFieldset('info_fieldset', array(
            'class'     => 'fieldset np nm inline-table',
        ));

        if ($model->getId()) {
            $infoFieldset->addField('id', 'hidden', array(
                'name' => 'id',
            ));
        }

        $infoFieldset->addField('name', 'text', array(
            'name'      => 'name',
            'class'     => 'required-entry validate-alphanum-with-spaces',
            'label'     => Mage::helper('websso')->__('Name'),
            'title'     => Mage::helper('websso')->__('Name'),
            'required'  => true,
        ));

        $infoFieldset->addField('identifier', 'text', array(
            'name'      => 'identifier',
            'class'     => 'required-entry',
            'label'     => Mage::helper('websso')->__('Identifier'),
            'title'     => Mage::helper('websso')->__('Identifier'),
            'required'  => true,
        ));

        $infoFieldset->addField('name_id', 'text', array(
            'name'      => 'name_id',
            'class'     => 'required-entry',
            'label'     => Mage::helper('websso')->__('EntityID'),
            'title'     => Mage::helper('websso')->__('EntityID'),
            'required'  => true,
        ));

        $infoFieldset->addField('name_id_format', 'select', array(
            'name'      => 'name_id_format',
            'class'     => 'required-entry',
            'label'     => Mage::helper('websso')->__('NameID Format'),
            'title'     => Mage::helper('websso')->__('NameID Format'),
            'values' => array(
                'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified',
                'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
                'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent',
                'urn:oasis:names:tc:SAML:2.0:nameid-format:transient' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient',
            ),
            'disabled' => false,
            'readonly' => false,
            'required'  => true,
            'style'     => 'width: 350px;'
        ));

        $infoFieldset->addField('metadata_url', 'text', array(
            'name'      => 'metadata_url',
            'class'     => 'required-entry',
            'label'     => Mage::helper('websso')->__('Metadata URL'),
            'title'     => Mage::helper('websso')->__('Metadata URL'),
            'required'  => true,
        ));

        $infoFieldset->addField('is_passive', 'select', array(
            'name'      => 'is_passive',
            'class'     => 'required-entry',
            'label'     => Mage::helper('websso')->__('Is Passive'),
            'title'     => Mage::helper('websso')->__('Is Passive'),
            'values' => array('0' => 'No','1' => 'Yes'),
            'disabled' => false,
            'readonly' => false,
            'required'  => true,
        ));

        $infoFieldset->addField('metadata_expiration', 'text', array(
            'name'      => 'metadata_expiration',
            'class'     => 'required-entry',
            'value'     => '86400',
            'label'     => Mage::helper('websso')->__('Metadata Expiration in seconds'),
            'title'     => Mage::helper('websso')->__('Metadata Expiration in seconds'),
            'required'  => true,
        ));

        $infoFieldset->addField('forceauthn', 'select', array(
            'name'      => 'forceauthn',
            'class'     => 'required-entry',
            'label'     => Mage::helper('websso')->__('Force Authentication (ForceAuthN)'),
            'title'     => Mage::helper('websso')->__('Force Authentication (ForceAuthN)'),
            'values' => array('0' => 'No','1' => 'Yes'),
            'disabled' => false,
            'readonly' => false,
            'required'  => true,
        ));

        $bindingFieldset = $baseFieldset->addFieldset('info_right_fieldset', array(
            'class'     => 'nm np inline-table',
        ));

        $bindingFieldset->addField('sso_header', 'note', array(
            'text' => '<h3>Binding Information</h3>'
        ));

        $bindingFieldset->addField('sso_binding', 'select', array(
            'name'      => 'sso_binding',
            'class'     => 'required-entry',
            'label'     => Mage::helper('websso')->__('Single SignOn Binding'),
            'title'     => Mage::helper('websso')->__('Single SignOn Binding'),
            'values'    => array('0' => 'Redirect', '1' => 'Post'),
            'disabled' => false,
            'readonly' => false,
            'required'  => true,
        ));

        $bindingFieldset->addField('slo_binding', 'select', array(
            'name'      => 'slo_binding',
            'class'     => 'required-entry',
            'label'     => Mage::helper('websso')->__('Single Logout Binding'),
            'title'     => Mage::helper('websso')->__('Single Logout Binding'),
            'values'    => array('0' => 'Redirect', '1' => 'Post'),
            'disabled'  => false,
            'readonly'  => false,
            'required'  => true,
        ));

        $bindingFieldset->addField('log_header', 'note', array(
            'text' => '<br /><h3>Logging Information</h3>'
        ));

        $bindingFieldset->addField('log_claims', 'select', array(
            'name'      => 'log_claims',
            'class'     => 'required-entry',
            'label'     => Mage::helper('websso')->__('Enable Claim Logging'),
            'title'     => Mage::helper('websso')->__('Enable Claim Logging'),
            'values' => array('0' => 'No','1' => 'Yes'),
            'disabled' => false,
            'readonly' => false,
            'required'  => true,
        ));

        $bindingFieldset->addField('log_debug', 'select', array(
            'name'      => 'log_debug',
            'class'     => 'required-entry',
            'label'     => Mage::helper('websso')->__('Enable Debug Logging'),
            'title'     => Mage::helper('websso')->__('Enable Debug Logging'),
            'values' => array('0' => 'No','1' => 'Yes'),
            'disabled' => false,
            'readonly' => false,
            'required'  => true,
        ));

        $certFieldset = $form->addFieldset('certificate_fieldset', array(
            'legend'    => Mage::helper('checkout')->__('Identity Provider Certificate'),
            'class'     => 'fieldset-wide np hor-scroll',
        ));

        $publicCertFieldSet = $certFieldset->addFieldset('public_cert_fieldset', array(
            'class'     => 'fieldset np nm inline-table',
        ));


        $publicCertFieldSet->addField('crt_data', 'textarea', array(
            'name'      => 'crt_data',
            'class'     => 'required-entry',
            'label'     => Mage::helper('websso')->__('Certificate Data (CRT)'),
            'title'     => Mage::helper('websso')->__('Certificate Data (CRT)'),
            'required'  => true,
        ));

        $publicCertFieldSet->addField('passphrase', 'text', array(
            'name'      => 'passphrase',
            'label'     => Mage::helper('websso')->__('Certificate Passphrase'),
            'title'     => Mage::helper('websso')->__('Certificate Passphrase'),
            'required'  => false,
        ));

        $privateCertFieldSet = $certFieldset->addFieldset('private_cert_fieldset', array(
            'class'     => 'fieldset np nm inline-table',
        ));

        $privateCertFieldSet->addField('pem_data', 'textarea', array(
            'name'      => 'pem_data',
            'class'     => 'required-entry',
            'label'     => Mage::helper('websso')->__('Private Key Data (PEM)'),
            'title'     => Mage::helper('websso')->__('Private Key Data (PEM)'),
            'required'  => true,
            'default'   => 'test'
        ));

        $claimFieldset = $form->addFieldset('claim_fieldset', array(
            'legend'    => Mage::helper('websso')->__('Additional Mappings'),
            'class'     => 'fieldset-wide',
        ));

        $claimFieldset->addType('claim_grid', 'Wizkunde_WebSSO_Block_Adminhtml_Idp_Edit_Form_Renderer_Fieldset_Claimgrid');

        $claimFieldset->addField('claims', 'claim_grid', array(
            'label' => Mage::helper('websso')->__('Mappings'),
            'class' => 'claim-data',
            'required' => false,
            'name' => 'claims',
            'tabindex' => 1
        ));

        $claimFieldset->addField('add_row', 'hidden', array(
            'label' => Mage::helper('core')->__('Actions'),
            'name'  => 'actions',
            'after_element_html' => '<button type="button" style="float: right;" onclick="addMappingRow();">Add Row</button>
<script type="text/javascript">
jQuery("#add_row").closest("td").removeClass("hidden");
</script>'
        ));

        $claimFieldset->addField('eav', 'select', array(
            'values' => $this->getAvailableAttributes(),
            'style' => 'display: none;'
        ));

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    protected function getAvailableAttributes()
    {
        $attributes = Mage::getModel('customer/entity_attribute_collection')->addVisibleFilter();

        $values = array();
        foreach ($attributes as $attribute) {
            if (($label = $attribute->getFrontendLabel()))
                $values[$attribute->getAttributeCode()] = 'Customer: ' . $label;
        }

        $values['password'] = 'Customer: password';

        $attributes = Mage::getModel('customer/entity_address_attribute_collection')->addVisibleFilter();
        foreach ($attributes as $attribute) {
            if (($label = $attribute->getFrontendLabel()))
                $values['billing_' . $attribute->getAttributeCode()] = 'Billing Address: ' . $label;
        }

        foreach ($attributes as $attribute) {
            if (($label = $attribute->getFrontendLabel()))
                $values['shipping_' . $attribute->getAttributeCode()] = 'Shipping Address: ' . $label;
        }

        return $values;
    }
}