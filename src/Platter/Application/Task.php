<?php

namespace Platter\Application;

class Task extends Base
{

    /**
     * 初始化Web应用程序
     * @author tabalt
     */
    final protected function initTaskApplication()
    {
    }

    /**
     * 初始化应用程序
     * @author tabalt
     */
    protected function initApplication()
    {
        // 调用父类的应用程序初始化方法
        parent::initApplication();
        
        // 初始化Web应用程序
        $this->initTaskApplication();
    }
}