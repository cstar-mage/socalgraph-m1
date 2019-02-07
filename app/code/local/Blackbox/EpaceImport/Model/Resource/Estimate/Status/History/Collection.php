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
 * Flat sales order status history collection
 *
 * @category    Mage
 * @package     Blackbox_EpaceImport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Model_Resource_Estimate_Status_History_Collection
    extends Blackbox_EpaceImport_Model_Resource_Estimate_Collection_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'epacei_estimate_status_history_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject    = 'estimate_status_history_collection';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('epacei/estimate_status_history');
    }

    /**
     * Get history object collection for specified instance (order, shipment, invoice or credit memo)
     * Parameter instance may be one of the following types: Blackbox_EpaceImport_Model_Estimate,
     * Blackbox_EpaceImport_Model_Estimate_Creditmemo, Blackbox_EpaceImport_Model_Estimate_Invoice, Blackbox_EpaceImport_Model_Estimate_Shipment
     *
     * @param mixed $instance
     * @param string $historyEntityName
     *
     * @return Blackbox_EpaceImport_Model_Estimate_Status_History|null
     */
    public function getUnnotifiedForInstance($instance, $historyEntityName=Blackbox_EpaceImport_Model_Estimate::HISTORY_ENTITY_NAME)
    {
        if(!$instance instanceof Blackbox_EpaceImport_Model_Estimate) {
            $instance = $instance->getOrder();
        }
        $this->setEstimate($instance)->setOrder('created_at', 'desc')
            ->addFieldToFilter('entity_name', $historyEntityName)
            ->addFieldToFilter('is_customer_notified', 0)->setPageSize(1);
        foreach($this as $historyItem) {
            return $historyItem;
        }
        return null;
    }

}
