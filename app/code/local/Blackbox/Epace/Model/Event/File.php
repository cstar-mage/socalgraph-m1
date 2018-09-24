<?php
 
class Blackbox_Epace_Model_Event_File extends Mage_Core_Model_Abstract {
    protected $fullpath = null;

    protected function _construct() {
        $this->_init('epace/event_file');
    }

    public function getName() {
        return pathinfo($this->getPath())['basename'];
    }



    public function getDownloadName()
    {
        $ext = pathinfo($this->getPath())['extension'];
        if ($ext) {
            $ext = '.' . $ext;
        }
        switch ($this->getType()) {
            case 'statistic':
                return $this->getAction() . $ext;
            case 'response':
            case 'request':
                return $this->getAction() . ' ' . $this->getType() . $ext;
            default:
                return $this->getAction() . $ext;
        }
    }

    public function getDate() {
        return date ("F d Y H:i:s.", filemtime($this->getFullPath()));
    }

    public function getFullPath() {
        if (!$this->fullpath) {
            $this->fullpath =  Mage::helper('epace/event_file')->getFullPath($this->getPath());
        }
        return $this->fullpath;
    }

    public function exists()
    {
        return file_exists($this->getFullPath());
    }

    public function save()
    {
        if (isset($this->_data['content'])) {
            $path = $this->getPath();
            $fileHelper = Mage::helper('epace/event_file'); /* @var Blackbox_Epace_Helper_Event_File $fileHelper*/
            if ($path) {
                if (substr($path, strlen($path) - 1, 1) == '/') {
                    $path .= $fileHelper->createFileName($this->getExt() ? $this->getExt() : 'txt');
                    $this->setPath($path);
                }
            } else {
                $path = $fileHelper->createFileName($this->getExt() ? $this->getExt() : 'txt');
                $this->setPath($path);
            }

            $fileHelper->writeFile($path, $this->_data['content']);
        }
        return parent::save();
    }

    public function delete()
    {
        Mage::helper('epace/event_file')->deleteFile($this->getPath());
        return parent::delete();
    }
}
