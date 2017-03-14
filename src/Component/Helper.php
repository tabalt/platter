<?php

namespace Platter\Component;

class Helper
{

    /**
     * 验证Refer，防范csrf攻击
     * @author tabalt
     * @param mixed $whiteHostList 白名单域名或ip
     * @return boolean true/false
     */
    public static function checkReferer($whiteHostList = array())
    {
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : false;
        if (empty($referer)) {
            return false;
        }
        // referer 必须以 http 或 https 开头
        if (strpos($referer, 'http://') !== 0 && strpos($referer, 'https://') !== 0) {
            return false;
        }
        // 设置默认域名
        if (empty($whiteHostList)) {
            $whiteHostList = array(
                $_SERVER['HTTP_HOST']
            );
        } else if (is_string($whiteHostList)) {
            $whiteHostList = array(
                $whiteHostList
            );
        }
        // refer 主机地址判断
        $refererHost = parse_url($referer, PHP_URL_HOST);
        if (is_array($whiteHostList) && in_array($refererHost, $whiteHostList)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 输出http状态码
     * @author tabalt
     * @param intval $code
     */
    public static function httpStatus($code)
    {
        $codeList = array(
            100 => 'Continue', 
            101 => 'Switching Protocols', 
            200 => 'OK', 
            201 => 'Created', 
            202 => 'Accepted', 
            203 => 'Non-Authoritative Information', 
            204 => 'No Content', 
            205 => 'Reset Content', 
            206 => 'Partial Content', 
            300 => 'Multiple Choices', 
            301 => 'Moved Permanently', 
            302 => 'Found', 
            303 => 'See Other', 
            304 => 'Not Modified', 
            305 => 'Use Proxy', 
            307 => 'Temporary Redirect', 
            400 => 'Bad Request', 
            401 => 'Unauthorized', 
            402 => 'Payment Required', 
            403 => 'Forbidden', 
            404 => 'Not Found', 
            405 => 'Method Not Allowed', 
            406 => 'Not Acceptable', 
            407 => 'Proxy Authentication Required', 
            408 => 'Request Timeout', 
            409 => 'Conflict', 
            410 => 'Gone', 
            411 => 'Length Required', 
            412 => 'Precondition Failed', 
            413 => 'Request Entity Too Large', 
            414 => 'Request-URI Too Long', 
            415 => 'Unsupported Media Type', 
            416 => 'Requested Range Not Satisfiable', 
            417 => 'Expectation Failed', 
            500 => 'Internal Server Error', 
            501 => 'Not Implemented', 
            502 => 'Bad Gateway', 
            503 => 'Service Unavailable', 
            504 => 'Gateway Timeout', 
            505 => 'HTTP Version Not Supported'
        );
        $code = intval($code);
        if (array_key_exists($code, $codeList)) {
            header('HTTP/1.1 ' . $code . ' ' . $codeList[$code]);
            header('status: ' . $code . ' ' . $codeList[$code]);
        }
    }

    /**
     * 获取客户端IP
     * PHP 5 >= 5.2.0
     * @author tabalt
     * @param boolean $onlyRemoteAddr 是否直接返回REMOTE_ADDR
     * @return string/boolean $clientIp
     */
    public static function getClientIp($onlyRemoteAddr = true)
    {
        // 是否直接返回REMOTE_ADDR
        if ($onlyRemoteAddr) {
            return $_SERVER['REMOTE_ADDR'];
        }
        
        // 验证是否为非私有IP
        if (filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)) {
            return $_SERVER['REMOTE_ADDR'];
        }
        
        // 验证HTTP头中是否有HTTP_X_FORWARDED_FOR
        if (! isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }
        
        // 定义客户端IP
        $clientIp = '';
        
        // 获取", "的位置
        $position = strrpos($_SERVER['HTTP_X_FORWARDED_FOR'], ', ');
        
        // 验证$position
        if (false === $position) {
            $clientIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $clientIp = substr($_SERVER['HTTP_X_FORWARDED_FOR'], $position + 2);
        }
        
        // 验证$clientIp是否为合法IP
        if (filter_var($clientIp, FILTER_VALIDATE_IP)) {
            return $clientIp;
        } else {
            return false;
        }
    }

    /**
     * 将gbk编码转换为utf-8编码,参数可以是字符串或者多维数组
     * @author pengming
     * @param string | array $transcoding
     */
    public static function gbk2Utf8($transcoding)
    {
        // 转码字符串
        if (is_string($transcoding)) {
            return mb_convert_encoding($transcoding, 'UTF-8', 'GBK');
        }
        // 转码数组
        if (is_array($transcoding)) {
            $data = array();
            foreach ($transcoding as $key => $value) {
                $data[$key] = self::gbk2Utf8($value);
            }
            return $data;
        }
    }

    /**
     * 求字符串的长度 中文算一个字符
     * @author tabalt
     * @param string $str
     */
    public static function stringLength($str)
    {
        if (empty($str)) {
            return 0;
        }
        if (function_exists('mb_strlen')) {
            return mb_strlen($str, 'utf-8');
        } else {
            preg_match_all("/./u", $str, $ar);
            return count($ar[0]);
        }
    }

    /**
     * 获取二维数组的指定列
     * @author tabalt
     * @param array $array 一般为数据库查询出的结果数组
     * @param string $key
     * @return array $data 处理后的结果
     */
    public static function getArrayColumn($array, $key)
    {
        $data = array();
        foreach ($array as $info) {
            if (isset($info[$key])) {
                $data[] = $info[$key];
            }
        }
        return $data;
    }

    /**
     * 获取按新key重置的数组
     * @author tabalt
     * @param array $array 一般为数据库查询出的结果数组
     * @param string $key
     * @return array $data 处理后的结果
     */
    public static function getResetArrayByKey($array, $key)
    {
        $data = array();
        foreach ($array as $info) {
            if (isset($info[$key])) {
                $data[$info[$key]] = $info;
            }
        }
        return $data;
    }

    /**
     * 创建签名
     * @author tabalt
     * @param array $data 要传递的数据
     * @param string $appSecret 密钥
     * @return string $sign 生成的签名串
     */
    public static function createSign($data, $appSecret)
    {
        ksort($data);
        $sign = '';
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $sign .= self::createSign($value, $appSecret);
            } else {
                $sign .= $value;
            }
        }
        $sign = md5($sign . $appSecret);
        return $sign;
    }
}