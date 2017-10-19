<?php
namespace TT\web;


use TT\util\HTMLPurifier;

class Request{

    private $_getData;
    private $_postData;

    public function __construct()
    {
        $this->_getData=$_GET;
        $this->_postData=$_POST;
    }

    /**
     * post数据
     * @param string $key
     * @return string
     */
    public function post($key=""){
        if($key==""){
            $data=$this->_postData;
            //过滤xss 仅支持３三层深
            foreach ($data as $k=>$v){
                if(is_array($v)){
                    foreach ($v as $kk=>$vv){
                        if(is_array($vv)){
                            foreach ($vv as $kkk=>$vvv){
                                if(is_array($vvv)){
                                    $data[$k][$kk][$kkk] = HTMLPurifier::filter(implode(",",$vvv));
                                }else{
                                    $data[$k][$kk][$kkk] = HTMLPurifier::filter($vvv);
                                }
                            }
                        }else{
                            $data[$k][$kk] = HTMLPurifier::filter($vv);
                        }
                    }
                }else{
                    $data[$k] = HTMLPurifier::filter($v);
                }
            }
            return $data;
        }else{
            return isset($this->_postData[$key])?HTMLPurifier::filter($this->_postData[$key]):"";
        }
    }

    /**
     * get数据
     * @param string $key
     * @return string
     */
    public function get($key=""){
        if($key==""){
            return $this->_getData;
        }else{
            return isset($this->_getData[$key])?$this->_getData[$key]:"";
        }
    }


    /**
     * 是否是ajax
     * @return bool
     */
    public function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    /**
     * 是否是delete
     * @return bool
     */
    public function isDel(){
        return $this->getMethod() === 'DELETE';
    }

    /**
     * 是否是get
     * @return bool
     */
    public function isGet(){
        return $this->getMethod() === 'GET';
    }

    /**
     * 是否是post
     * @return bool
     */
    public function isPost(){
        return $this->getMethod() === 'POST';
    }

    /**
     * 获取 Post,get,head....
     * @return string
     */
    public function getMethod()
    {
        if (isset($_POST['__method'])) { //delete,put..
            return strtoupper($_POST['__method']);
        } elseif (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
            return strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
        } else {
            return isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';
        }
    }

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