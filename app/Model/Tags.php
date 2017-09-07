<?php
/**
 * Created by PhpStorm.
 * User: dengyun
 * Date: 2017/8/20
 * Time: 11:28
 */

namespace app\Model;


use core\Model;

class Tags extends Model
{
    public $table = 'tags';

    public function tagsAar($tags)
    {
        $tags = $this->where(['name'=>$tags])->find();
        $result = $this->table('article')
            ->field('article.id,article.title,article.create_time,article.content,cat.id as cat_id,cat.name,cat.class')
            ->join('tags_map',['article.id','=','tags_map.article_id'])
            ->join('cat',['cat.id','=','article.cat'],'right')
            ->where(['tags_map.tag_id'=>$tags['id'],'article.status'=>1])
            ->limit('30')
            ->select();

        return $result;
    }


    public function tags($id)
    {
        $result = $this->field('tags.id,tags.name')
            ->join('tags_map',['tags_map.tag_id','=','tags.id'])
            ->where(['tags_map.article_id'=>$id])
            ->select();
        return $result;
    }
}