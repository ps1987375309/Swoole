<?php

/*
 * 协程写法
 */
// Co\run(function () {
//     $redis = new Swoole\Coroutine\Redis();
//     $redis->connect('127.0.0.1', 6379);
//     $redis->set('key',"test");
//     $val = $redis->get('key');
//     var_dump($val);
// });


/*
 * 一键协程化写法
 * 使用此方法必须安装phpredis驱动
 * 安装教程
 * 下载地址：https://github.com/phpredis/phpredis/releases    解压
 * 进入解压目录执行下面命令
 * $ //usr/src/php-7.2.3/bin/phpize              #  /usr/src/php-7.2.3 表示php安装路径
 * $ ./configure --with-php-config=/usr/src/php-7.2.3/bin/php-config          # /usr/src/php-7.2.3 表示php安装路径
 * $ make 
 * $ make install   
 * 安装完成后会显示如下信息是扩展文件的存放目录   
 * #Installing shared extensions:     /usr/src/php-7.2.3/lib/php/extensions/no-debug-non-zts-20170718/
 * 打开php.ini配置文件，添加扩展
 * extension=redis
 */

Co::set(['hook_flags' => SWOOLE_HOOK_ALL]);

Co\run(function() {
        go(function () {
            $redis = new Redis();
            $redis->connect('127.0.0.1', 6379);//此处产生协程调度，cpu切到下一个协程，不会阻塞进程
            $redis->set('key',"test");
            $val = $redis->get('key');//此处产生协程调度，cpu切到下一个协程，不会阻塞进程
            var_dump($val);
        });
});






