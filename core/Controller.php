<?php
namespace core;

class Controller extends Tpl
{
    public $assign = [];
    public function assign($name,$value)
    {
        $this->assign[$name] = $value;
    }

    public function display($tpl,$param = null,$cache = true){

        $tplPath = APP_PATH.'/view/'.$tpl.'.html';
        if(!file_exists($tplPath)) throw new Exception("模板文件{$tpl}不存在");
        $parPath = APP_PATH.'/cache/'.$tpl.'.php';
        if(is_array($param)){
            if(key($param) == 'page'){
                $cachePath = APP_PATH.'/cache/html/list-'.current($param).'.html';
            }elseif (key($param) == 'article'){
                $cachePath = APP_PATH.'/cache/html/article-'.current($param).'.html';
            }
        }else{
            $cachePath = APP_PATH.'/cache/html/'.$tpl.'.html';
        }

        if(CACHE && $cache){
            if (file_exists($cachePath)&&file_exists($parPath)){
                if (filemtime($parPath)>=filemtime($tplPath)&&filemtime($cachePath)>=filemtime($parPath)){
                    include $cachePath;
                    return;
                }
            }
        }
        if(!file_exists($parPath)||filemtime($parPath)<filemtime($tplPath)){
            $this->content = file_get_contents($tplPath);
            $this->compile($parPath);
        }
        include $parPath;

        if(CACHE && $cache){
            file_put_contents($cachePath, ob_get_contents());
            ob_end_clean();
            include $cachePath;
        }
    }

    // 载入文件
    public function create($file){
        $tplFile = APP_PATH.'/view/'.$file.'.html';
        if(!file_exists($tplFile)){
            throw new Exception($file.'模板文件不存在');
        }

        $parPath = APP_PATH.'/cache/'.$file.'.php';
        if(!file_exists($parPath) || filemtime($parPath)<=filemtime($parPath)){
            $this->content = file_get_contents($tplFile);
            $this->compile($parPath);
        }
        include $parPath;
    }

    //
    protected function json($data) {
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($data));
    }
}