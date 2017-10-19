<?php
namespace TT\web;
use TT\helpers\Log;


class ErrorHandler{
    /**
     * 注册错误处理
     */
    public function register(){
        error_reporting(0);
        ini_set("display_errors",false);
        set_exception_handler([$this,"exceptionHandler"]);
        set_error_handler([$this,"errorHandler"]);
        register_shutdown_function([$this, 'handleFatalError']);
    }

    public function handleFatalError(){
        $e = error_get_last();
        $this->exceptionHandler($e);
        //return $this->exceptionHandler($e);

        exit(1);
    }

    public function errorHandler($code, $message, $file, $line){

        if (error_reporting() & $code) {
            $exception = new \ErrorException($message, $code, $code, $file, $line);
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            array_shift($trace);
            foreach ($trace as $frame) {
                if ($frame['function'] === '__toString') {
                    $this->exceptionHandler($exception);
                    if (defined('HHVM_VERSION')) {
                        flush();
                    }
                    exit(1);
                }
            }

            throw $exception;
        }else{
            return false;
        }
    }

    public function exceptionHandler($exception){
        $this->logError($exception);
        restore_error_handler();
        restore_exception_handler();

        if(PHP_SAPI!="cli"){
            $code=500;
            $msg="Internal Server Error";
            header('HTTP/1.1 '.$code.' '.$msg);
            header('Status:'.$code.' '.$msg);
        }
        //exit(1);
    }
    public function object_to_array($obj) {
        $obj = (array)$obj;
        foreach ($obj as $k => $v) {
            if (gettype($v) == 'resource') {
                return;
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $obj[$k] = (array)$this->object_to_array($v);
            }
        }
        return $obj;
    }
    public function logError($error){
        $env=\TT::getConfig("env");

        $error=$this->parseException($error);
        $str="";
        if($env=="prod"){
            $br="\r\n";
            $str.=$error['message'].$br;
        }else{
            $br="<br>";
            $str.="<font size=\"3\" color=\"red\">".$error['message'].$br."</font>";
        }
        foreach($error['files'] as $key=>$value)
        {
            $tmp="line: ";
            foreach($value as $k=>$v){
                $tmp.=$v." ";
            }
            $str.= $key . " : " . $tmp . $br;
        }

        if($env=="prod"){
            Log::writeLog("error",$str);
        }else{
            echo $str;
        }
    }
    /**
     * 解析异常信息
     * @param object $e
     * @return array
     */
    public function parseException($e)
    {
        $trace = $e->getTrace();
        $files = [];$pro=[];$sys = [];
        $gfile = $e->getFile();
        if(!empty($gfile)){
            $files[] = ['file'=>$gfile,'line'=>$e->getLine()];
        }
        foreach($trace as $t){
            if(!empty($t['file'])){
                $files[] = ['file'=>$t['file'],'line'=>$t['line']];
            }
        }
        foreach ($files as $t){
            if(!empty($t['file'])){
                $file = $t['file']; unset($t['file']);
                if(strpos($file,H2O_PATH) !== false){
                    $sys[$file][] = $t['line'];
                }else{
                    $pro[$file][] = $t['line'];
                }
            }
        }
        $files = array_merge($pro,$sys);
        return ['message'=>$e->getMessage(),'files'=>$files];
    }
}