<?php

//创建服务
$http = new swoole_http_server("0.0.0.0", 8811);
//设置参数
$http->set(
    [
        'enable_static_handler' => true,    //开启静态资源目录
        'document_root' => "/root/workspace/Swoole/thinkphp/public/static",  //设置静态资源目录
        'worker_num' => 4,  //开启进程数
    ]
    );
//WorkerStart事件监听进程启动
//进程启动时发生,这里创建的对象可以在进程生命周期内使用
//在进程启动时加载ThinkPHP框架,启动程序放在request回调中
$http->on('WorkerStart', function(swoole_server $server,  $worker_id) {
    // 定义应用目录
    define('APP_PATH', __DIR__ . '/../application/');
    // 加载框架里面的文件
    require __DIR__ . '/../thinkphp/base.php';
//     require __DIR__ . '/../thinkphp/start.php';
});
//监听用户请求
//ThinkPHP Request对象是从PHP系统超全局数组 $_SERVER/$_GET/$_POST/$_SESSION 中获取访问信息，所以需要对这些数组进行初始化
//因为进程会常驻在内存中，所以在一次请求结束后相关的信息不会被销毁，因此需要在赋值手动清空

/*
 * 开启服务后测试访问tp路径
 * http://127.0.0.1:8811/?s=index/index/singwa&uu=fff&sdd=rrr&ghfgf=ppp
 */

$http->on('request', function($request, $response) use($http){
    
    $_SERVER  =  [];
    if(isset($request->server)) {
        foreach($request->server as $k => $v) {
            $_SERVER[strtoupper($k)] = $v;
        }
    }
    if(isset($request->header)) {
        foreach($request->header as $k => $v) {
            $_SERVER[strtoupper($k)] = $v;
        }
    }
    
    $_GET = [];
    if(isset($request->get)) {
        foreach($request->get as $k => $v) {
            $_GET[$k] = $v;
        }
    }
    
    $_POST = [];
    if(isset($request->post)) {
        foreach($request->post as $k => $v) {
            $_POST[$k] = $v;
        }
    }
    
    $_COOKIE = [];
    if(isset($request->cookie)) {
        foreach($request->cookie as $k => $v) {
            $_COOKIE[$k] = $v;
        }
    }
    //开启ob
    ob_start();
    // 执行应用并响应
    try {
        think\Container::get('app', [APP_PATH])
        ->run()
        ->send();
    }catch (\Exception $e) {
        // todo
    }
    //从ob中读取数据返回给客户端
    $res = ob_get_contents();
    ob_end_clean();
    $response->end($res);
});
        
$http->start();