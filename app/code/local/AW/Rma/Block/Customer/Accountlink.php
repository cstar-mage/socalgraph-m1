<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Rma
 * @version    1.6.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Rma_Block_Customer_Accountlink extends Mage_Core_Block_Abstract
{
    /**
     * Adds RMA link to customer account
     */
    public function addLink()
    {
        if (Mage::helper('awrma')->isEnabled()) {
            $parentBlock = $this->getParentBlock();
            if ($parentBlock instanceof Mage_Customer_Block_Account_Navigation) {
                $parentBlock->addLink('awrma',
                    'awrma/customer_rma/list', $this->__('RMA'),
                    array('_secure' => Mage::app()->getStore(true)->isCurrentlySecure())
                );
            }
        }
    }
}