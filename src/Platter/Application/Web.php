<?php

namespace Platter\Application;

class Web extends Base
{

    /**
     * 模板目录
     * @var string
     */
    protected $viewPath = '';

    /**
     * 静态文件目录
     * @var string
     */
    protected $staticPath = '';

    /**
     * 初始化Web应用程序
     * @author tabalt
     */
    final protected function initWebApplication()
    {
        // 设置cookie_httponly
        // ini_set('session.cookie_httponly', 1);
        
        // 开启session
        // session_start();
        
        // 页面编码
        header('Content-Type:text/html;charset=utf-8');
        
        // 设置控制器的模板目录
        $this->Controller->setViewPath($this->viewPath);
        
        // 设置控制器的静态文件目录
        $this->Controller->setStaticPath($this->staticPath);
    }

    /**
     * 初始化应用程序
     * @author tabalt
     */
    protected function initApplication()
    {
        // 调用父类的应用程序初始化方法
        parent::initApplication();
        
        // 初始化Web应用程序
        $this->initWebApplication();
    }

    /**
     * 设置模板目录
     * @param string $viewPath
     */
    public function setViewPath($viewPath)
    {
        $this->viewPath = $this->getPathWithModuleName($viewPath);
    }

    /**
     * 设置静态文件目录
     * @param string $staticPath
     */
    public function setStaticPath($staticPath)
    {
        $this->staticPath = $staticPath . strtolower($this->moduleName) . DIRECTORY_SEPARATOR . 'static' . DIRECTORY_SEPARATOR;
    }
}