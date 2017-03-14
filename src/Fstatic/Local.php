<?php

namespace Platter\Fstatic;

class Local extends Base
{

    protected function getStaticUrl($type, $files)
    {
        $url = "http://{$_SERVER['HTTP_HOST']}/?c=fstatic&type={$type}&files[]=" . implode('&files[]=', $files);
        return $url;
    }
    
    protected function getStaticContents($type, $files)
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
    
        $contents = '';
        foreach ($files as $file) {
            if(false === strpos($file, '../')){
                $filePath = $this->staticPath . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . $file;
                $contents .= "\n" . \Platter\Component\File::getContents($filePath);
            }
        }
        
        $contents = trim($contents);
        
        if ($contents) {
        	return $contents;
        } else {
        	return false;
        }
    }
    
    public function outputContents()
    {
        $type = \Platter\Http\Request::getFilteredValue('type', 'strip_tags', self::TYPE_CSS);
        $files = \Platter\Http\Request::getValue('files', false);
        
        $content = $this->getStaticContents($type, $files);
        switch ($type) {
        	case self::TYPE_CSS : 
        	    header('Content-Type:text/css; charset=utf-8');
        	    break;
        	case self::TYPE_JS :
        	    header('Content-Type:application/x-javascript; charset=utf-8');
        	    break;
        }
        \Platter\Http\Response::output($content);
    }
    
}