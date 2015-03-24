<?php

namespace Platter\Http;

class Response
{

    /**
     * 跳转到指定的url
     * @param string $url
     */
    public static function redirect($url)
    {
        if (empty($url)) {
            $url = '/';
        }
        header("Location: $url");
        exit();
    }

    /**
     * 输出内容
     * @author tanyanping
     * @param string $content 输出内容
     */
    public static function output($content)
    {
        echo $content;
    }

    /**
     * 输出json格式的数据
     * @param array $data 要输出json的数组
     */
    public static function outputJson($data)
    {
        // header('Content-Type:application/json; charset=utf-8');
        $content = json_encode($data);
        self::output($content);
    }

    /**
     * 输出api数据数据
     * @author tanyanping
     * @param int $status 0/1 成功与失败状态
     * @param int $errorCode 错误码
     * @param string $info 说明信息
     * @param mixed $data 返回的数据
     */
    public static function outputApi($status, $errorCode, $info, $data)
    {
        $result = array(
            'status' => $status, 
            'error_code' => $errorCode, 
            'info' => $info, 
            'data' => $data
        );
        self::outputJson($result);
    }
}