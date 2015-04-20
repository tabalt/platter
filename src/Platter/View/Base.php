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
     * 临时文件目录
     * @author tabalt
     * @var string
     */
    protected $tmpPath = '';

    /**
     * 构造函数
     * @param string $viewPath
     */
    public function __construct($viewPath, $tmpPath = false)
    {
        $this->viewPath = $viewPath;
        $this->tmpPath = $tmpPath;
    }

    /**
     * 引入模板变量
     * @author tabalt
     * @param string $key
     * @param mixed $value
     * @param boolean $filterHtml
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