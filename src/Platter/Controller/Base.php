<?php

namespace Platter\Controller;

/**
 * 视图控制器类
 * @author tabalt
 */
abstract class Base
{

    /**
     * 模块名称
     * @author tabalt
     * @var string
     */
    protected $moduleName = '';

    /**
     * 控制器名称
     * @author tabalt
     * @var string
     */
    protected $controllerName = '';

    /**
     * 操作名称
     * @author tabalt
     * @var string
     */
    protected $actionName = '';

    /**
     * 初始化基类控制器
     * @author tabalt
     */
    final protected function initBaseController()
    {
    }

    /**
     * 初始化控制器
     */
    protected function initController()
    {
        // 初始化基类控制器
        $this->initBaseController();
    }

    /**
     * 运行操作
     * @author tabalt
     */
    protected function runAction()
    {
        call_user_func(array(
            $this, 
            $this->actionName
        ));
    }

    /**
     * 初始化函数
     * @author tabalt
     */
    final public function __construct($moduleName, $controllerName, $actionName)
    {
        $this->moduleName = $moduleName;
        $this->controllerName = $controllerName;
        $this->actionName = $actionName;
    }

    public function run()
    {
        // 初始化控制器
        $this->initController();
        
        // 运行操作
        $this->runAction();
    }
}