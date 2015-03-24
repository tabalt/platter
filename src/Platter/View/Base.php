<?php

namespace Platter\View;

abstract class Base
{

    /**
     * 模板目录
     * @author tabalt
     * @var array
     */
    protected $viewPath = '';

    /**
     * 构造函数
     * @param string $viewPath
     */
    final public function __construct($viewPath)
    {
        $this->viewPath = $viewPath;
    }

    /**
     * 引入模板变量
     * @author tabalt
     * @param string $key
     * @param mixed $value
     */
    abstract public function assign($key, $value);

    /**
     * 获取渲染后的模板代码
     * @author tabalt
     * @param string $tpl
     * @param boolean $trim
     */
    abstract public function display($tplFile = false, $trim = false, $return = false);
}