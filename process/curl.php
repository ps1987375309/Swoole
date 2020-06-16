<?php
/**
 * 同步的流程是每次遍历一次需要sleep 1秒，那么6个地址需要6秒
 * 异步写法同时开启6个子进程对应处理6次循环，那么就会同时进行操作，只需要1秒完成
 */

echo "process-start-time:".date("Ymd H:i:s").PHP_EOL;
$workers = [];
$urls = [
    'http://baidu.com',
    'http://sina.com.cn',
    'http://qq.com',
    'http://baidu.com?search=singwa',
    'http://baidu.com?search=singwa2',
    'http://baidu.com?search=imooc',
];

for($i = 0; $i < 6; $i++) {
    // 子进程
    $process = new swoole_process(function(swoole_process $worker) use($i, $urls) {
        // curl
        $content = curlData($urls[$i]);
        //echo $content.PHP_EOL;
        $worker->write($content.PHP_EOL);
    }, true);
    $pid = $process->start();
    $workers[$pid] = $process;
}

foreach($workers as $process) {
    echo $process->read();
}
/**
 * 模拟请求URL的内容  1s
 * @param $url
 * @return string
 */
function curlData($url) {
    // curl file_get_contents
    sleep(1);
    return $url . " success".PHP_EOL;
}
echo "process-end-time:".date("Ymd H:i:s").PHP_EOL;