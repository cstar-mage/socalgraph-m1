<?php

namespace Wizkunde\SAMLBase\Configuration;

class Metadata implements MetadataInterface
{
    /**
     * @return string with xml data
     */
    public function getMetadata()
    {
        $metadataTemplate = $this->getContainer()->get('twig')->render('Metadata.xml.twig',
            array(
                'UniqueID' => $this->getContainer()->get('samlbase_unique_id_generator')->generate(),
                'Timestamp' => $this->getContainer()->get('samlbase_timestamp_generator')->generate()->toFormat(),
            )
        );

        return (string)$metadataTemplate;
    }
}
