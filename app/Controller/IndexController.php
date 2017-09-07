<?php
namespace app\Controller;


use app\Model\Article;
use app\Model\Cat;
use app\Model\Tags;
use core\Page;
use core\Parser;

class IndexController extends BasicController
{
    public function index()
    {
        $article = (new Article())->art();
        $this->assign('article',$article['data']);
        $this->assign('page',$article['page']);
        $this->display('index',['page'=>1]);
    }

    public function article($id){
        $parser = new Parser();
        $article = (new Article())->where(['id'=>$id])->find();
        $article['content'] = $parser->makeHtml($article['content']);
        $tags = (new Tags())->tags($id);
        $cat = (new Cat())->field('id,name')->where(['id'=>$article['cat']])->find();
        (new Article())->setInc('view',$id);
        $this->assign('cat',$cat);
        $this->assign('tags',$tags);
        $this->assign('article',$article);
        $this->display('article',['article'=>$id]);
    }

    public function about()
    {
        $this->display('about');
    }

    public function search($w = '')
    {
        $w = urldecode($w);
        $art = (new Article())->field('article.id,article.cat,article.title,article.create_time,article.description,cat.id,cat.name,cat.class')
            ->join('cat',['cat.id','=','article.cat'])
            ->where(['title'=>['LIKE'=>'%'.$w.'%']])
            ->select();
        $this->assign('w',$w);
        $this->assign('art',$art);
        $this->display('search');
    }
}