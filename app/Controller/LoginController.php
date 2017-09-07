<?php
/**
 * Created by PhpStorm.
 * User: dengyun
 * Date: 2017/8/21
 * Time: 22:39
 */

namespace app\Controller;


use core\Controller;

class LoginController extends Controller
{
    public function login()
    {
        $this->display('login',null,false);
    }
}