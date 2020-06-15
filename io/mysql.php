<?php

/*
 * 协程写法
 */

// Co\run(function () {
//     $swoole_mysql = new Swoole\Coroutine\MySQL();
//     $swoole_mysql->connect([
//         'host'     => '127.0.0.1',
//         'port'     => 3306,
//         'user'     => 'root',
//         'password' => 'root',
//         'database' => 'chat',
//     ]);
//     $res = $swoole_mysql->query('select * from chat_chat_user');
//     var_dump($res);
// });

class AysMysql {
    /**
     * mysql的配置
     * @var array
     */
    public $dbConfig = [];
    
    public $db;
    
    public function __construct() {
        $this->dbConfig = [
            'host'     => '127.0.0.1',
            'port'     => 3306,
            'user'     => 'root',
            'password' => 'root',
            'database' => 'chat',
            'charset' => 'utf8',
        ];
        Co\run(function () {
            $this->db = new Swoole\Coroutine\MySQL();
            $result = $this->db->connect($this->dbConfig);  //返回的是true or false
            if($result === false){
                var_dump("连接失败");
            }
            $res = $this->db->query('select * from chat_chat_user');
            var_dump($res);
        });
    }
    /**
     * mysql 执行逻辑
     * @param $id
     * @param $username
     * @return bool
     */
    public function execute($id, $username) {
        Co\run(function ()use($id, $username) {
                $sql = "update chat_chat_user set `name` = '".$username."' where id=".$id;
                $res = $this->db->query($sql);
                var_dump($res );
            });
        return true;
    }
    
}

$obj = new AysMysql();
$flag = $obj->execute(1, '00001');
var_dump($flag).PHP_EOL;

