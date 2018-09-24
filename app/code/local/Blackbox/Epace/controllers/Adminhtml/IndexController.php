<?php

class Blackbox_Epace_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        parent::__construct($request, $response, $invokeArgs);
    }

    protected function _initAction() {
        $this
            ->loadLayout()
            ->_setActiveMenu('epacemenu/epace');

        return $this;
    }

    public function indexAction() {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('epace/grid'))
            ->renderLayout();
    }

    public function testConnectionAction()
    {
        $api = Mage::helper('epace/api'); /* @var Blackbox_Epace_Helper_Api $api*/

        $event = Mage::getModel('epace/event')
            ->setData(array(
                'name' => 'Test Connection',
                'processed_time' => time(),
                'status' => Blackbox_Epace_Model_Event::STATUS_CRITICAL,
                'username' => $api->getUsername(),
                'password' => $api->getPassword(),
                'host' => $api->getHost(),
            ));
        $event->save();
        $api->setEvent($event);

        $in1 = urldecode($this->getRequest()->getParam('in1'));
        $in2 = urldecode($this->getRequest()->getParam('in2'));

        try {
            $result = $api->findObjects($in1, $in2);
            if ($result) {
                Mage::getSingleton('adminhtml/session')->addSuccess('Connection tested successfully');
                $event->setStatus(Blackbox_Epace_Model_Event::STATUS_SUCCESS);
            } else {
                Mage::getSingleton('adminhtml/session')->addError('Not valid response');
                $event->setStatus(Blackbox_Epace_Model_Event::STATUS_WITH_ERRORS);
                $event->setSerializedData(serialize(array('error' => 'Not valid response')));
            }
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $event->setStatus(Blackbox_Epace_Model_Event::STATUS_WITH_ERRORS);
            $event->setSerializedData(serialize(array('error' => $e->getMessage())));
        }
        $event->save();

        $this->_redirect('adminhtml/system_config/edit/section/epace');
    }

    public function testCreateJobAction()
    {
        $api = Mage::helper('epace/api'); /* @var Blackbox_Epace_Helper_Api $api*/

        $event = Mage::getModel('epace/event')
            ->setData(array(
                'name' => 'Test Create Job',
                'processed_time' => time(),
                'status' => Blackbox_Epace_Model_Event::STATUS_CRITICAL,
                'username' => $api->getUsername(),
                'password' => $api->getPassword(),
                'host' => $api->getHost(),
            ));
        $event->save();
        $api->setEvent($event);

        $customer = urldecode($this->getRequest()->getParam('customer'));
        $description = urldecode($this->getRequest()->getParam('description'));

        try {
            $result = $api->createJob($customer, 'TG Test Order '.$customer, array('jobType'=>'7', 'shipToJobContact'=> Mage::getStoreConfig('epace/main_settings/contact_id')));
            if ($result) {
                Mage::getSingleton('adminhtml/session')->addSuccess('Job was created successfully');
                $event->setStatus(Blackbox_Epace_Model_Event::STATUS_SUCCESS);

                $response = '<div>Response:<div><div><ul>';
                foreach ($result as $key => $value) {
                    $response .= '<li>' . $key . ' = ' . $value . '</li>';
                }
                $response .= '</ul></div>';
                Mage::getSingleton('adminhtml/session')->addSuccess($response);

            } else {
                Mage::getSingleton('adminhtml/session')->addError('Not valid response');
                $event->setStatus(Blackbox_Epace_Model_Event::STATUS_WITH_ERRORS);
                $event->setSerializedData(serialize(array('error' => 'Not valid response')));
            }
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $event->setStatus(Blackbox_Epace_Model_Event::STATUS_WITH_ERRORS);
            $event->setSerializedData(serialize(array('error' => $e->getMessage())));
        }
        $event->save();

        $this->_redirect('adminhtml/system_config/edit/section/epace');
    }

    public function testCreateEmployeeTimeAction()
    {
        $api = Mage::helper('epace/api'); /* @var Blackbox_Epace_Helper_Api $api*/

        $event = Mage::getModel('epace/event')
            ->setData(array(
                'name' => 'Test Create Employee Time',
                'processed_time' => time(),
                'status' => Blackbox_Epace_Model_Event::STATUS_CRITICAL,
                'username' => $api->getUsername(),
                'password' => $api->getPassword(),
                'host' => $api->getHost(),
            ));
        $event->save();
        $api->setEvent($event);

        $employee = urldecode($this->getRequest()->getParam('employee'));
        $startDate = urldecode($this->getRequest()->getParam('startDate'));
        $startTime = urldecode($this->getRequest()->getParam('startTime'));
        $stopDate = urldecode($this->getRequest()->getParam('stopDate'));
        $stopTime = urldecode($this->getRequest()->getParam('stopTime'));

        try {
            $result = $api->createEmployeeTime($employee, $startDate, $startTime, $stopDate, $stopTime);
            if ($result) {
                Mage::getSingleton('adminhtml/session')->addSuccess('Employee Time was created successfully');
                $event->setStatus(Blackbox_Epace_Model_Event::STATUS_SUCCESS);

                $response = '<div>Response:<div><div><ul>';
                foreach ($result as $key => $value) {
                    $response .= '<li>' . $key . ' = ' . $value . '</li>';
                }
                $response .= '</ul></div>';
                Mage::getSingleton('adminhtml/session')->addSuccess($response);

            } else {
                Mage::getSingleton('adminhtml/session')->addError('Not valid response');
                $event->setStatus(Blackbox_Epace_Model_Event::STATUS_WITH_ERRORS);
                $event->setSerializedData(serialize(array('error' => 'Not valid response')));
            }
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $event->setStatus(Blackbox_Epace_Model_Event::STATUS_WITH_ERRORS);
            $event->setSerializedData(serialize(array('error' => $e->getMessage())));
        }
        $event->save();

        $this->_redirect('adminhtml/system_config/edit/section/epace');
    }

    public function deleteAction()
    {
        $id = (int)$this->getRequest()->getParam('id');

        if ($id) {
            Mage::getModel('epace/event')->load($id)->delete();
        }

        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $eventIds = $this->getRequest()->getParam('ids');

        $session = Mage::getSingleton('adminhtml/session');
        if(!is_array($eventIds)) {
            $session->addError(Mage::helper('adminhtml')->__('Please select event(s).'));
        } else {
            try {
                foreach ($eventIds as $eventId) {
                    $model = Mage::getModel('epace/event')->load($eventId);
                    $model->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) have been deleted.', count($eventId))
                );
            } catch (Mage_Core_Exception $e) {
                $session->addError($e->getMessage());
            } catch (Exception $e){
                $session->addException($e, Mage::helper('adminhtml')->__('An error occurred while deleting record(s).'));
            }
        }

        $this->_redirect('*/*/' . $this->getRequest()->getParam('ret', 'index'));
    }

    public function viewAction()
    {
        $id  = (int)$this->getRequest()->getParam('id');

        $event = Mage::getModel('epace/event')->load($id);

        if (!$event->getId()) {
            $this->_getSession()->addError(Mage::helper('epace')->__('This event no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        Mage::register('epace_current_event', $event);

        $this->_title($event->getText());

        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('epace/adminhtml_epace_view'))
            ->renderLayout();
    }

    public function downloadFileAction()
    {
        $file = $this->getRequest()->getParam('file');
        $name = $this->getFileNameByPath($file);
        $path = Mage::helper('epace/event_file')->getFullPath($file);
        if (file_exists($path)) {
            try {
                $this->_prepareDownloadResponse($name, array('type' => 'filename', 'value' => $path));
            } catch (Exception $e) {

            }
        }
    }

    protected function getFileNameByPath($path)
    {
        $file = Mage::getModel('epace/event_file')->getCollection()
            ->addFieldToFilter('path', $path)->getFirstItem();

        if ($file && $file->getId()) {
            return $file->getDownloadName();
        }

        return pathinfo($path)['basename'];
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/epace');
    }

    protected function _prepareDownloadResponse(
        $fileName,
        $content,
        $contentType = 'application/octet-stream',
        $contentLength = null)
    {
        parent::_prepareDownloadResponse($fileName, $content, $contentType, $contentLength);
        $this->getResponse()
            ->setHeader('Content-Disposition', 'inline; filename="'.$fileName.'"', true);
        return $this;
    }
}