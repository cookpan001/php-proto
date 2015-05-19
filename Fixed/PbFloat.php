<?php
class PbFloat
{
    const WIRE_TYPE = 5;
    const BIT_SIZE = 32;
    
    public function decode($data, &$i)
    {
        $len = intval(self::BIT_SIZE / 8);
        $str = substr($data, $i, $len);
        $i += $len;
        $arr = unpack('f', $str);
        if(!empty($arr)){
            return array_pop($arr);
        }
        return null;
    }

    public function encode($field, $value)
    {
        return chr(($field << 3) | self::WIRE_TYPE) . pack('f',$value);
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
PbFloat::test(-2, 1);
PbFloat::test(1234, 1);
PbFloat::test(1234456, 1);
PbFloat::test(123.4456, 1);
PbFloat::test(-1234.456, 1);