<?php
/**
 * Created by PhpStorm.
 * User: dengyun
 * Date: 2017/8/28
 * Time: 13:28
 */

namespace core;

//分页类
class Page
{
    private $total; //数据表中总记录数
    private $listRows; //每页显示行数
    public $limit;
    private $uri;
    private $pageNum; //页数
    private $config = ['prev' => '←', 'next' => '→'];
    private $listNum = 3;

    public function __construct($total,$listRows,$page,$pa = "")
    {
        $this->total = $total;
        $this->listRows = $listRows;
        $this->uri = $this->getUri($pa);
        $this->page = $page;
        $this->pageNum = ceil($this->total / $this->listRows);
        $this->limit = $this->setLimit();
    }

    public function setLimit()
    {
        return " LIMIT " . ($this->page - 1) * $this->listRows . ", {$this->listRows}";
    }

    private function getUri($pa)
    {
        $url = $_SERVER["REQUEST_URI"] . (strpos($_SERVER["REQUEST_URI"], '?') ? '' : "?") . $pa;
        $parse = parse_url($url);
        if (isset($parse["query"])) {
            parse_str($parse['query'], $params);
            unset($params["page"]);
            $url = $parse['path'] . '?' . http_build_query($params);
        }
        return $url;
    }

    function __get($args)
    {
        if ($args == "limit")
            return $this->limit;
        else
            return null;
    }

    private function prev()
    {
        $html = "";
        if ($this->page == 1)
            $html .= '';
        else
            $html .= "<li class=\"prev\"><a href='{$this->uri}page=" . ($this->page - 1) . "'>{$this->config["prev"]}</a></li>";

        return $html;
    }

    private function pageList()
    {
        $linkPage = "";
        $inum = floor($this->listNum / 2);
        for ($i = $inum; $i >= 1; $i--) {
            $page = $this->page - $i;
            if ($page < 1)
                continue;
                $linkPage .= "<li><a href='{$this->uri}page={$page}'>{$page}</a></li>";
        }
        $linkPage .= "<li class=\"current\"><a>{$this->page}</a></li>";
        for ($i = 1; $i <= $inum; $i++) {
            $page = $this->page + $i;
            if ($page <= $this->pageNum)
                $linkPage .= "<li><a href='{$this->uri}page={$page}'>{$page}</a></li>";
            else
                break;
        }
        return $linkPage;
    }

    private function next()
    {
        $html = "";
        if ($this->page == $this->pageNum)
            $html .= '';
        else
            $html .= "<li class=\"next\"><a href='{$this->uri}page=" . ($this->page + 1) . "'>{$this->config["next"]}</a></li>";
        return $html;
    }

    function render($display = [1,2,3])
    {
        $html[1] = $this->prev();
        $html[2] = $this->pageList();
        $html[3] = $this->next();
        $render = '<ol class="page-navigator">';
        foreach ($display as $index) {$render .= $html[$index];}
        $render .='</ol>';
        return $render;
    }
}