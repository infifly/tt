<?php
/**
 * User: lzw
 * Date: 2017/10/19
 * Time: 9:18
 * 防止xss 攻击
 */
namespace TT\util;
class HTMLPurifier{

    public static function filter($str)
    {
        $config = \HTMLPurifier_Config::createDefault();
        $purifier = new \HTMLPurifier($config);
        return $purifier->purify($str);
    }
}
?>


