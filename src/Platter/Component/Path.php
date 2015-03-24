<?php

namespace Platter\Component;

class Path
{
    public static function isExist($filePath)
    {
        if ($filePath && is_dir($filePath)) {
            return true;
        }
        return false;
    }

    public static function create($dirList, $basePath = './')
    {
        if (is_array($dirList)) {
            
        }
        
    }
    
}