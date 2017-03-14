<?php

namespace Platter\Component;

class Error
{

    protected static $errorTypeList = array(
        1 => 'E_ERROR', 
        2 => 'E_WARNING', 
        4 => 'E_PARSE', 
        8 => 'E_NOTICE', 
        16 => 'E_CORE_ERROR', 
        32 => 'E_CORE_WARNING', 
        64 => 'E_COMPILE_ERROR', 
        128 => 'E_COMPILE_WARNING', 
        256 => 'E_USER_ERROR', 
        512 => 'E_USER_WARNING', 
        1024 => 'E_USER_NOTICE', 
        2048 => 'E_STRICT', 
        4096 => 'E_RECOVERABLE_ERROR', 
        8192 => 'E_DEPRECATED', 
        16384 => 'E_USER_DEPRECATED'
    );

    /**
     * 错误处理函数
     * @author tabalt
     * @param int $error_number
     * @param string $error_string
     * @param string $error_file
     * @param int $error_line
     * @throws Exception
     * @return boolean
     */
    public static function handler($error_number, $error_string, $error_file, $error_line)
    {
        if (! (error_reporting() & $error_number)) {
            return;
        }
        
        $error_type = 'UNKNOWN';
        if (isset(self::$errorTypeList[$error_number])) {
            $error_type = self::$errorTypeList[$error_number];
        }
        
        $message = "{$error_string} file: {$error_file} line: {$error_line}";
        throw new \Exception($message, $error_number);
        
        // \Platter\Component\Logger::debug($message);
        return true;
    }
}