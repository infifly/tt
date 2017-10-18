<?php

/**
 * Class TT
 * lzw
 */
namespace TT;

use DebugBar\StandardDebugBar;
use TT\web\ErrorHandler;
use TT\helpers\FileUtil;

defined('APP_PATH') or define('APP_PATH', dirname(dirname(dirname(__DIR__))));
defined('VENDOR_PATH') or define('VENDOR_PATH',dirname(dirname(__DIR__)));
defined('RUNTIME_PATH') or define('RUNTIME_PATH',APP_PATH."/runtime");
defined('LOG_PATH') or define('LOG_PATH',APP_PATH."/runtime/log");

abstract class TT{

    private static $configs=[];
    private static $container=[];
    public static $classloader=[];

    /**
     * 配置文件
     * @param array $configs
     */
    public static function setConfigs($configs=[]){
        self::$configs=$configs;
    }

    public static function getConfig($name){
        return self::$configs[$name];
    }

    public static function getContainer($name){
        if(!$name)return self::$container;
        return self::$container[$name];
    }

    public static function getClassPath($class)
    {
        return self::$classloader->findFile($class);
    }

    /**
     * 初始化数据
     */
    public static function init(){
        $c=[
            'request'=>"\TT\web\Request",
            'view'=>"\TT\web\View",
        ];
        foreach($c as $k=>$v){
            self::$container[$k]=new $v();
        }
        FileUtil::createDirs(LOG_PATH);
        (new ErrorHandler())->register();

        if(self::getConfig("env")!="prod"){
            $debugbar = new StandardDebugBar();
            self::$container['debug']=$debugbar;
        }
    }

    /**
     * @return StandardDebugBar|mixed
     */
    public static function debug(){
        $debug=self::getContainer("debug");
        if(!$debug){
           return false;
        }
        return $debug;
    }

    /**
     * 添加调试信息
     * @param $msg
     */
    public static function setDebugMessage($msg){
        if(self::debug()){
            self::debug()['messages']->addMessage($msg);
        }
    }

    public static function setException($exception){
        if(self::debug()){
            self::debug()['exceptions']->addException($exception);
        }
    }

    /**
     * 初始化类
     * @param $classname
     * @param string $param
     * @return mixed
     * @throws Exception
     */
    public static function createClass($classname,$param=""){
        if(is_string($classname)){
            return new $classname($param);
        }else{
            throw new Exception("class create error: {$classname}");
        }
    }
}
TT::$classloader=require (VENDOR_PATH."/autoload.php");