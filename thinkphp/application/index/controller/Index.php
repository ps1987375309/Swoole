<?php
namespace app\index\controller;
class Index
{
    public function index()
    {
        return "";
//        var_dump($_GET);
//        echo  '6666666666';
    }

    public function singwa() {
        echo time();
    }

    public function hello($name = 'ThinkPHP5')
    {
        echo 'hessdggsg' . $name.time();
    }

}
