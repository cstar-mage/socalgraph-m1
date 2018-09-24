<?php

class Blackbox_OrderApproval_Model_Rule_Condition_Abstract extends Mage_Rule_Model_Condition_Abstract
{
    protected function _convertIdToName($id)
    {
        return ucfirst(preg_replace_callback('/_([a-zA-Z])/', function ($match) {
            return ' ' . strtoupper($match[1]);
        }, $id));
    }
}