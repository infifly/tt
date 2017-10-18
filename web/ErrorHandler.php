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
            http_send_status(500);
        }
        //exit(1);
    }

    public function logError($error){

        $env=\TT::getConfig("env");
        if($env=="prod"){
            Log::writeLog("",$error);
        }else{
            echo $error;
        }
    }
}