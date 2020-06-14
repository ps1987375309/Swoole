<?php
/*
 * 开启本服务,终端运行
 * php udp_server.php
 *
 * 客户端测试，UDP 服务器使用 netcat -u 来连接测试
 * netcat -u 127.0.0.1 9502
 * 输入数据如：
 * hello
 * 返回结果
 * Server: hello
 */

//创建Server对象，监听 127.0.0.1:9502端口，类型为SWOOLE_SOCK_UDP
$serv = new Swoole\Server("127.0.0.1", 9502, SWOOLE_PROCESS, SWOOLE_SOCK_UDP); 

//监听数据接收事件
$serv->on('Packet', function ($serv, $data, $clientInfo) {
    $serv->sendto($clientInfo['address'], $clientInfo['port'], "Server ".$data);
    var_dump($clientInfo);
});

//启动服务器
$serv->start(); 