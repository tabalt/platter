<?php

namespace Hello\Controller\Front;

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