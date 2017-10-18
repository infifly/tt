<?php
namespace TT\web;


class Request{

    /**
     * 分析路由:localhost/td1/test/web/a.b?id=8 得到 a b
     * @return array
     */
    public function getRouter(){
        $uri=$_SERVER['REQUEST_URI'];
        $ex=explode("/",$uri);
        $ex=array_reverse($ex);
        if(count($ex)>0){
            $uri=$ex[0];
            $pos=strpos($uri,"?");
            $controller=$pos===false?$uri:substr($uri,0,$pos);
            $route=explode(".",$controller);
            if(sizeof($route)==1){
                $route[1]="index";
            }
        }
        if(sizeof($route)==0){
            $route[0]="site";
            $route[1]="index";
        }
        return $route;
    }
    public function runAction($cl){
        $route=$this->getRouter();
        $route[0]=ucfirst($route[0]);
        $route[1]=lcfirst($route[1]);
        $classstr=APP_ROOT_NAME."\\"."controllers"."\\".$route[0]."Controller";
        //文件存在，并且方法存在
        if(\TT::getClassPath($classstr)&&method_exists($classstr,$route[1]."Action")){
            return call_user_func([new $classstr(),$route[1]."Action"]);
        }else{
            $page=\TT::getConfig("404page");
            $page=explode(".",$page);
            http_response_code(404);
            $ctr=$page[0]."Controller";
            return  call_user_func([new $ctr(),$page[1]."Action"]);
        }
    }
}