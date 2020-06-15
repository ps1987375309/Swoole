<?php 

/*
 * swoole 中使用协程的 2 个要点:
 * 开协程: 这个容易, go() 一下就行了
 * 协程中执行 非阻塞代码: 下面提供一个简单的检测 demo
 */


go(function () {
    sleep(3); // 未开启协程 runtime, 此处会阻塞, 输出为 go -> main
    echo "go \n";
});
echo "main \n";

//输出为: go -> main



\Swoole\Runtime::enableCoroutine();   //未开启协程

go(function () {
    sleep(3); // 开启协程 runtime, 此处为阻塞,继续执行下面的语句 输出为 main -> go
    echo "go \n";
});
echo "main \n";

//输出为: main -> go, 发生了协程调度.