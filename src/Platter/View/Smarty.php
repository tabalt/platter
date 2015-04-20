<?php

namespace Platter\View;

require_once __DIR__ . '/smarty/Smarty.class.php';
class Smarty extends Base
{

    /**
     * Smarty类
     * @var Smarty
     */
    protected $Smarty;

    /**
     * 初始化Smarty
     */
    protected function initSmarty()
    {
        $this->Smarty = new \Smarty();
        $this->Smarty->caching = false;
        
        $this->Smarty->template_dir = $this->viewPath;
        $this->Smarty->compile_dir = $this->tmpPath . 'smarty_compile' . DIRECTORY_SEPARATOR;
        
        $this->Smarty->left_delimiter = "{%";
        $this->Smarty->right_delimiter = "%}";
    }

    /**
     * 构造函数
     * @param string $viewPath
     */
    public function __construct($viewPath, $tmpPath)
    {
        parent::__construct($viewPath, $tmpPath);
        
        // 初始化Smarty
        $this->initSmarty();
    }

    /**
     * 引入模板变量
     * @author tabalt
     * @param string $key
     * @param mixed $value
     */
    public function assign($key, $value)
    {
        $this->Smarty->assign($key, $value);
    }

    /**
     * 获取渲染后的模板代码
     * @author tabalt
     * @param string $tpl
     * @param boolean $trim
     */
    public function display($tplFile = false, $trim = false, $return = false)
    {
        $content = $this->Smarty->fetch($tplFile);
        if ($trim) {
            $searchList = array(
                "\r\n", 
                "\r", 
                "\n"
            );
            // TODO remove more
            $content = str_replace($searchList, '', $content);
        }
        if ($return) {
            return $content;
        } else {
            \Platter\Http\Response::output($content);
        }
    }
}