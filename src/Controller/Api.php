<?php

namespace Platter\Controller;

class Api extends Base
{

    const ERROR_IP_FORBIDDEN = 1001;

    const ERROR_LOST_REQUIRED_PARAMETER = 1002;

    const ERROR_PARAMETER_VALUE_ERROR = 1003;

    const ERROR_API_NOT_EXISTS = 1004;

    const ERROR_ACCESS_DENIED = 1005;

    const ERROR_NEED_REQUEST_METHOD = 1006;

    /**
     * 基本错误信息配置
     * @author tabalt
     * @var array
     */
    protected $apiErrorConfig = array(
        self::ERROR_IP_FORBIDDEN => 'Ip forbidden', 
        self::ERROR_LOST_REQUIRED_PARAMETER => 'Lost required parameter', 
        self::ERROR_PARAMETER_VALUE_ERROR => 'Error parameter value', 
        self::ERROR_API_NOT_EXISTS => 'Api not exists', 
        self::ERROR_ACCESS_DENIED => 'Access denied for this api', 
        self::ERROR_NEED_REQUEST_METHOD => 'Need request method', 
    );

    /**
     * 基本参数列表
     * @author tabalt
     * @var array
     */
    protected $basicParamList = array(
        'app_key', 
        'timestamp', 
        'nonce', 
        'signature'
    );

    /**
     * API运行记录
     * @author tabalt
     * @var array
     */
    protected $runtime = array(
        'start_time' => 0, 
        'end_time' => 0, 
        'error_code' => 0, 
        'error_message' => ''
    );

    /**
     * 初始化Api控制器
     * @author tabalt
     */
    final protected function initApiController()
    {
        // 接口开始时间
        $this->runtime['start_time'] = microtime(true);
        
        // 初始化错误码配置
        $errorConfig = \Platter\Component\Config::get('API_ERROR_CONFIG');
        if (is_array($errorConfig)) {
            foreach ($errorConfig as $key => $value) {
                $this->apiErrorConfig[$key] = $value;
            }
        }
    }

    /**
     * 初始化控制器
     * @author tabalt
     */
    protected function initController()
    {
        // 调用父类的控制器初始化方法
        parent::initController();
        
        // 初始化Api控制器
        $this->initApiController();
    }

    /**
     * success 方法
     * @author tabalt
     * @param string $info 提示信息
     * @param mixed $data 数据
     */
    protected function success($info, $data = false)
    {
        \Platter\Http\Response::outputApi(1, 0, $info, $data);
    }

    /**
     * error 方法
     * @author tabalt
     * @param string $info 错误信息
     * @param int $errorCode 错误码
     * @param var $data 数据
     */
    protected function error($info, $errorCode = 0, $data = false)
    {
        \Platter\Http\Response::outputApi(0, $errorCode, $info, $data);
        exit();
    }

    /**
     * 通过错误码显示错误
     * @author tabalt
     * @param int $code
     * @param string $message
     */
    protected function codeError($code, $message = '')
    {
        if (isset($this->apiErrorConfig[$code])) {
            if (! empty($message)) {
                $message = $this->apiErrorConfig[$code] . ' - ' . $message;
            } else {
                $message = $this->apiErrorConfig[$code];
            }
        } else {
            $message = 'Unknown error';
        }
        // 记录运行时错误信息
        $this->runtime['error_code'] = $code;
        $this->runtime['error_message'] = $message;
        $this->error($message, $code);
    }

    /**
     * 必须参数验证
     * @param array $paramList
     */
    protected function checkRequeiredParamList($method, $paramList)
    {
        if (is_array($paramList)) {
            foreach ($paramList as $param) {
                $paramValue = \Platter\Http\Request::requestValue($method, $param);
                if (empty($paramValue)) {
                    // 缺少必须的参数
                    $this->codeError(self::ERROR_LOST_REQUIRED_PARAMETER, $method . '/' . $param);
                }
            }
        }
    }

    /**
     * 请求有效性认证
     * @author tabalt
     * @param array $appList
     * @param array $apiList
     */
    protected function checkRequestEffective($appList, $apiList)
    {
        // 接口验证
        $api = strtolower($this->moduleName . '/' . $this->controllerName . '/' . $this->actionName);
        $apiInfo = isset($apiList[$api]) ? $apiList[$api] : false;
        
        if (empty($apiInfo)) {
            $this->codeError(self::ERROR_API_NOT_EXISTS);
        }
        
        // app_key验证
        $appKey = isset($_REQUEST['app_key']) ? $_REQUEST['app_key'] : false;
        $appInfo = isset($appList[$appKey]) ? $appList[$appKey] : false;
        
        if (empty($appInfo)) {
            $this->codeError(self::ERROR_PARAMETER_VALUE_ERROR, 'app_key');
        }
        
        // 请求方式验证
        $apiMethod = isset($apiInfo['method']) ? $apiInfo['method'] : false;
        
        if (empty($apiMethod) || ! \Platter\Http\Request::checkRequestMethod($apiMethod)) {
            $this->codeError(self::ERROR_NEED_REQUEST_METHOD, strtoupper($apiMethod));
        }
        
        // 基本参数验证
        $this->checkRequeiredParamList($apiMethod, $this->basicParamList);
        
        // timestamp 时间偏差(精确到秒)不能大于5分钟
        $offset = time() - \Platter\Http\Request::requestValue($apiMethod, 'timestamp');
        if ($offset > 300) {
            $this->codeError(self::ERROR_PARAMETER_VALUE_ERROR, 'timestamp');
        }
        
        // TODO 随机串唯一性认证缓存一个五分钟过期的key
        
        // IP限制，未设置则 不验证
        if (isset($appInfo['ip_limit']) && ! empty($appInfo['ip_limit'])) {
            $ipLimitList = is_array($appInfo['ip_limit']) ? $appInfo['ip_limit'] : explode(",", $appInfo['ip_limit']);
            $clientIp = \Platter\Component\Helper::getClientIp();
            if (! is_array($ipLimitList) || ! in_array($clientIp, $ipLimitList)) {
                // IP禁止访问
                $this->codeError(self::ERROR_IP_FORBIDDEN);
            }
        }
        
        // 接口权限限制，未设置则 不验证
        if (isset($appInfo['api_limit']) && ! empty($appInfo['api_limit'])) {
            $apiLimitList = is_array($appInfo['api_limit']) ? $appInfo['api_limit'] : explode(",", $appInfo['api_limit']);
            if (! is_array($apiLimitList) || ! in_array($api, $apiLimitList)) {
                // 接口禁止访问
                $this->codeError(self::ERROR_ACCESS_DENIED);
            }
        }
        
        // signature 签名认证
        $requestData = \Platter\Http\Request::requestDataList($apiMethod);
        $paramSignature = $requestData['signature'];
        unset($requestData['m'], $requestData['c'], $requestData['a'], $requestData['signature']);
        
        $signature = \Platter\Component\Helper::createSign($requestData, $appInfo['app_secret']);
        
        if ($signature !== $paramSignature) {
            $this->codeError(self::ERROR_PARAMETER_VALUE_ERROR, 'signature');
        }
    }
}