<?php
namespace app\admin\controller;
use app\common\lib\Util;
use app\common\lib\redis\Predis;
class Live
{

    public function push() {
        
        if(empty($_GET)) {
            return Util::show(config('code.error'), 'error');
        }  // admin
        // token    md5(content)
        // => mysql
        $teams = [
            1 => [
                'name' => '马刺',
                'logo' => '/live/imgs/team1.png',
            ],
            4 => [
                'name' => '火箭',
                'logo' => '/live/imgs/team2.png',
            ],
        ];

        $data = [
            'type' => intval($_GET['type']),
            'title' => !empty($teams[$_GET['team_id']]['name']) ?$teams[$_GET['team_id']]['name'] : '直播员',
            'logo' => !empty($teams[$_GET['team_id']]['logo']) ?$teams[$_GET['team_id']]['logo'] : '',
            'content' => !empty($_GET['content']) ? $_GET['content'] : '',
            'image' => !empty($_GET['image']) ? $_GET['image'] : '',
        ];
        
        
        $server = $_POST['http_server'];
        foreach($server->ports[0]->connections as $fd) {
            var_dump($fd);
            if ($fd > 2) { //测试时系统默认会默认生产1,2   具体原因不详
                $server->push($fd, json_encode($data));
            }
        }
        

 /*
  * =================================以下功能正常======================================================
  */           
        
//         $client = Predis::getInstance()->sMembers(config("redis.live_game_key"));
//         foreach ($client as $fd){
//             $_POST['http_server']->push($fd,json_encode($data));
//         }

//         print_r($_GET);
        // 获取连接的用户
        // 赛况的基本信息入库   2、数据组织好 push到直播页面
//         $taskData = [
//             'method' => 'pushLive',
//             'data' => $data
//         ];
//         $_POST['http_server']->task($taskData);

//         return Util::show(config('code.success'), 'ok');
    }

}
