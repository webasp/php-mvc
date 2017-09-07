<?php
namespace core;

class Config
{
    static private $config = [];
    static public function get($name)
    {
        if(empty(self::$config)){
            $file = ROOT_PATH.'/config/config.php';
            if(is_file($file)){
                $config = include $file;
                if(isset($config[$name])){
                    self::$config = $config;
                    return $config[$name];
                }else{
                    throw new Exception('没有'.$name.'配置项');
                }
            }else{
                throw new Exception('配置文件不存在：'.$file);
            }
        }else{
            return self::$config[$name];
        }

    }
}