<?php
function zy($str){
    if (!get_magic_quotes_gpc()) {
        return mysql_escape_string($str);
    }else {
        return $str;
    }
}