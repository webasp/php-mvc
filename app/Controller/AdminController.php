<?php
/**
 * Created by PhpStorm.
 * User: dengyun
 * Date: 2017/8/20
 * Time: 0:43
 */

namespace app\Controller;


use app\Model\Admin;
use app\Model\Archives;
use app\Model\Article;
use app\Model\Cat;
use app\Model\Tags;
use app\Model\TagsMap;
use core\Controller;
use core\Exception;

class AdminController extends Controller
{
    public function __construct()
    {
        session_start();
        if(isset($_POST['password']) && isset($_POST['username'])){
            $username = $_POST['username'];
            $password = md5($_POST['password']);
            $result = (new Admin())->where(['username'=>$username,'password'=>$password])->find();
            $_SESSION['admin'] = $result;
        }

        if(empty($_SESSION['admin'])){
            header("Location: /login");
        }
    }

    public function index()
    {
        $data = (new Archives())->select();
        $this->assign('data',$data);
        $this->display('admin',null,false);
    }


    public function add()
    {
        if($_POST){
            $date['title'] = $_POST['title'];
            $date['cat'] = $_POST['cat'];
            $date['content'] = \zy($_POST['content']);
            $result = (new Archives())->add($date);
            if($result){
                $age['status'] = 1;
                $age['info'] = '添加成功';
            }else{
                $age['status'] = 0;
                $age['info'] = '添加失败';
            }
            $this->json($age);
        }

        $cat =(new Cat())->select();
        $tag =(new Tags())->select();
        $this->assign('cat',$cat);
        $this->assign('tag',$tag);
        $this->display('add',null,false);

    }

    public function edit($id)
    {
        if($_POST){
            $date['title'] = $_POST['title'];
            $date['cat'] = $_POST['cat'];
            $date['thumb'] = $_POST['thumb'];
            $date['description'] = $_POST['description'];
            $date['content'] = $_POST['content'];
            $result = (new Archives())->update($id,$date);
            $tagsMap = new TagsMap();
            $tags = isset($_POST['tag']) ? $_POST['tag'] : null;
            if($tags){
                $map = [];
                $artMap = $tagsMap->where(['article_id'=>$id])->select();
                foreach ($artMap as $value){
                    $map[] = $value['tag_id'];
                }
                $m = array_intersect($tags,$map);
                $old = array_diff($map,$m);
                $new = array_diff($tags,$m);
                $o = implode(',',$old);
                if($o){
                    $tagsMap->where(['tag_id'=>['IN'=>$o]])->del();
                }
                $data = [];
                foreach ($new as $value){
                    $data[] = ['tag_id'=>$value,'article_id'=>$id];
                }
                $tagsMap->addAll($data);
            }

            if($result){
                $age['status'] = 1;
                $age['info'] = '修改成功';
            }else{
                $age['status'] = 0;
                $age['info'] = '修改失败';
            }
            $this->json($age);
        }
        $artMap = (new TagsMap())->where(['article_id'=>$id])->select();
        $tags = [];
        foreach ($artMap as $value){
            $tags[] = $value['tag_id'];
        }
        $art = (new Article())->where(['id'=>$id])->find();
        $cat =(new Cat())->select();
        $tag =(new Tags())->select();
        $this->assign('tags', json_encode($tags));
        $this->assign('art',$art);
        $this->assign('cat',$cat);
        $this->assign('tag',$tag);
        $this->display('edit',null,false);
    }

    public function state($id)
    {
        $result = (new Article())->field('id,status')->where(['id'=>$id])->find();
        if($result['status'] == 0){
            (new Article())->update($id,['status'=>'1']);
            $age['status'] = $id;
            $age['info'] = '显示';
        }else{
            (new Article())->update($id,['status'=>'0']);
            $age['status'] = $id;
            $age['info'] = '隐藏';
        }
        return $this->json($age);

    }

    // 上传文件
    public function update()
    {
        $imgName = $_FILES['img']['name'];
        $imgSize = $_FILES['img']['size'];
        if ($imgName != "") {
            if ($imgSize > 1024000) {
                echo '图片大小不能超过1M';
                throw new Exception('图片太大');
            }
            $type = strstr($imgName, '.');
            if ($type != ".png" && $type != ".jpg") {
                throw new Exception('图片格式不对');
            }
            $pic_path = ROOT_PATH."/static/Thumb/". $imgName;

            move_uploaded_file($_FILES['img']['tmp_name'], $pic_path);
        }
        return $this->json($imgName);
    }

}