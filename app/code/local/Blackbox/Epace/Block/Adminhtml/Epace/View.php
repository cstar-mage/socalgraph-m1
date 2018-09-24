<?php

class Blackbox_Epace_Block_Adminhtml_Epace_View extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('blackbox/epace/view.phtml');
        $this->setId('event_view');
    }

    /**
     * Retrieve currently edited product object
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getEvent()
    {
        return Mage::registry('epace_current_event');
    }

    protected function _prepareLayout()
    {
        if (!$this->getRequest()->getParam('popup')) {
            $this->setChild('back_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label'     => Mage::helper('epace')->__('Back'),
                        'onclick'   => 'setLocation(\''
                            . $this->getUrl('*/*/', array('store'=>$this->getRequest()->getParam('store', 0))).'\')',
                        'class' => 'back'
                    ))
            );
        } else {
            $this->setChild('back_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label'     => Mage::helper('epace')->__('Close Window'),
                        'onclick'   => 'window.close()',
                        'class' => 'cancel'
                    ))
            );
        }

        if (!$this->getRequest()->getParam('popup')) {
            $confirmationMessage = Mage::helper('core')->jsQuoteEscape(
                Mage::helper('epace')->__('Are you sure?')
            );
            $this->setChild('delete_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label'     => Mage::helper('epace')->__('Delete'),
                        'onclick'   => 'confirmSetLocation(\'' . $confirmationMessage
                            . '\', \'' . $this->getDeleteUrl() . '\')',
                        'class'  => 'delete'
                    ))
            );
        }

        return parent::_prepareLayout();
    }

    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current'=>true));
    }

    public function getEventId()
    {
        return $this->getEvent()->getId();
    }

    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array('_current'=>true));
    }

    public function getHeader()
    {
        return $this->escapeHtml($this->getEvent()->getTitle());
    }

    public function getFilesCollection()
    {
        return Mage::getModel('epace/event_file')->getCollection()
            ->addFieldToFilter('event_id', $this->getEventId())
            ->addOrder('id', Varien_Data_Collection_Db::SORT_ORDER_ASC);
    }

    public function getDownloadAction($path)
    {
        return $this->getUrl('*/*/downloadFile/', array('_current'=>true)) . '?file=' . urlencode($path);
    }

    public function getStatisticBlockHtml()
    {
        switch ($this->getEvent()->getName())
        {
            default:
                $block = 'epace/adminhtml_epace_view_statistics_default';
        }
        return $this->getLayout()->createBlock($block)
            ->setEvent($this->getEvent())->toHtml();
    }
}