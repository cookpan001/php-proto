<?php

abstract class PbInt
{
    const WIRE_TYPE = 0;
    
    public function decode($data, &$i)
    {
        $start = $i;
        $code = ord($data[$i]);
        $arr = array();
        while($code >> 7){
            array_unshift($arr, ($code & 0x7f) << 1);
            ++$i;
            $code = ord($data[$i]);
        }
        array_unshift($arr, $code << 1);
        $result = 0;
        $total = $i - $start;
        foreach($arr as $v){
            $new = ($v << (7 * $total));
            $result += $new;
            $total--;
        }
        
        $dest = (int)($result >> 1);
//        $class = get_called_class();
//        $bits = $class::BIT_SIZE;
//        $flag = ($dest >> ($bits - 1)) & 1;
//        if($flag){
//            echo sprintf("%b\n%b\n", $dest, (-1 << $bits));
//            $dest = (-1 << $bits) ^ $dest;
//            echo sprintf("%b\n", $dest);
//        }
        return $dest;
    }
    
    abstract public function encode($field, $value);
}

