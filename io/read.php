<?php
/**
 * descript: phpstrom
 * User: singwa
 * Date: 18/3/7
 * Time: 上午1:53
 */

/**
 * 读取文件
 * __DIR__
 */
go(function() {
    $result = Swoole\Coroutine\System::readFile(__DIR__."/1.txt");
    var_dump($result);
});

echo "start".PHP_EOL;

/*
 * PHP_EOL是php换行
 * 上面的程序会先执行输出start，后执行var_dump
 */