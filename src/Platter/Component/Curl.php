<?php

namespace Platter\Component;

class Curl {

    /**
     * 初始化CURL选项列表
     * @author tabalt
     * @var array
     */
    private static $initOptionList = array(
        //发起连接最长等待时间(s)
        CURLOPT_CONNECTTIMEOUT => 30, 
        //最长请求时间(s)
        CURLOPT_TIMEOUT => 60, 
        //将curl_exec()获取的信息以文件流的形式返回
        CURLOPT_RETURNTRANSFER => 1, 
        //每次将强制请求一个新连接代替缓存中的连接
        CURLOPT_FRESH_CONNECT => true
    );

    /**
     * 用户自定义CURL选项列表
     * @author tabalt
     * @var array
     */
    private static $optionList = array();

    /**
     * CURL结果
     * @author tabalt
     * @var array
     */
    private static $curlResult;

    /**
     * 设置http请求方式
     * @author tabalt
     */
    private static function httpMethod($method) {
        self::setOption('CUSTOMREQUEST', $method);
    }

    /**
     * 发送CURL请求
     * @author tabalt
     * @param sting $url 请求地址
     * @return boolean|mixed 请求成功返回结果否则返回false
     */
    private static function request() {
        $ch = curl_init();
        curl_setopt_array($ch, self::$initOptionList);
        curl_setopt_array($ch, self::$optionList);
        $result = curl_exec($ch);
        $curlResult = array(
            'httpCode' => curl_getinfo($ch, CURLINFO_HTTP_CODE), 
            'fileTime' => curl_getinfo($ch, CURLINFO_FILETIME), 
            'totalTime' => curl_getinfo($ch, CURLINFO_TOTAL_TIME), 
            'nameLookupTime' => curl_getinfo($ch, CURLINFO_NAMELOOKUP_TIME), 
            'connectTime ' => curl_getinfo($ch, CURLINFO_CONNECT_TIME), 
            'perTransferTime' => curl_getinfo($ch, CURLINFO_PRETRANSFER_TIME), 
            'startTransferTime' => curl_getinfo($ch, CURLINFO_STARTTRANSFER_TIME), 
            'redirectTime' => curl_getinfo($ch, CURLINFO_REDIRECT_TIME)
        );
        if (!$result) {
            $curlResult['errNo'] = curl_errno($ch);
            $curlResult['errMsg'] = curl_error($ch);
        }
        curl_close($ch);
        self::$curlResult = $curlResult;
        //每次请求后清空配置信息
        self::$optionList = array();
        return $result;
    }

    /**
     * 设置一个CURL选项
     * @author tabalt
     * @param sting $name 设置CURL选项常量名后缀
     * @param sting $value 设置句柄选项值
     */
    public static function setOption($name, $value) {
        self::$optionList[constant('CURLOPT_' . strtoupper($name))] = $value;
    }

    /**
     * 设置多个CURL选项
     * @author tabalt
     * @param  array $setting
     */
    public static function setOptionArray($setting = array()) {
        foreach ($setting as $name => $value) {
            self::setOption($name, $value);
        }
    }

    /**
     * 设置默认值
     * @author tabalt
     */
    public static function setDetaults() {
        self::$optionList = array();
    }

    //TODO 默认超时时间 3000ms
    /**
     * 发送CURL GET请求
     * @author tabalt
     * @param string $url 请求地址
     * @param array $data 请求数据
     * @return boolean|Ambigous <boolean, mixed>  返回请求的数据
     */
    public static function get($url, $data = array()) {
        if (!empty($data)) {
            $url .= (strpos($url, '?') === false) ? '?' : '&';
            $url .= http_build_query($data);
        }
        self::httpMethod('GET');
        self::setOption('URL', $url);
        self::setOption('HTTPGET', true);
        return self::request();
    }

    /**
     * 发送CURL POST请求
     * @author tabalt
     * @param string $url 请求地址
     * @param array  $data 请求数据
     */
    public static function post($url, $data = array()) {
        self::httpMethod('POST');
        self::setOption('URL', $url);
        self::setOption('POST', true);
        self::setOption('POSTFIELDS', http_build_query($data));
        return self::request();
    }

    /**
     * 发送CURL PUT请求
     * @author tabalt
     * @param string $url 请求地址
     * @param array $file 发送的文件地址
     */
    public static function put($url, $file) {
        self::httpMethod('PUT');
        self::setOption('URL', $url);
        self::setOption('PUT', true);
        //TODO put 请求
        //self::setOption('INFILE', $file);
        //self::setOption('INFILESIZE', $file);
        return self::request();
    }

    /**
     * 发送CURL DELETE请求
     * @author tabalt
     * @param string $url 请求地址
     */
    public static function delete($url) {
        self::httpMethod('DELETE');
        self::setOption('URL', $url);
        self::setOption('CUSTOMREQUEST', 'DELETE');
        return self::request();
    }

    /**
     * 获取CURL请求结果
     * 不传参数key则返回所有信息，参数key区分大小写
     * 参数key的可选值：
     * httpCode				HTTP状态码
     * fileTime				远程获取文档的时间，如果无法获取，则返回值为“-1”
     * totalTime			最后一次传输所消耗的时间
     * nameLookupTime		名称解析所消耗的时间
     * connectTime			建立连接所消耗的时间
     * perTransferTime		从建立连接到准备传输所使用的时间
     * startTransferTime	从建立连接到传输开始所使用的时间
     * redirectTime			在事务传输开始前重定向所使用的时间
     * errNo 				错误号
     * errMsg 				错误信息
     * @author tabalt
     * @return mixed
     */
    public static function getResult($key = '') {
        if (empty($key)) {
            return self::$curlResult;
        }
        if (isset(self::$curlResult[$key])) {
            return self::$curlResult[$key];
        } else {
            return false;
        }
    }

    /**
     * 获取HTTP状态码
     * @author tabalt
     * @return string
     */
    public static function getHttpCode() {
        return self::getResult('httpCode');
    }

    /**
     * 获取CURL错误代码
     * @author tabalt
     * @return string
     */
    public static function getErrNo() {
        return self::getResult('errNo');
    }

    /**
     * 获取CURL错误信息
     * @author tabalt
     * @return string
     */
    public static function getError() {
        return self::getResult('errMsg');
    }

    //TODO add function for set cookie,user_agent,headers,basic auth etc.
}
