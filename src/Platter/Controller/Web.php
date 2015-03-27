<?php

namespace Platter\Controller;

use Platter\Component\Config;

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
    protected function setLayoutTpl($layoutTpl = 'layout.php')
    {
        $layoutFile = $this->viewPath . $layoutTpl;
        
        if (! file_exists($layoutFile)) {
            throw new \Exception('layout tpl ' . $this->layoutTpl . 'not exist');
        }
        $this->layoutTpl = $layoutTpl;
    }

    /**
     * 初始化视图
     * @author tabalt
     */
    protected function initView()
    {
        // 实例化视图
        $this->View = new \Platter\View\Simple($this->viewPath);
        
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
    protected function assign($tplVar, $value)
    {
        if (is_array($tplVar)) {
            foreach ($tplVar as $key => $value) {
                $this->View->assign($key, $value);
            }
        } else {
            $this->View->assign($tplVar, $value);
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
        $tplSuffix = Config::get('TPL_SUFFIX');
        $tplFile = "{$this->viewPath}{$tpl}.{$tplSuffix}";
        if (! file_exists($tplFile)) {
            throw new \Exception('tpl file ' . $tplFile . 'not exist');
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
            $tplFile = $this->viewPath . "/public/message.html";
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
     * @param int $errorCode 错误码
     * @param string $data 返回的数据
     * @param boolean $ajax 是否为Ajax方式
     */
    protected function error($info, $errorCode = 0, $data = false, $ajax = false)
    {
        $this->showMessage(0, $errorCode, $info, $data, $ajax);
        exit();
    }

    /**
     * 重写检测参数的值方法，为空返回错误码
     * @author tabalt
     * @param string $method
     * @param string $key
     * @param string $filter
     */
    protected function checkParam($method, $key, $filter, $defaultValue = false)
    {
        // TODO check this code
        $value = parent::checkParam($method, $key, $filter, $defaultValue);
        if (empty($value)) {
            $this->error('缺少参数' . htmlspecialchars($key), 0);
        }
        return $value;
    }
}