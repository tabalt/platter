<?php

namespace Platter\Component;

class ClassLoader
{

    /**
     * 命名空间列表
     * @var array
     */
    protected static $namespaceList = array();

    protected static function getClassFile($className, $baseDir)
    {
        if ($className && $baseDir) {
            $pathList = explode('\\', $className);
            $classFile = $baseDir . implode(DIRECTORY_SEPARATOR, $pathList) . '.php';
            return $classFile;
        }
        return false;
    }

    public static function registerNamespace($prefix, $baseDir)
    {
        $prefix = trim($prefix, '\\');
        $baseDir = rtrim($baseDir, '\\/') . DIRECTORY_SEPARATOR;
        
        self::$namespaceList[$prefix] = $baseDir;
    }

    public static function load($className)
    {
        foreach (self::$namespaceList as $prefix => $baseDir) {
            if (strpos($className, $prefix) === 0) {
                $classFile = self::getClassFile($className, $baseDir);
                //TODO remove test
                //var_dump($className); echo($classFile . "<br />");
                if (file_exists($classFile)) {
                	require $classFile;
                	return true;
                }
                break;
            }
        }
    }

    /**
     * 注册自动加载
     */
    public static function registerAutoLoad()
    {
        spl_autoload_register('Platter\Component\ClassLoader::load');
    }
}