<?php
include 'PbInt.php';
class PbInt32 extends PbInt
{
    const BIT_SIZE = 32;
    
    public function decode($data, &$i)
    {
        return parent::decode($data, $i);
    }
    
    public function encode($field, $intval)
    {
        $bits = self::BIT_SIZE;
        $ret = array();
        $result = '';
        $flag = (($intval >> (self::BIT_SIZE - 1)) & 1);
        $value = ($intval & ~(-1 << (self::BIT_SIZE - 1))) | ($flag << (self::BIT_SIZE - 1));
        echo "intval: $intval, flag: $flag, value: $value\n";
        while($bits > 0 && $value){
            if($bits > 7){
                $v = $value & 0x7f;
            } else {
                $v = $value & (~(-1 << $bits));
            }
            $ret[] = $v;
            $bits -= 7;
            $value = $value >> 7;
        }
        $index = 1;
        foreach($ret as $item){
            echo sprintf("encode: %d, %b\n", $item, $item);
            if($index < count($ret)){
                $item = $item | 0x80;
            }
            $result .= chr($item);
            ++$index;
        }
        if(empty($result)){
            $result = chr(0);
        }
        return chr(($field << 3) | self::WIRE_TYPE) . $result;
    }
    
    public static function test($intval, $i)
    {
        $app = new self();
        $v = $app->encode($i, $intval);
        var_dump(base64_encode($v));
        $index = 1;
        $ret = $app->decode($v, $index);
        echo "encode: $intval, decode: $ret\n";
    }
}

PbInt32::test(123, 1);
