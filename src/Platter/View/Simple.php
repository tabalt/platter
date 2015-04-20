<?php

namespace Platter\View;

class Simple extends Base
{

    /**
     * 模板变量列表
     * @author tabalt
     * @var array
     */
    private $tplVarList = array();

    /**
     * 引入模板变量
     * @author tabalt
     * @param string $key
     * @param mixed $value
     */
    public function assign($key, $value)
    {
        if (! preg_match('/^[a-zA-Z]/i', $key)) {
            throw new \Exception('tpl variable ' . $key . 'name error');
        }
        $this->tplVarList[$key] = $value;
    }

    /**
     * 获取渲染后的模板代码
     * @author tabalt
     * @param string $tpl
     * @param boolean $trim
     */
    public function display($tplFile = false, $trim = false, $return = false)
    {
        // 处理模板变量
        if (! empty($this->tplVarList)) {
            extract($this->tplVarList);
        }
        if (! file_exists($tplFile)) {
            throw new \Exception('tpl file ' . $tplFile . ' not exists');
        }
        ob_start();
        require $tplFile;
        $content = ob_get_contents();
        ob_end_clean();
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