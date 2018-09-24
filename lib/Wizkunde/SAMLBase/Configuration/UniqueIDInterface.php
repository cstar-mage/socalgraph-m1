<?php

namespace Wizkunde\SAMLBase\Configuration;

/**
 * Interface UniqueID
 * @package Wizkunde\SAMLBase\Configuration
 */
interface UniqueIDInterface
{
    /**
     * @param string $prefix
     * @return string
     */
    public function generate($prefix = 'SAMLBase');

    /**
     * @return string
     */
    public function getPrefix();

    /**
     * @param string $prefix
     */
    public function setPrefix($prefix);

    /**
     * @return string
     */
    public function __toString();
}
