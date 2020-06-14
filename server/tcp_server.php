<?php
//创建Server对象，监听 127.0.0.1:9501端口
$serv = new Swoole\Server("127.0.0.1", 9501);

$serv->set([
    'worker_num' => 4 , // worker进程数 cpu 1-4
    'max_request' => 10000,
]);

/*
 *服务器可以同时被成千上万个客户端连接，$fd 就是客户端连接的唯一标识符
 *调用 $server->send() 方法向客户端连接发送数据，参数就是 $fd 客户端标识符
 *调用 $server->close() 方法可以强制关闭某个客户端连接
 *客户端可能会主动断开连接，此时会触发 onClose 事件回调
 *$reactor_id线程id
 */
//监听连接进入事件
$serv->on('Connect', function ($serv, $fd,$reactor_id) {
    echo "标识id：".$fd." 线程id：".$reactor_id." Client: Connect.\n";
});
    
//监听数据接收事件
/*
 * $from_id线程id同上$reactor_id
 * send发送的信息是给客户端的
 */
$serv->on('Receive', function ($serv, $fd, $from_id, $data) {
    $serv->send($fd, "服务器监听到你的信息。你的标识id是：".$fd."你刚发送的数据是：".$data);
});
        
//监听连接关闭事件
$serv->on('Close', function ($serv, $fd) {
    echo "服务已关闭 Client: Close.\n";
});
    
//启动服务器
$serv->start(); 