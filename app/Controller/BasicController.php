<?php

namespace app\Controller;


use app\Model\Article;
use core\Config;
use core\Controller;

class BasicController extends Controller
{
    public function __construct()
    {
        $topArt = (new Article())->field('id,title')->limit('5')->select();
        $link = Config::get('link');
        $this->assign('top',$topArt);
        $this->assign('link',$link);
    }

}