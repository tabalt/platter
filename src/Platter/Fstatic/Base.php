<?php

namespace Platter\Fstatic;

abstract class Base
{

    const TYPE_CSS = 'css';

    const TYPE_JS = 'js';

    /**
     * 静态文件类型列表
     * @var array
     */
    protected $typeList = array(
        self::TYPE_CSS, 
        self::TYPE_JS
    );
    
    /**
     * 静态文件目录
     * @var string
     */
    protected $staticPath = '';
    
    abstract protected function getStaticUrl($type, $files);

    protected function createStaticTag($type, $url)
    {
        $tag = false;
        switch ($type) {
            case self::TYPE_CSS :
                $tag = '<link type="text/css" rel="stylesheet" href="' . $url . '" />';
                break;
            case self::TYPE_JS :
                $tag = '<script type="text/javascript" src="' . $url . '"></script>';
                break;
        }
        return $tag;
    }

    protected function loadStaticWithTag($type, $files)
    {
        
        if (! in_array($type, $this->typeList)) {
            return false;
        }
        
        if (empty($files)) {
            return false;
        }
        if (! is_array($files)) {
            $files = array(
                $files
            );
        }
        
        $url = $this->getStaticUrl($type, $files);
        if (empty($url)) {
            return false;
        }
        
        $tag = $this->createStaticTag($type, $url);
        if (empty($tag)) {
            return false;
        }
        
        \Platter\Http\Response::output($tag);
        
        return true;
    }

    /**
     * 构造函数
     * @param string $staticPath
     */
    public function __construct($staticPath)
    {
        $this->staticPath = $staticPath;
    }
    
    public function loadCssWithTag($files)
    {
        $this->loadStaticWithTag(self::TYPE_CSS, $files);
    }

    public function loadJsWithTag($files)
    {
        $this->loadStaticWithTag(self::TYPE_JS, $files);
    }
}