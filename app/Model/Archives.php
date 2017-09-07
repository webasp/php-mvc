<?php
/**
 * Created by PhpStorm.
 * User: dengyun
 * Date: 2017/8/20
 * Time: 23:25
 */

namespace app\Model;


use core\Model;

class Archives extends Model
{
    public $table = 'article';

    public function getArchives()
    {

        $sql = "select date_format(`create_time`,'%Y-%m') as `month`,date_format(`create_time`,'%Y年%m月') as `months` from article 
                                  where `status`='1'
                                  group by date_format(`create_time`,'%Y-%m') 
                                  order by `create_time` desc
                                  LIMIT 10";
        $sth = $this->dbHandle->prepare($sql);
        $sth->execute();
        $pubTime =  $sth->fetchAll();
        $data = [];
        if($pubTime){
            foreach ($pubTime as $months){
                $data[$months['months']] = $this->field('id,title,create_time')
                    ->where(['status'=>1])
                    ->order('create_time DESC')
                    ->where(['create_time'=>['LIKE'=>$months['month'].'%']])
                    ->select();
            }
        }
        return $data;
    }
}