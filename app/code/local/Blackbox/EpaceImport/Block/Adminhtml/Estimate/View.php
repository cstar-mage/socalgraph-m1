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
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml epacei estimate view
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Block_Adminhtml_Estimate_View extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId    = 'estimate_id';
        $this->_controller  = 'epacei_estimate';
        $this->_mode        = 'view';

        parent::__construct();

        $this->_removeButton('delete');
        $this->_removeButton('reset');
        $this->_removeButton('save');
        $this->setId('epacei_estimate_view');
        $estimate = $this->getOrder();
        $coreHelper = Mage::helper('core');

        if ($this->_isAllowedAction('edit') && $estimate->canEdit()) {
            $confirmationMessage = $coreHelper->jsQuoteEscape(
                Mage::helper('epacei')->__('Are you sure? This estimate will be canceled and a new one will be created instead')
            );
            $onclickJs = 'deleteConfirm(\'' . $confirmationMessage . '\', \'' . $this->getEditUrl() . '\');';
            $this->_addButton('estimate_edit', array(
                'label'    => Mage::helper('epacei')->__('Edit'),
                'onclick'  => $onclickJs,
            ));
            // see if estimate has non-editable products as items
            $nonEditableTypes = array_keys($this->getOrder()->getResource()->aggregateProductsByTypes(
                $estimate->getId(),
                array_keys(Mage::getConfig()
                    ->getNode('adminhtml/epacei/estimate/create/available_product_types')
                    ->asArray()
                ),
                false
            ));
            if ($nonEditableTypes) {
                $confirmationMessage = $coreHelper->jsQuoteEscape(
                    Mage::helper('epacei')
                        ->__('This estimate contains (%s) items and therefore cannot be edited through the admin interface at this time, if you wish to continue editing the (%s) items will be removed, the estimate will be canceled and a new estimate will be placed.',
                        implode(', ', $nonEditableTypes), implode(', ', $nonEditableTypes))
                );
                $this->_updateButton('estimate_edit', 'onclick',
                    'if (!confirm(\'' . $confirmationMessage . '\')) return false;' . $onclickJs
                );
            }
        }

        if ($this->_isAllowedAction('cancel') && $estimate->canCancel()) {
            $confirmationMessage = $coreHelper->jsQuoteEscape(
                Mage::helper('epacei')->__('Are you sure you want to cancel this estimate?')
            );
            $this->_addButton('estimate_cancel', array(
                'label'     => Mage::helper('epacei')->__('Cancel'),
                'onclick'   => 'deleteConfirm(\'' . $confirmationMessage . '\', \'' . $this->getCancelUrl() . '\')',
            ));
        }

        if ($this->_isAllowedAction('emails') && !$estimate->isCanceled()) {
            $confirmationMessage = $coreHelper->jsQuoteEscape(
                Mage::helper('epacei')->__('Are you sure you want to send estimate email to customer?')
            );
            $this->addButton('send_notification', array(
                'label'     => Mage::helper('epacei')->__('Send Email'),
                'onclick'   => "confirmSetLocation('{$confirmationMessage}', '{$this->getEmailUrl()}')",
            ));
        }

        if ($this->_isAllowedAction('creditmemo') && $estimate->canCreditmemo()) {
            $confirmationMessage = $coreHelper->jsQuoteEscape(
                Mage::helper('epacei')->__('This will create an offline refund. To create an online refund, open an invoice and create credit memo for it. Do you wish to proceed?')
            );
            $onClick = "setLocation('{$this->getCreditmemoUrl()}')";
            if ($estimate->getPayment()->getMethodInstance()->isGateway()) {
                $onClick = "confirmSetLocation('{$confirmationMessage}', '{$this->getCreditmemoUrl()}')";
            }
            $this->_addButton('estimate_creditmemo', array(
                'label'     => Mage::helper('epacei')->__('Credit Memo'),
                'onclick'   => $onClick,
                'class'     => 'go'
            ));
        }

        // invoice action intentionally
        if ($this->_isAllowedAction('invoice') && $estimate->canVoidPayment()) {
            $confirmationMessage = $coreHelper->jsQuoteEscape(
                Mage::helper('epacei')->__('Are you sure you want to void the payment?')
            );
            $this->addButton('void_payment', array(
                'label'     => Mage::helper('epacei')->__('Void'),
                'onclick'   => "confirmSetLocation('{$confirmationMessage}', '{$this->getVoidPaymentUrl()}')",
            ));
        }

        if ($this->_isAllowedAction('hold') && $estimate->canHold()) {
            $this->_addButton('estimate_hold', array(
                'label'     => Mage::helper('epacei')->__('Hold'),
                'onclick'   => 'setLocation(\'' . $this->getHoldUrl() . '\')',
            ));
        }

        if ($this->_isAllowedAction('unhold') && $estimate->canUnhold()) {
            $this->_addButton('estimate_unhold', array(
                'label'     => Mage::helper('epacei')->__('Unhold'),
                'onclick'   => 'setLocation(\'' . $this->getUnholdUrl() . '\')',
            ));
        }

        if ($this->_isAllowedAction('review_payment')) {
            if ($estimate->canReviewPayment()) {
                $confirmationMessage = $coreHelper->jsQuoteEscape(
                    Mage::helper('epacei')->__('Are you sure you want to accept this payment?')
                );
                $onClick = "confirmSetLocation('{$confirmationMessage}', '{$this->getReviewPaymentUrl('accept')}')";
                $this->_addButton('accept_payment', array(
                    'label'     => Mage::helper('epacei')->__('Accept Payment'),
                    'onclick'   => $onClick,
                ));
                $confirmationMessage = $coreHelper->jsQuoteEscape(
                    Mage::helper('epacei')->__('Are you sure you want to deny this payment?')
                );
                $onClick = "confirmSetLocation('{$confirmationMessage}', '{$this->getReviewPaymentUrl('deny')}')";
                $this->_addButton('deny_payment', array(
                    'label'     => Mage::helper('epacei')->__('Deny Payment'),
                    'onclick'   => $onClick,
                ));
            }
            if ($estimate->canFetchPaymentReviewUpdate()) {
                $this->_addButton('get_review_payment_update', array(
                    'label'     => Mage::helper('epacei')->__('Get Payment Update'),
                    'onclick'   => 'setLocation(\'' . $this->getReviewPaymentUrl('update') . '\')',
                ));
            }
        }

        if ($this->_isAllowedAction('invoice') && $estimate->canInvoice()) {
            $_label = $estimate->getForcedDoShipmentWithInvoice() ?
                Mage::helper('epacei')->__('Invoice and Ship') :
                Mage::helper('epacei')->__('Invoice');
            $this->_addButton('estimate_invoice', array(
                'label'     => $_label,
                'onclick'   => 'setLocation(\'' . $this->getInvoiceUrl() . '\')',
                'class'     => 'go'
            ));
        }

        if ($this->_isAllowedAction('ship') && $estimate->canShip()
            && !$estimate->getForcedDoShipmentWithInvoice()) {
            $this->_addButton('estimate_ship', array(
                'label'     => Mage::helper('epacei')->__('Ship'),
                'onclick'   => 'setLocation(\'' . $this->getShipUrl() . '\')',
                'class'     => 'go'
            ));
        }

        if ($this->_isAllowedAction('reestimate')
            && $this->helper('epacei/reestimate')->isAllowed($estimate->getStore())
            && $estimate->canReestimateIgnoreSalable()
        ) {
            $this->_addButton('estimate_reestimate', array(
                'label'     => Mage::helper('epacei')->__('Reestimate'),
                'onclick'   => 'setLocation(\'' . $this->getReestimateUrl() . '\')',
                'class'     => 'go'
            ));
        }
    }

    /**
     * Retrieve estimate model object
     *
     * @return Blackbox_EpaceImport_Model_Estimate
     */
    public function getOrder()
    {
        return Mage::registry('epacei_estimate');
    }

    /**
     * Retrieve Order Identifier
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->getOrder()->getId();
    }

    public function getHeaderText()
    {
        if ($_extOrderId = $this->getOrder()->getExtOrderId()) {
            $_extOrderId = '[' . $_extOrderId . '] ';
        } else {
            $_extOrderId = '';
        }
        return Mage::helper('epacei')->__('Order # %s %s | %s', $this->getOrder()->getRealOrderId(), $_extOrderId, $this->formatDate($this->getOrder()->getCreatedAtDate(), 'medium', true));
    }

    public function getUrl($params='', $params2=array())
    {
        $params2['estimate_id'] = $this->getOrderId();
        return parent::getUrl($params, $params2);
    }

    public function getEditUrl()
    {
        return $this->getUrl('*/epacei_estimate_edit/start');
    }

    public function getEmailUrl()
    {
        return $this->getUrl('*/*/email');
    }

    public function getCancelUrl()
    {
        return $this->getUrl('*/*/cancel');
    }

    public function getInvoiceUrl()
    {
        return $this->getUrl('*/epacei_estimate_invoice/start');
    }

    public function getCreditmemoUrl()
    {
        return $this->getUrl('*/epacei_estimate_creditmemo/start');
    }

    public function getHoldUrl()
    {
        return $this->getUrl('*/*/hold');
    }

    public function getUnholdUrl()
    {
        return $this->getUrl('*/*/unhold');
    }

    public function getShipUrl()
    {
        return $this->getUrl('*/epacei_estimate_shipment/start');
    }

    public function getCommentUrl()
    {
        return $this->getUrl('*/*/comment');
    }

    public function getReestimateUrl()
    {
        return $this->getUrl('*/epacei_estimate_create/reestimate');
    }

    /**
     * Payment void URL getter
     */
    public function getVoidPaymentUrl()
    {
        return $this->getUrl('*/*/voidPayment');
    }

    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('epacei/estimate/actions/' . $action);
    }

    /**
     * Return back url for view grid
     *
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->getOrder()->getBackUrl()) {
            return $this->getOrder()->getBackUrl();
        }

        return $this->getUrl('*/*/');
    }

    public function getReviewPaymentUrl($action)
    {
        return $this->getUrl('*/*/reviewPayment', array('action' => $action));
    }
//
//    /**
//     * Return URL for accept payment action
//     *
//     * @return string
//     */
//    public function getAcceptPaymentUrl()
//    {
//        return $this->getUrl('*/*/reviewPayment', array('action' => 'accept'));
//    }
//
//    /**
//     * Return URL for deny payment action
//     *
//     * @return string
//     */
//    public function getDenyPaymentUrl()
//    {
//        return $this->getUrl('*/*/reviewPayment', array('action' => 'deny'));
//    }
//
//    public function getPaymentReviewUpdateUrl()
//    {
//        return $this->getUrl('*/*/reviewPaymentUpdate');
//    }
}
