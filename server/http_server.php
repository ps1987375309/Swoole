<?php
/**
 * Created by PhpStorm.
 * User: baidu
 * Date: 18/2/28
 * Time: 上午1:42
 */
$http = new swoole_http_server("0.0.0.0", 8811);

$http->set(
    [
        'enable_static_handler' => true,
        'document_root' => "/root/workspace/Swoole/html",
    ]
);
$http->on('request', function($request, $response) {
    //print_r($request->get);
    $content = [
        'date:' => date("Ymd H:i:s"),
        'get:' => $request->get,
        'post:' => $request->post,
        'header:' => $request->header,
    ];

    Swoole\Coroutine\System::writeFile(__DIR__."/access.log", json_encode($content).PHP_EOL, FILE_APPEND);
    $response->cookie("singwa", "xsssss", time() + 1800);
    $response->end("sss". json_encode($request->get));
});

$http->start();