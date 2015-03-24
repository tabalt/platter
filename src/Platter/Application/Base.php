<?php

namespace Platter\Application;

abstract class Base
{

    /**
     * 应用名称
     * @author tabalt
     * @var string
     */
    protected $appName = null;

    /**
     * 代码目录
     * @author tabalt
     * @var string
     */
    protected $srcPath = './src/';

    /**
     * 配置目录
     * @author tabalt
     * @var string
     */
    protected $configPath = './config/';

    /**
     * 日志目录
     * @author tabalt
     * @var string
     */
    protected $logPath = './log/';

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
     * 给目录加上模块名
     * @param string $path
     * @return string
     */
    protected function getPathWithModuleName($path)
    {
        return $path . strtolower($this->moduleName) . DIRECTORY_SEPARATOR;
    }

    /**
     * 初始化配置
     * @author tabalt
     */
    final protected function initConfig()
    {
        // 加载项目配置文件
        $appConfigFile = $this->configPath . 'config.php';
        \Platter\Component\Config::parseFile($appConfigFile);
    }

    /**
     * 初始化日志
     * @author tabalt
     */
    final protected function initLogger()
    {
        \Platter\Component\Logger::setConfig($this->logPath);
    }

    /**
     * 初始化基类应用程序
     * @author tabalt
     */
    final protected function initBaseApplication()
    {
        // 设置时区 东8区（china）
        date_default_timezone_set('PRC');
        
        // 非调试模式，则关闭错误报告，
        if (! \Platter\Component\Config::get('IS_DEBUG')) {
            error_reporting(0);
        } else {
            error_reporting(E_ALL);
        }
        
        // TODO 设置错误处理函数
        // set_error_handler('\Platter\Component\Error::handler');
    }

    /**
     * 初始化应用程序
     * @author tabalt
     */
    protected function initApplication()
    {
        // 初始化基类应用程序
        $this->initBaseApplication();
    }

    /**
     * 初始化控制器
     * @author tabalt
     */
    protected function initController()
    {
        // 构造控制器类
        // TODO 使用目录类生成，考虑控制器为空的情况
        $controllerClass = '\\' . $this->appName . '\\Controller\\' . $this->moduleName . '\\' . $this->controllerName;
        if (! class_exists($controllerClass)) {
            throw new \Exception('error controller class ' . $controllerClass);
        } else {
            $methodList = get_class_methods($controllerClass);
            if (! in_array($this->actionName, $methodList)) {
                throw new \Exception('action ' . $this->actionName . ' not exists in class ' . $controllerClass);
            } else {
                $this->Controller = new $controllerClass($this->moduleName, $this->controllerName, $this->actionName);
            }
        }
    }

    /**
     * 运行控制器
     * @author tabalt
     */
    protected function runController()
    {
        $this->Controller->run();
    }

    /**
     * 构造函数
     * @author tabalt
     */
    final public function __construct($appName, $srcPath)
    {
        $this->appName = $appName;
        $this->srcPath = rtrim($srcPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        
        \Platter\Component\ClassLoader::registerNamespace($appName, $srcPath);
    }

    /**
     * 设置配置目录
     * @author tabalt
     */
    public function setConfigPath($configPath)
    {
        $this->configPath = $this->getPathWithModuleName($configPath);
    }

    /**
     * 设置日志目录
     * @author tabalt
     */
    public function setLogPath($logPath)
    {
        $this->logPath = $this->getPathWithModuleName($logPath);
    }

    /**
     * 设置模块名
     * @author tabalt
     */
    public function setModuleName($moduleName)
    {
        $this->moduleName = $moduleName;
    }

    /**
     * 设置控制器
     * @author tabalt
     */
    public function setControllerName($controllerName)
    {
        $this->controllerName = $controllerName;
    }

    /**
     * 设置操作名
     * @author tabalt
     */
    public function setActionName($actionName)
    {
        $this->actionName = $actionName;
    }

    /**
     * 运行应用程序
     * @author tabalt
     */
    final public function run()
    {
        // 初始化配置
        $this->initConfig();
        
        // 初始化日志
        $this->initLogger();
        
        try {
            // 初始化控制器
            $this->initController();
            
            // 初始化应用程序
            $this->initApplication();
            
            // 运行控制器
            $this->runController();
        } catch ( \Exception $e ) {
            if (! \Platter\Component\Config::get('IS_DEBUG')) {
                \Platter\Component\Logger::framework($e->getMessage());
            } else {
                echo $e->getMessage();
            }
        }
    }
}