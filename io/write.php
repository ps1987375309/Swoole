<?php
/**
 * descript: phpstrom
 * User: singwa
 * Date: 18/3/7
 * Time: 上午2:05
 */

$content = date("Ymd H:i:s").PHP_EOL;

go(function ()use($content){
    $result = Swoole\Coroutine\System::writeFile(__DIR__."/1.log", $content, FILE_APPEND);
    var_dump($result);
});
// file_put_contents();
echo "start".PHP_EOL;

/*
 * PHP_EOL是php换行
 * 上面的程序会先执行输出start，后执行var_dump
 */