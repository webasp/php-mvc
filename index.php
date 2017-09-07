<?php
define('ROOT_PATH',__DIR__);
define('CORE_PATH',ROOT_PATH.'/core');
define('APP_PATH',ROOT_PATH.'/app');
define('CACHE',false);
CACHE ? ob_start():null;
include CORE_PATH.'/Core.php';
\core\core::run();



