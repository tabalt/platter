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

    public static function create($dirList, $basePath = '')
    {
        $path = '';
        if (is_array($dirList)) {
            $path = implode(DIRECTORY_SEPARATOR, array_filter($dirList)) . DIRECTORY_SEPARATOR;
        }
        return $basePath . $path;
    }
}