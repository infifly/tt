<?php
namespace TT\core;


class App{

    /**
     * 构造函数
     * App constructor.
     * @param array $configs
     */
    public function __construct($configs=[])
    {
        \TT::setConfigs($configs);
        \TT::init();
    }

    /**
     * 执行入口操作
     */
    public function run(){
        $request=\TT::getContainer('request');
        $rt=$request->getRouter();
        echo $request->runAction($rt);
    }
}
