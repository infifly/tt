<?php
namespace TT\helpers;
class Log{
    /**
     * 写日志　,默认写到log目录
     * @param string $name
     * @param $msg
     */
    public static function writeLog($name="",$msg){
        if($name){
            $path=LOG_PATH."/".date("Y-m-d")."-".$name.".log";
        }else{
            $path=LOG_PATH."/".date("Y-m-d").".log";
        }
        FileUtil::write($path,$msg);
    }
}