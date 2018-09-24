<?php

namespace Wizkunde\SAMLBase\Configuration;

/**
 * Interface SettingsInterface
 * @package Wizkunde\SAMLBase
 */
interface SettingsInterface
{
    /**
     * @param $value
     * @return mixed
     */
    public function getValue($value);

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function setValue($key, $value);

    /**
     * @return mixed
     */
    public function getValues();

    /**
     * @param array $values
     * @return mixed
     */
    public function setValues($values = array());
}