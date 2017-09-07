<?php
namespace core;


class Exception extends \Exception
{
    public function __construct($message, $code = 0)
    {
        parent::__construct($message, $code);
        $info = <<<EOF
        <table>
            <tr>
                <td>错误代码</td>
                <td>{$this->code}</td>
            <tr>
                <td>错误信息</td>
                <td>{$this->message}</td>
            </tr>
            <tr>
                <td>错误位置</td>
                <td>{$this->file} {$this->line}</td>
            </tr>
        </table>
    
EOF;
        exit($info);
    }


}