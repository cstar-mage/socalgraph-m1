<?php

class Blackbox_Epace_Model_Exception extends Mage_Exception
{
    protected $response = null;

    public function setResponse($response)
    {
        $this->response = $response;
    }


    public function getResponse() {
        return $this->response;
    }
}