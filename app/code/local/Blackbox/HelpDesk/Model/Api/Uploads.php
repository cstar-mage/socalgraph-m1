<?php

class Blackbox_HelpDesk_Model_Api_Uploads extends Zendesk_Zendesk_Model_Api_Abstract
{
    protected $transactionLevel = 0;

    public function uploads($fileName, $filePath, $token = null)
    {
        $endpoint = 'uploads.json';

        $params = ['filename' => $fileName];
        if ($token) {
            $params['token'] = $token;
        }

        $args = array();
        foreach($params as $arg => $val) {
            $args[] = urlencode($arg) . '=' . urlencode($val);
        }
        $endpoint .= '?' . implode('&', $args);

        $url = $this->_getUrl($endpoint);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERPWD, $this->getUsername().':'.$this->getPassword());
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/binary'));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION ,1);
        curl_setopt($ch, CURLOPT_HEADER, 0); // DO NOT RETURN HTTP HEADERS
        curl_setopt($ch, CURLOPT_RETURNTRANSFER ,1); // RETURN THE CONTENTS OF THE CALL
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($filePath));
        curl_setopt($ch, CURLOPT_USERAGENT, "PHP_HTTP_CLIENT");
        $output = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($code !== 200 && $code !== 201)
        {
            return 'Status code returned was '.$code.'!';
        }

        $decoded = json_decode($output, true);
        return $decoded;
    }

    public function beginTransaction()
    {
        $this->transactionLevel++;
    }

    public function commit()
    {
        if (--$this->transactionLevel < 0) {
            $this->transactionLevel = 0;
        }
    }

    public function rollBack()
    {
        if (--$this->transactionLevel < 0) {
            $this->transactionLevel = 0;
        }
    }

    public function getTransactionLevel()
    {
        return $this->transactionLevel;
    }
}