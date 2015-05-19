<?php

class PbBytes
{
    const WIRE_TYPE = 2;
    
    public function parse($data, &$i)
    {
        $size = ord($data[$i]);
        ++$i;
        $result = substr($data, $i, $size);
        $i += $size;
        return $result;
    }
    
    public function encode($field, $value)
    {
        return chr(($field << 3)| self::WIRE_TYPE) . chr(strlen($value)) . $value;
    }
}