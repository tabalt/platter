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

    protected function outputStaticTag($type, $url)
    {
        $tag = $this->createStaticTag($type, $url);
        if (empty($tag) || empty($url)) {
            return false;
        } else {
            \Platter\Http\Response::output($tag);
            return true;
        }
    }

    protected function loadStaticWithTag($type, $files, $isDev = false)
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
        foreach ($files as $key => $file) {
            $files[$key] = urlencode($file);
        }
        
        if (! $isDev) {
            $url = $this->getStaticUrl($type, $files);
            $this->outputStaticTag($type, $url);
        } else {
            foreach ($files as $file) {
                $url = '/static/' . $type . '/' . $file;
                $this->outputStaticTag($type, $url);
            }
        }
        
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

    public function loadCssWithTag($files, $isDev = false)
    {
        $this->loadStaticWithTag(self::TYPE_CSS, $files, $isDev);
    }

    public function loadJsWithTag($files, $isDev = false)
    {
        $this->loadStaticWithTag(self::TYPE_JS, $files, $isDev);
    }
}