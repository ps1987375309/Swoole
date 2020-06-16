<?php
/**
 * HTTP 优化 基础类库
 * User: singwa
 * Date: 18/3/2
 * Time: 上午12:34
 */

class Http {

    CONST HOST = "0.0.0.0";
    CONST PORT = 8811;

    public $http = null;
    public function __construct() {
        $this->http = new swoole_http_server(self::HOST, self::PORT);

        $this->http->set(
            [
                'enable_static_handler' => true,    //开启静态资源目录
                'document_root' => "/root/workspace/Swoole/thinkphp/public/static",  //设置静态资源目录
                'worker_num' => 4,
                'task_worker_num' => 4,
            ]
        );
        $this->http->on("workerstart", [$this, 'onWorkerStart']);
        $this->http->on("request", [$this, 'onRequest']);
        $this->http->on("task", [$this, 'onTask']);
        $this->http->on("finish", [$this, 'onFinish']);
        $this->http->on("close", [$this, 'onClose']);

        $this->http->start();
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
        
        $_POST['http_server'] = $this->http;
        
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
        $flag = $obj->$method($data['data']);
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
        echo "clientid:{$fd}\n";
    }
}

$obj = new Http();