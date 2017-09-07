<?php
/**
 * Created by PhpStorm.
 * User: dengyun
 * Date: 2017/9/5
 * Time: 14:19
 */

namespace app\Controller;


use app\Model\Article;
use app\Model\Cat;

class CatController extends BasicController
{

    public function index($id)
    {
        $cat = (new Cat())->field('id,name')->where(['id'=>$id])->find();
        $art = (new Article())->field('id,title,create_time')->where(['cat'=>$cat['id']])->select();
        $this->assign('cat',$cat);
        $this->assign('art',$art);
        $this->display('cat');
    }
}