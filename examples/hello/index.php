<?php
$frameworkPath = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;
$appPath = dirname(__FILE__) . DIRECTORY_SEPARATOR;
$srcPath = $appPath . 'src' . DIRECTORY_SEPARATOR;


require $frameworkPath . 'Platter/Platter.php';

try {
    $App = new \Platter\Application\Web('Hello', $srcPath);
    
    $controllerName = str_replace('/', '', str_replace('.', '', ucfirst(\Platter\Http\Request::getFilteredValue('c', 'strip_tags', 'Index'))));
    $actionName = \Platter\Http\Request::getFilteredValue('a', 'strip_tags', 'index');
    
    $App->setModuleName('Front');
    $App->setControllerName($controllerName);
    $App->setActionName($actionName);
    
    $App->setLogPath($appPath . 'log' . DIRECTORY_SEPARATOR);
    $App->setConfigPath($appPath . 'config' . DIRECTORY_SEPARATOR);
    $App->setViewPath($appPath . 'view' . DIRECTORY_SEPARATOR);
    $App->setStaticPath($appPath . 'static' . DIRECTORY_SEPARATOR);
    
    $App->run();
} catch ( \Exception $e ) {
}

