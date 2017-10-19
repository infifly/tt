<?php
/**
 * User: lzw
 * Date: 2017/10/19
 * Time: 15:00
 */

namespace TT\db;

class Db{
    private static $db;

    /**
     * @param string $conf
     * @return \MysqliDb
     */
    public static function getInstance($conf="db")
    {
        if(!self::$db){
            $arr=\TT::getConfig($conf)[$conf];
            self::$db = new \MysqliDb ($arr['host'], $arr['username'],$arr['password'], $arr['dbname']);
        }
        return self::$db;
    }
}