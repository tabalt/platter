<?php

namespace Platter\Http;

class Request
{

    /**
     * 过滤数据
     * @author tabalt
     * @param string $value
     * @param string $filter
     * @param mixed $defaultValue
     */
    protected static function filterValue($value, $filter)
    {
        // 考虑数组的情况
        if (function_exists($filter)) {
            if (is_array($value)) {
                $value = array_map($filter, $value);
            } else {
                $value = $filter($value);
            }
        }
        return $value;
    }

    /**
     * 提交方式判断
     * @author tabalt
     * @param string $type 提交方式
     * @return boolean true/false
     */
    public static function checkRequestMethod($type)
    {
        $typeList = array(
            'post', 
            'get', 
            'head', 
            'delete', 
            'put'
        );
        if (in_array($type, $typeList) && strtolower($_SERVER['REQUEST_METHOD']) === $type) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 是否POST提交数据
     * @author tabalt
     * @return boolean true/false
     */
    public static function isPost()
    {
        return self::checkRequestMethod('post');
    }

    /**
     * 是否GET提交数据
     * @author tabalt
     * @return boolean true/false
     */
    public static function isGet()
    {
        return self::checkRequestMethod('get');
    }

    /**
     * 是否Head提交数据
     * @author tabalt
     * @return boolean true/false
     */
    public static function isHead()
    {
        return self::checkRequestMethod('head');
    }

    /**
     * 是否Delete提交数据
     * @author tabalt
     * @return boolean true/false
     */
    public static function isDelete()
    {
        return self::checkRequestMethod('delete');
    }

    /**
     * 是否Put提交数据
     * @author tabalt
     * @return boolean true/false
     */
    public static function isPut()
    {
        return self::checkRequestMethod('put');
    }

    /**
     * 是否Ajax提交数据
     * @author tabalt
     * @param $ajaxRequestFlagParam ajax请求标识 参数名
     * @return boolean true/false
     */
    public static function isAjax($ajaxRequestFlagParam = 'ajax_request')
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            if ('xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                return true;
            }
        }
        if (isset($_REQUEST[$ajaxRequestFlagParam]) && ! empty($_REQUEST[$ajaxRequestFlagParam])) {
            // 判断Ajax方式提交
            return true;
        }
        return false;
    }

    /**
     * 获取请求的数据
     * @author tabalt
     * @param string $method
     * @return $data
     */
    public static function requestDataList($method)
    {
        $methodList = array(
            'get', 
            'post', 
            'server', 
            'cookie', 
            'session'
        );
        $data = array();
        if (in_array($method, $methodList)) {
            if (isset($GLOBALS['_' . strtoupper($method)])) {
                $data = $GLOBALS['_' . strtoupper($method)];
            }
        }
        return $data;
    }

    /**
     * 获取请求参数的值
     * @author tabalt
     * @param string $method
     * @param string $key
     */
    public static function requestValue($method, $key, $defaultValue = false)
    {
        $data = self::requestDataList($method);
        $value = false;
        if (isset($data[$key])) {
            $value = $data[$key];
        }
        if ($value !== false) {
            return $value;
        } else {
            return $defaultValue;
        }
    }

    /**
     * 从GET参数中取值
     * @author tabalt
     * @param string $key
     * @param mixed $defaultValue
     */
    public static function getValue($key, $defaultValue = false)
    {
        return self::requestValue('get', $key, $defaultValue);
    }

    /**
     * 从POST参数中取值
     * @author tabalt
     * @param string $key
     * @param mixed $defaultValue
     */
    public static function postValue($key, $defaultValue = false)
    {
        return self::requestValue('post', $key, $defaultValue);
    }

    /**
     * 获取过滤后的get参数的值
     * @author tabalt
     * @param string $key
     * @param string $filter
     */
    public static function getFilteredValue($key, $filter, $defaultValue = false)
    {
        $value = self::getValue($key, $defaultValue);
        return self::filterValue($value, $filter);
    }

    /**
     * 获取过滤后的post参数的值
     * @author tabalt
     * @param string $key
     * @param string $filter
     */
    public static function postFilteredValue($key, $filter, $defaultValue = false)
    {
        $value = self::postValue($key, $defaultValue);
        return self::filterValue($value, $filter);
    }
}