<?php

namespace Platter\Component;

class File
{
    public static function isExist($filePath)
    {
        if ($filePath && file_exists($filePath)) {
            return true;
        }
        return false;
    }
    
    public static function requireFile($filePath, $returnResult = false)
    {
        if (self::isExist($filePath)) {
            $result = require $filePath;
            if ($returnResult) {
                return $result;
            }
            return true;
        }
        return false;
    }
    
    public static function getContents($filePath)
    {
        if (self::isExist($filePath)) {
            return file_get_contents($filePath);
        }
        return false;
    }
    
    public static function putContents($filePath, $content)
    {
        if (self::isExist($filePath)) {
            return file_put_contents($filePath, $content);
        }
        return false;
    }
    
    /**
     * 异步写入文件
     * @author tabalt
     * @param string $filePath
     * @param string $content
     * @return void
     */
    public static function asyncWrite($filePath, $content, $type = 'w+') {
        $fileHandle = fopen($filePath, $type);
        if ($fileHandle) {
            $startTime = microtime(true);
            do {
                $locked = flock($fileHandle, LOCK_EX);
                if (!$locked) {
                    usleep(round(rand(0, 100) * 1000));
                }
            } while ((!$locked) && ((microtime(true) - $startTime) < 1000));
    
            if ($locked) {
                fwrite($fileHandle, $content);
                flock($fileHandle, LOCK_UN);
            }
    
            fclose($fileHandle);
        }
    }
}