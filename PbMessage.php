<?php

class PbMessage
{
    const WIRE_TYPE = 0;
    private $value = null;
    private $pack = false;
    private $required = false;
    private $name = '';
    private $index = 0;
    
    public function setValue($value)
    {
        $this->value = $value;
    }
    
    public function getValue()
    {
        return $this->value;
    }
    
    public function decode($data, &$i)
    {
        
    }
    
    public function encode()
    {
        
    }
}
