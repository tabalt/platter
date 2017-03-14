<?php

namespace Platter\Component;

class Config
{
    /**
     * 默认配置，配置值可被项目配置覆盖
     * @author tabalt
     */
    protected static $defaultConfigList = array(
        'TEMPLATE_ENGINE' => 'Simple',
        'TEMPLATE_SUFFIX' => 'php',
        'REMOVE_TPL_SPACES' => false,
        'IS_DEBUG' => false,
    );
    
    /**
     * @author tabalt
     * 配置列表
     */
    protected static $configList = array();

    /**
     * 获取配置项
     * @author tabalt
     * @param string $key
     */
    public static function get($key)
    {
        $key = strtolower($key);
        if (isset(self::$configList[$key])) {
            return self::$configList[$key];
        } else {
            return null;
        }
    }

    /**
     * 设置配置项
     * @author tabalt
     * @param string $key
     * @param mixed $value
     */
    public static function set($key, $value)
    {
        $key = strtolower($key);
        self::$configList[$key] = $value;
    }

    /**
     * 解析配置文件
     * @author tabalt
     * @param string $configFile
     */
    public static function parseFile($configFile)
    {
        $config = \Platter\Component\File::requireFile($configFile, true);
        if (!is_array($config)) {
            $config = array();
        }
        
        $defaultConfig = array_change_key_case(self::$defaultConfigList, CASE_LOWER);
        $config = array_change_key_case($config, CASE_LOWER);
        
        self::$configList = array_merge($defaultConfig, $config);
        
    }
}