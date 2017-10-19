<?php
namespace TT\core;
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

        $error = error_get_last();
        //处理错误范围
        if(!in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING])){
            return;
        }
        $exception = new \ErrorException($error['message'], $error['type'], $error['type'], $error['file'], $error['line']);
        $this->exceptionHandler($exception);
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
        $files = [];$pro=[];
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
                $file = $t['file'];
                unset($t['file']);
                $pro[$file][] = $t['line'];
            }
        }
        return ['message'=>$e->getMessage(),'files'=>$pro];
    }
}