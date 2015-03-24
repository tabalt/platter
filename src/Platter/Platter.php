<?php

// php 版本要求 5.3.0
version_compare(PHP_VERSION, '5.3.0', '>') or die('require php version > 5.3.0!');

//设置类自动加载
$baseDir = dirname(dirname(__FILE__));
$classLoaderFile = "{$baseDir}/Platter/Component/ClassLoader.php";

if (file_exists($classLoaderFile)) {
    
    require $classLoaderFile;
    
    \Platter\Component\ClassLoader::registerNamespace('Platter', $baseDir);
    \Platter\Component\ClassLoader::registerAutoLoad();
}

