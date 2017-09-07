<?php
namespace core;


class core
{
    static public $classMap = array();
    static public function run()
    {
        include CORE_PATH.'/Function.php';
        spl_autoload_register('self::load');
        include ROOT_PATH.'/config/route.php';
    }

    static public function load($class)
    {
        if(isset(self::$classMap[$class])){
            return true;
        }else{
            $class = str_replace('\\','/',$class);
            $file = ROOT_PATH.'/'.$class.'.php';
            if(is_file($file)){
                include $file;
                self::$classMap[$class] = $class;
            }else{
                return false;
            }
        }
    }
}