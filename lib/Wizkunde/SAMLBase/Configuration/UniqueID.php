<?php

namespace Wizkunde\SAMLBase\Configuration;

/**
 * Class UniqueID
 * @package Wizkunde\SAMLBase\Configuration
 */
class UniqueID implements UniqueIDInterface
{
    /**
     * @var string
     */
    protected $prefix = 'SAMLBase';

    /**
     * @param string $prefix
     * @return string
     */
    public function generate($prefix = 'SAMLBase')
    {
        $this->setPrefix($prefix);

        return $this->prefix . sha1(uniqid(mt_rand(), true));
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param string $prefix
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->uniqueID;
    }
}
