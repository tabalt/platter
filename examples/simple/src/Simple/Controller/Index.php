<?php

namespace Simple\Controller;

class Index extends \Platter\Controller\Web
{

    /**
     * 首页
     */
    public function index()
    {
        $this->assign('name', 'Platter');
        $this->display();
    }
}