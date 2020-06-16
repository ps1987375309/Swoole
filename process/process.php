<?php
/*
 * 终端查看进程命令：ps aft | grep http_server    //http_server 开启的服务文件名
 * 查看一个进程的子进程命令：pstree -p 22727      //22727 是父类pid
 */


/*
 * 使用进程开启服务
 */

$process = new swoole_process(function(swoole_process $pro) {
    // todo
    // php redis.php
    $pro->exec("/usr/src/php-7.2.3/bin/php", [__DIR__.'/../server/http_server.php']);
}, false);

$pid = $process->start();
echo $pid . PHP_EOL;

swoole_process::wait();

/*
 * 创建 3 个子进程，主进程用 wait 回收进程
 * 主进程异常退出时，子进程会继续执行，完成所有任务后退出
 */

// use Swoole\Process;

// for ($n = 1; $n <= 3; $n++) {
//     $process = new Process(function () use ($n) {
//         echo 'Child #' . getmypid() . " start and sleep {$n}s" . PHP_EOL;
//         sleep($n);
//         echo 'Child #' . getmypid() . ' exit' . PHP_EOL;
//     });
//         $process->start();
// }
// for ($n = 3; $n--;) {
//     $status = Process::wait(true);
//     echo "Recycled #{$status['pid']}, code={$status['code']}, signal={$status['signal']}" . PHP_EOL;
// }
// echo 'Parent #' . getmypid() . ' exit' . PHP_EOL;
