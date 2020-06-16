<?php

class Ws {

    CONST HOST = "0.0.0.0";
    CONST PORT = 8811;

    public $ws = null;
    public function __construct() {
        $this->ws = new swoole_websocket_server(self::HOST, self::PORT);

        $this->ws->set(
            [
                'enable_static_handler' => true,    //开启静态资源目录
                'document_root' => "/root/workspace/Swoole/thinkphp/public/static",  //设置静态资源目录
                'worker_num' => 4,
                'task_worker_num' => 4,
            ]
        );
        
        $this->ws->on("open", [$this, 'onOpen']);
        $this->ws->on("message", [$this, 'onMessage']);
        $this->ws->on("workerstart", [$this, 'onWorkerStart']);
        $this->ws->on("request", [$this, 'onRequest']);
        $this->ws->on("task", [$this, 'onTask']);
        $this->ws->on("finish", [$this, 'onFinish']);
        $this->ws->on("close", [$this, 'onClose']);

        $this->ws->start();
    }
    
    
    /**
     * 监听ws连接事件
     * @param $ws
     * @param $request
     */
    public function onOpen($ws, $request) {
        \app\common\lib\redis\Predis::getInstance()->sadd(config("redis.live_game_key"),$request->fd);
        var_dump($request->fd);
        
    }
    
    /**
     * 监听ws消息事件
     * @param $ws
     * @param $frame
     */
    public function onMessage($ws, $frame) {
        echo "ser-push-message:{$frame->data}\n";    //接收客户端发送的数据，并输出在终端
        $ws->push($frame->fd, "server-push:".date("Y-m-d H:i:s"));
    }
    
    /**
     * onWorkerStart回调
     * @param unknown $server
     * @param unknown $worker_id
     */
    public function onWorkerStart($server,  $worker_id){
        // 定义应用目录
        define('APP_PATH', __DIR__ . '/../application/');
        // 加载框架里面的文件
        require __DIR__ . '/../thinkphp/start.php';
    }
    
    /**
     * 监听http request回调
     * @param unknown $request
     * @param unknown $response
     */
    
    public function onRequest($request, $response){
        $_SERVER  =  [];
        if(isset($request->server)) {
            foreach($request->server as $k => $v) {
                $_SERVER[strtoupper($k)] = $v;
            }
        }
        if(isset($request->header)) {
            foreach($request->header as $k => $v) {
                $_SERVER[strtoupper($k)] = $v;
            }
        }
        
        $_FILES = [];
        if(isset($request->files)) {
            foreach($request->files as $k => $v) {
                $_FILES[$k] = $v;
            }
        }
        
        $_GET = [];
        if(isset($request->get)) {
            foreach($request->get as $k => $v) {
                $_GET[$k] = $v;
            }
        }
        
        $_POST = [];
        if(isset($request->post)) {
            foreach($request->post as $k => $v) {
                $_POST[$k] = $v;
            }
        }
        
        $_COOKIE = [];
        if(isset($request->cookie)) {
            foreach($request->cookie as $k => $v) {
                $_COOKIE[$k] = $v;
            }
        }
        
        $_POST['http_server'] = $this->ws;
        
        //开启ob
        ob_start();
        // 执行应用并响应
        try {
            think\Container::get('app', [APP_PATH])
            ->run()
            ->send();
        }catch (\Exception $e) {
            // todo
        }
        //从ob中读取数据返回给客户端
        $res = ob_get_contents();
        ob_end_clean();
        $response->end($res);
    }

    /**
     * @param $serv
     * @param $taskId
     * @param $workerId
     * @param $data
     */
    public function onTask($serv, $taskId, $workerId, $data) {
        //分发task任务机制，让不同的任务走不同的逻辑
        $obj = new app\common\lib\task\Task();
        $method = $data['method'];
        $flag = $obj->$method($data['data'],$serv);
        return $flag;
    }

    /**
     * @param $serv
     * @param $taskId
     * @param $data
     */
    public function onFinish($serv, $taskId, $data) {
        echo "taskId:{$taskId}\n";
        echo "finish-data-sucess:{$data}\n";
    }

    /**
     * close
     * @param $ws
     * @param $fd
     */
    public function onClose($ws, $fd) {
        \app\common\lib\redis\Predis::getInstance()->srem(config("redis.live_game_key"),$fd);
        echo "clientid:{$fd}\n";
    }
}

$obj = new Ws();