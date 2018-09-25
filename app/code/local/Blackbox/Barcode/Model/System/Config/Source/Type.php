<?php

class Blackbox_Barcode_Model_System_Config_Source_Type
{
    protected $options;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $result = [];
        foreach ($this->toArray() as $value => $label) {
            $result[] = ['value' => $value, 'label' => $label];
        }
        return $result;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        if (!$this->options) {
            $this->options = [];
            $reflector = new ReflectionClass(\Picqer\Barcode\BarcodeGenerator::class);
            $len = strlen('TYPE_');
            foreach ($reflector->getConstants() as $name => $value) {
                if (substr($name, 0, $len) == 'TYPE_') {
                    $this->options[$value] = $value;
                }
            }
        }
        return $this->options;
    }
}