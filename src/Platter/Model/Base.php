<?php

namespace Platter\Model;

abstract class Base
{

    /**
     * 名称
     * @author tabalt
     * @var sting
     */
    protected $name;

    /**
     * 错误信息
     * @var string
     */
    protected $errorMessage = '';

    /**
     * 获取类名
     */
    protected function getClassName()
    {
        $fullClassName = get_class($this);
        $itemList = explode("\\", $fullClassName);
        $className = array_pop($itemList);
        
        return $className;
    }

    /**
     * 驼峰 命名 转换
     * $type 0 ：UserRole => user_role
     * $type 1 ：user_role => UserRole
     * @author tabalt
     * @param string $name
     * @param int $type
     * @return string
     */
    function formatName($name, $type = 0)
    {
        if ($type) {
            return ucfirst(preg_replace("/_([a-zA-Z])/e", "strtoupper('\\1')", $name));
        } else {
            return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
        }
    }

    /**
     * 初始化Base Model
     */
    public function __construct($name = null)
    {
        $this->setName($name);
    }

    /**
     * 设置模型名
     */
    public function setName($name)
    {
        // 初始化名字
        $name = empty($name) ? $this->getClassName() : $name;
        $this->name = $this->formatName($name);
    }

    /**
     * 获取模型名
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 设置错误信息
     * @author tabalt
     * @param string $errorMessage 错误信息
     */
    public function setError($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * 获取错误信息
     * @author tabalt
     * @return string $errorMessage 错误信息
     */
    public function getError()
    {
        return $this->errorMessage;
    }
}