<?php
namespace TT\web;

use DebugBar\DataCollector\ExceptionsCollector;
use DebugBar\StandardDebugBar;

abstract class Controller{

    private $layout='';
    private $content="";


    public function __construct()
    {
        $layout=\TT::getConfig("defaultlayout");
        if($layout){
            $this->setLayout($layout);
        }
    }

    public function clearLayout(){
        $this->layout="";
    }

    public function setLayout($layout){
        $this->layout=$layout;
    }

    public function setContent($content){
        $this->content=$content;
    }

    public function getContent(){
        return $this->content;
    }


    /**
     * 渲染模板
     * @param $tpl
     * @param $params
     */
    public function render($tpl,$params=[]){
        $view=\TT::getContainer("view");
        $view->setTpl($this->getViewPath()."/".$tpl.".php");
        $view->setContent($this->getContent());
        $content=$view->render($params);
        if($this->content!=""||$this->layout==""){
            return $content;
        }
        $layout=explode(".",$this->layout);
        $lclass=\TT::createClass($layout[0]."Controller");
        $lclass->setContent($content);
        return call_user_func([$lclass,$layout[1]."Action"]);
    }

    /**
     * 计算模板位置
     * @return string
     */
    public function getViewPath(){
        //controller
        $reflector = new \ReflectionClass($this);
        return  dirname($reflector->getFileName())."/".'views'."/".strtolower(substr($reflector->getShortName(),0,-10));
    }
}