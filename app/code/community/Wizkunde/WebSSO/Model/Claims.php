<?php

/**
 * Class Wizkunde_WebSSO_Model_Claims
 */
class Wizkunde_WebSSO_Model_Claims
{
    /**
     * All claims that we know off now
     *
     * @var array
     */
    protected $_claims = array();

    /**
     * @param array $attributes
     */
    public function mapClaims($attributes = array())
    {
        $idpData = Mage::helper('websso/data')->getIdpData();

        if($idpData->getLogClaims() == true) {
            /**
             * Not relying on Mage::log here, because if Mage logging is disabled, it will write nothing and customers might get confused
             */
            file_put_contents(Mage::getBaseDir('log') . '/saml-claims-' . $idpData->getIdentifier() . '.log', date('Y-m-d H:i:s', time()) . ' => ' . var_export($attributes, true));
        }

        /**
         * Map all the claim mappings that we have set in the backend
         */
        $mappings = Mage::getResourceModel('websso/claim_collection');
        $mappings->addFieldToFilter('server_id', $idpData['id']);

        foreach($mappings as $attribute) {
            $externalData = unserialize($attribute->getExternal());

            if(isset($attributes[$externalData['value']]) || $externalData['transform'] == 'default') {
                $value = (isset($attributes[$externalData['value']])) ? $attributes[$externalData['value']] : '';
                $value = $this->transformClaim($externalData, $value);
                $this->_claims[$attribute->getInternal()] = $value;
            }
        }

        return $this;
    }

    /**
     * Transform the incoming claim
     *
     * @param $externalData
     * @param $value
     * @return string
     */
    protected function transformClaim($externalData, $value = '')
    {
        switch($externalData['transform']) {
            case 'default':
                $value = ($value == '') ? $externalData['extra'] : $value;
                break;
            case 'password':
                $customer = Mage::getModel('customer/customer');
                $value = $customer->hashPassword($value);
                break;
            case 'before':
                $data = explode($externalData['extra'], $value);
                $value = array_shift($data);
                break;
            case 'after':
                $data = explode($externalData['extra'], $value);
                array_shift($data);
                $value = implode($externalData['extra'], $data);
                break;
            case 'preg':
                preg_match($externalData['extra'], $value, $matches, PREG_OFFSET_CAPTURE);
                $value = (isset($matches[0]) && is_array($matches[0])) ? current($matches[0]) : $value;
            default:
                break;
        }

        return $value;
    }

    /**
     * Return the claim data
     *
     * @param $claimName
     * @return string
     */
    public function getClaim($claimName)
    {
        if (isset($this->_claims[$claimName])) {
            return $this->_claims[$claimName];
        }

        return '';
    }

    /**
     * Get all the mapped claims
     *
     * @return array
     */
    public function getClaims()
    {
        return $this->_claims;
    }
}
