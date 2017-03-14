<?php

namespace Platter\Controller;

class Task extends Base
{

    /**
     * 初始化Task控制器
     * @author tabalt
     */
    final protected function initTaskController()
    {
        
    }
    
    /**
     * 初始化控制器
     * @author tabalt
     */
    protected function initController()
    {
        // 调用父类的控制器初始化方法
        parent::initController();
    
        // 初始化Task控制器
        $this->initTaskController();
    }
    
}