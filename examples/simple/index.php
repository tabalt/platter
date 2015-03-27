<?php
require '../../src/Platter/Platter.php';

try {
    $App = new \Platter\Application\Web('Simple');
    
    $controllerName = str_replace('/', '', str_replace('.', '', ucfirst(\Platter\Http\Request::getFilteredValue('c', 'strip_tags', 'Index'))));
    $actionName = \Platter\Http\Request::getFilteredValue('a', 'strip_tags', 'index');
    
    $App->setControllerName($controllerName);
    $App->setActionName($actionName);
    
    $App->run();
} catch ( \Exception $e ) {
}

