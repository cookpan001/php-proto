<?php
class PbDouble
{
    const WIRE_TYPE = 1;
    const BIT_SIZE = 64;
    
    public function decode($data, &$i)
    {
        $len = intval(self::BIT_SIZE / 8);
        $str = substr($data, $i, $len);
        $i += $len;
        $arr = unpack('d', $str);
        if(!empty($arr)){
            return array_pop($arr);
        }
        return null;
    }

    public function encode($field, $value)
    {
        return chr(($field << 3) | self::WIRE_TYPE) . pack('d',$value);
    }
    
    public static function test($i, $intval)
    {
        $app = new self();
        $v = $app->encode($i, $intval);
        $index = 1;
        $ret = $app->decode($v, $index);
        echo "encode: $intval, decode: $ret, ".  base64_encode($v)."\n";
    }
}
PbDouble::test(2, -2.123456);