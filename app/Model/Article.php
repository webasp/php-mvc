<?php
/**
 * Created by PhpStorm.
 * User: dengyun
 * Date: 2017/8/19
 * Time: 19:07
 */

namespace app\Model;


use core\Model;

class Article extends Model
{
    public $table = 'article';

    public function art(){
       return $this->field('article.id,article.title,article.cat,article.thumb,article.description,cat.name,cat.class')
           ->join('cat',['article.cat','=','cat.id'])
            ->where(['status'=>1])
           ->paginate('15');
    }

    public function setInc($field,$id)
    {
        $sql = sprintf("UPDATE `%s` SET %s=%s+1 WHERE id=%s",$this->table,$field,$field,$id);
        $this->query($sql);
    }

}