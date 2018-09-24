<?php

namespace Wizkunde\SAMLBase\Configuration;

use Wizkunde\SAMLBase\Configuration;

interface MetadataInterface
{
    /**
     * @return string with xml data
     */
    public function getMetadata();
}
