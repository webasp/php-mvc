<?php
namespace core;

class Tpl {
    public $content;

    // 普通变量
    private function parVar(){
        $pattern1='/\{\$(\w+).(\w+)\}/';
        $pattern2='/\{\#(\w+)\}/';
        if(preg_match($pattern1,$this->content)){
            $this->content=preg_replace($pattern1, "<?php echo \$this->assign['$1']['$2'];?>",$this->content);
        }
        if(preg_match($pattern2,$this->content)){
            $this->content=preg_replace($pattern2, "<?php echo \$this->assign['$1'];?>",$this->content);
        }
    }

    // 解析系统变量
    private function parConfig(){
        $pattern='/\{@\$(\w+)\}/';
        if(preg_match($pattern,$this->content)){
            $this->content=preg_replace($pattern, "<?php echo core\Config::get('$1');?>",$this->content);
        }
    }

    // 解析if标签
    private function parIf(){
        $pattenIf = '/\{if\s+\$([\w]+)\}/';
        $pattenEndIf = '/\{\/if\}/';
        $pattenElse = '/\{else\}/';
        if (preg_match($pattenIf,$this->content)) {
            if (preg_match($pattenEndIf,$this->content)) {
                $this->content = preg_replace($pattenIf,"<?php if (\$this->assign['$1']) {?>",$this->content);
                $this->content = preg_replace($pattenEndIf,"<?php } ?>",$this->content);
                if (preg_match($pattenElse,$this->content)) {
                    $this->content = preg_replace($pattenElse,"<?php } else { ?>",$this->content);
                }
            } else {
                throw new Exception('IF语句没有关闭');
            }
        }
    }

    // 解析Foreach标签
    private function parForeach() {
        $pattenForeach = '/\{foreach\s+\$([\w]+)\(([\w]+),([\w]+)\)\}/';
        $qpattenForeach = '/\{foreach\s+\@([\w]+)\(([\w]+),([\w]+)\)\}/';
        $qpattenVar = '/\{@@([\w]+)\}/';
        $pattenEndForeach = '/\{\/foreach\}/';
        $pattenVar = '/\{@([\w]+).([\w]+)\}/';
        if (preg_match($pattenForeach,$this->content)) {
            if (preg_match($pattenEndForeach,$this->content)) {
                $this->content = preg_replace($pattenForeach,"<?php foreach (\$this->assign['$1'] as \$$2=>\$$3) { ?>",$this->content);
                $this->content = preg_replace($qpattenForeach,"<?php foreach (\$$1 as \$$2=>\$$3) { ?>",$this->content);
                $this->content = preg_replace($pattenEndForeach,"<?php } ?>",$this->content);
                if (preg_match($pattenVar,$this->content)) {
                    $this->content = preg_replace($pattenVar,"<?php echo \$$1['$2'];?>",$this->content);

                }
                if (preg_match($qpattenVar,$this->content)) {
                    $this->content = preg_replace($qpattenVar,"<?php echo \$$1;?>",$this->content);
                }

            } else {
                throw new Exception('Foreach语句必须有结尾标签');
            }
        }
    }

    //解析include语句
    private function parInclude(){
        $patten = '/\{@include\s+\"([\w\.\-]+)\"\}/';
        if(preg_match_all($patten, $this->content,$file)){
            foreach ($file[1] as $value){
                $tpl = APP_PATH.'/view/'.$value.'.html';
                if(!file_exists($tpl)){
                    throw new Exception($tpl.'包含文件出错');
                }
            }
            $this->content = preg_replace($patten, "<?php \$this->create('$1')?>", $this->content);
        }
    }


    public function compile($parPath){
        $this->parVar();
        $this->parConfig();
        $this->parIf();
        $this->parForeach();
        $this->parInclude();
        if(!file_put_contents($parPath,$this->content)){
            throw new Exception('编译文件生成出错');
        }
    }
}