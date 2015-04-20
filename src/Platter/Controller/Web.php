<?php

namespace Platter\Controller;

/**
 * 视图控制器类
 * @author tabalt
 */
class Web extends Base
{

    /**
     * 视图目录
     * @author tabalt
     * @var string
     */
    protected $viewPath = './';

    /**
     * 静态文件目录
     * @var string
     */
    protected $staticPath = '';

    /**
     * 布局模板
     * @author tabalt
     * @var string
     */
    protected $layoutTpl = 'layout.php';

    /**
     * 视图对象
     * @author tabalt
     * @var object
     */
    protected $View;

    /**
     * 设置布局模板
     * @author tabalt
     * @var string
     */
    protected function setLayoutTpl($layoutTpl = false)
    {
        if ($layoutTpl) {
            $layoutFile = $this->viewPath . $layoutTpl;
            
            if (! file_exists($layoutFile)) {
                throw new \Exception('layout tpl ' . $this->layoutTpl . ' not exist');
            }
            $this->layoutTpl = $layoutTpl;
        }
    }

    /**
     * 初始化视图
     * @author tabalt
     */
    protected function initView()
    {
        $templateEngine = \Platter\Component\Config::get('TEMPLATE_ENGINE');
        $viewClass = '\\Platter\\View\\' . ucfirst($templateEngine);
        
        // 实例化视图
        $this->View = new $viewClass($this->viewPath, $this->tmpPath);
        
        // 设置布局模板
        $this->setLayoutTpl();
    }

    /**
     * 初始化Web控制器
     * @author tabalt
     */
    final protected function initWebController()
    {
        // 初始化视图
        $this->initView();
    }

    /**
     * 初始化控制器
     * @author tabalt
     */
    protected function initController()
    {
        // 调用父类的控制器初始化方法
        parent::initController();
        
        // 初始化Web控制器
        $this->initWebController();
    }

    /**
     * 设置模板目录
     * @param string $viewPath
     */
    public function setViewPath($viewPath)
    {
        $this->viewPath = $viewPath;
    }

    /**
     * 设置静态文件目录
     * @param string $staticPath
     */
    public function setStaticPath($staticPath)
    {
        $this->staticPath = $staticPath;
    }

    /**
     * 引入模板变量
     * @author tabalt
     * @param string|array $tplVar
     * @param mixed $value
     */
    protected function assign($tplVar, $value, $filterHtml = true)
    {
        if (is_array($tplVar)) {
            foreach ($tplVar as $key => $value) {
                $this->View->assign($key, $value, $filterHtml);
            }
        } else {
            $this->View->assign($tplVar, $value, $filterHtml);
        }
    }

    /**
     * 输出渲染后的模板
     * @author tabalt
     * @param string $tpl
     */
    protected function display($tpl = '', $return = false)
    {
        if (empty($tpl)) {
            $tpl = strtolower($this->controllerName) . DIRECTORY_SEPARATOR . $this->actionName;
        }
        $tplName = $tpl . '.' . \Platter\Component\Config::get('TEMPLATE_SUFFIX');
        $tplFile = $this->viewPath . $tplName;
        if (! file_exists($tplFile)) {
            throw new \Exception('tpl file ' . $tplFile . ' not exist');
        } else {
            $this->assign('mainTpl', $tplFile);
        }
        $layoutFile = $this->viewPath . $this->layoutTpl;
        $html = $this->View->display($layoutFile, $return);
    }

    /**
     * 显示提示信息
     * @author tabalt
     * @param string $type
     * @param string $message
     * @param string $data
     * @param boolean $ajax
     */
    protected function showMessage($status, $errorCode, $info, $data, $ajax)
    {
        if ($ajax || \Platter\Http\Request::isAjax()) {
            \Platter\Http\Response::outputApi($status, $errorCode, $info, $data);
        } else {
            // 不是ajax提交 则调用系统模板显示信息
            $this->assign('status', $status);
            $this->assign('info', $info);
            $this->assign('jumpUrl', $data);
            $tplFile = $this->viewPath . "public/message.php";
            $this->View->display($tplFile);
        }
    }

    /**
     * success 方法
     * @author tabalt
     * @param string $info 提示信息
     * @param string $data 返回的数据
     * @param boolean $ajax 是否为Ajax方式
     */
    protected function success($info, $data = false, $ajax = false)
    {
        $this->showMessage(1, 0, $info, $data, $ajax);
    }

    /**
     * error 方法
     * @author tabalt
     * @param string $message 错误信息
     * @param string $data 返回的数据
     * @param boolean $ajax 是否为Ajax方式
     * @param int $errorCode 错误码
     */
    protected function error($info, $data = false, $ajax = false, $errorCode = -1)
    {
        $this->showMessage(0, $errorCode, $info, $data, $ajax);
        exit();
    }

    /**
     * 跳转函数
     * @author tabalt
     * @param string $url URL
     * @param boolean $ajax 是否为Ajax方式
     */
    protected function redirect($url, $ajax = false)
    {
        if ($ajax || \Platter\Http\Request::isAjax()) {
            $this->showMessage(1, 0, 'redirect', $url);
        } else {
            header("Location:" . $url);
        }
        exit();
    }
}