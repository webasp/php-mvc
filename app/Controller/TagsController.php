<?php
/**
 * Created by PhpStorm.
 * User: dengyun
 * Date: 2017/8/20
 * Time: 11:27
 */

namespace app\Controller;


use app\Model\Tags;

class TagsController extends BasicController
{
    public function index($tag = '读书笔记')
    {
        $tag = urldecode($tag);
        $tagModel = new Tags();
        $tags = $tagModel->select();
        $article = $tagModel->tagsAar($tag);
        $this->assign('article',$article);
        $this->assign('tags',$tags);
        $this->assign('tag',$tag);
        $this->display('tags');

    }

}