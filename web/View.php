<?php
namespace TT\web;

class View{

    private $tpl="";

    private $content="";

    /**
     * 设置模板
     * @param $tpl
     */
    public function setTpl($tpl){
        $this->tpl=$tpl;
    }

    /**
     * 获取模板
     * @return string
     */
    public function getTpl(){
        return $this->tpl;
    }

    public function getContent(){
        return $this->content;
    }

    public function setContent($content){
        $this->content=$content;
    }

    public function debugbar(){
        $debugbar=\TT::debug();
        if(!$debugbar)return "";
        $debugbarRenderer = $debugbar->getJavascriptRenderer("./assets/debugbar/",null);
        return $debugbarRenderer;
    }

    /**
     * 渲染模板..
     */
    public function render($params){
        $debugbar=$this->debugbar();
        $tpl = $this->getTpl();
        ob_start();
        foreach($params as $k=>$v) $$k = $v;
        if($debugbar){
            $debug="debugbar";
            $$debug=$debugbar;
        }
        include($tpl);
        $data = ob_get_contents();
        ob_end_clean();
        if($debugbar) {
            $data = str_ireplace("</head>", $debugbar->renderHead()."</head>", $data);
            $data = str_ireplace("</html>", $debugbar->render()."</html>", $data);
        }
        return $data;
    }
}