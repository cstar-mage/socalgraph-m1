<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Blackbox_EpaceImport
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Flat sales estimate collection
 *
 * @category    Mage
 * @package     Blackbox_EpaceImport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Blackbox_EpaceImport_Model_Resource_Estimate_Collection_Abstract extends Mage_Sales_Model_Resource_Collection_Abstract
{
    /**
     * Estimate object
     *
     * @var Blackbox_EpaceImport_Model_Estimate
     */
    protected $_estimate   = null;

    /**
     * Estimate field for setEstimateFilter
     *
     * @var string
     */
    protected $_estimateField   = 'parent_id';

    /**
     * Set sales estimate model as parent collection object
     *
     * @param Blackbox_EpaceImport_Model_Estimate $estimate
     * @return Blackbox_EpaceImport_Model_Resource_Estimate_Collection_Abstract
     */
    public function setEstimate($estimate)
    {
        $this->_estimate = $estimate;
        if ($this->_eventPrefix && $this->_eventObject) {
            Mage::dispatchEvent($this->_eventPrefix . '_set_estimate', array(
                'collection' => $this,
                $this->_eventObject => $this,
                'estimate' => $estimate
            ));
        }

        return $this;
    }

    /**
     * Retrieve sales estimate as parent collection object
     *
     * @return Blackbox_EpaceImport_Model_Estimate|null
     */
    public function getEstimate()
    {
        return $this->_estimate;
    }

    /**
     * Add estimate filter
     *
     * @param int|Blackbox_EpaceImport_Model_Estimate $estimate
     * @return Blackbox_EpaceImport_Model_Resource_Estimate_Collection_Abstract
     */
    public function setEstimateFilter($estimate)
    {
        if ($estimate instanceof Blackbox_EpaceImport_Model_Estimate) {
            $this->setEstimate($estimate);
            $estimateId = $estimate->getId();
            if ($estimateId) {
                $this->addFieldToFilter($this->_estimateField, $estimateId);
            } else {
                $this->_totalRecords = 0;
                $this->_setIsLoaded(true);
            }
        } else {
            $this->addFieldToFilter($this->_estimateField, $estimate);
        }
        return $this;
    }
}
