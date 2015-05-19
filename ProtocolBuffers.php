<?php

class ProtocolBuffers
{
    const WIRED_VARINT = 0;
    const WIRED_64BIT = 1;
    const WIRED_LENGTH_DELIMITED = 2;
    const WIRED_START_GROUP = 3;
    const WIRED_END_GROUP = 4;
    const WIRED_32BIT = 5;
    
    public static $wireType = array(
        self::WIRED_VARINT => 'VarInt',
        self::WIRED_64BIT => '64Bit',
        self::WIRED_LENGTH_DELIMITED => 'Delimited',
        self::WIRED_START_GROUP => 'Start',
        self::WIRED_END_GROUP => 'End',
        self::WIRED_32BIT => '32Bit',
    );
    
    private function parseVarInt($data, &$i)
    {
        $start = $i;
        $code = ord($data[$i]);
        $arr = array();
        while($code >> 7){
            array_unshift($arr, $code << 1);
            ++$i;
            $code = ord($data[$i]);
        }
        array_unshift($arr, $code << 1);
        $result = 0;
        $total = $i - $start;
        foreach($arr as $v){
            $result += ($v << (7 * $total));
            $total--;
        }
        ++$i;
        return $result >> 1;
    }
    
    function parseInt32($data, &$i)
    {
        return $this->parseVarInt($data, $i);
    }
    
    function parseInt64($data, &$i)
    {
        return $this->parseVarInt($data, $i);
    }
    
    function parseUint32($data, &$i)
    {
        return $this->parseVarInt($data, $i);
    }
    
    function parseUint64($data, &$i)
    {
        return $this->parseVarInt($data, $i);
    }
    
    function parseSint32($data, &$i)
    {
        $n = $this->parseVarInt($data, $i);
        return ($n >> 1) ^ -($n & 1);
    }
    
    function parseSint64($data, &$i)
    {
        $n = $this->parseVarInt($data, $i);
        return ($n >> 1) ^ -($n & 1);
    }

    function parseBytes($data, &$i)
    {
        $size = ord($data[$i]);
        ++$i;
        $result = substr($data, $i, $size);
        $i += $size;
        return $result;
    }
    
    function parseString($data, &$i)
    {
        $size = ord($data[$i]);
        ++$i;
        $result = substr($data, $i, $size);
        $i += $size;
        return $result;
    }
    
    function parseMessage($data, $fileds, $values)
    {
        $len = strlen($data);
        $i = 0;
        $result = array();
        while($i < $len){
            $first = ord($data[$i]);
            $field = $first >> 3;
            $wireType = $first & 7;
            $method = 'parse'.self::$wireType[$wireType];
            ++$i;
            //echo "field:$field, wireType: $wireType, before:$i, ";
            if($wireType == self::WIRED_LENGTH_DELIMITED){
                $size = ord($data[$i]);
                $str = substr($data, $i + 1, $size);
                $i += $size + 1;
                if(is_array($fileds[$field])){//embeded messages
                    $value = $this->parseMessage($str, $fileds[$field], array());
                }else if($fileds[$field] == 'string' || $fileds[$field] == 'bytes'){
                    $value = $str;
                }else{
                    //packed
                }
            }else{
                $value = $this->$method($data, $i, $fileds[$field]);
            }
            if(isset($values[$field]) && is_array($values[$field])){
                $result[$field][] = $value;
            }else{
                $result[$field] = $value;
            }
            //echo "value: $value, after:$i\n";
        }
        return $result;
    }
    
    function test()
    {
        $fileds = array(
            1 => 'int32',
            2 => 'int64',
            3 => array(1 => 'string',2 => 'string'),//Embedded Messages
            4 => 'string',
        );
        $values = array(
            1 => '',
            2 => array(),//repeated
            3 => array(),//repeated
        );
        $data = base64_decode('CAgQBBA4EAUaDAoHaW5jUmF0ZRIBMA==');
        $result = $this->parseMessage($data, $fileds, $values);
        return $result;
    }
}
$app = new ProtocolBuffers();
$result = $app->test();
var_dump($result);