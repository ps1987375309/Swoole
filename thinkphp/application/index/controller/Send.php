<?php
namespace app\index\controller;
use app\common\lib\ali\Sms;
use app\common\lib\Util;
use app\common\lib\Redis;
class Send
{
    /**
     * 发送验证码
     */
    public function index() {
        // tp  input
        //$phoneNum = request()->get('phone_num', 0, 'intval');
        $phoneNum = intval($_GET['phone_num']);
        if(empty($phoneNum)) {
            // status 0 1  message data
            return Util::show(config('code.error'), '手机号码为空');
        }
        
        // 生成一个随机数
        $code = rand(1000, 9999);
        
/**
 * 以下代码配合server文件里面的http服务对应使用
 * 注意：和下面的http_server服务同时只能使用一个
 * 推荐使用这个，这个是优化版
 * 由于没有开通阿里大于故此处不使用这个服务
 */  
        
//         $taskData = [
//             'method' => 'sendSms',
//             'data' => [
//                 'phone' => $phoneNum,
//                 'code' => $code,
//             ]
//         ];
//         $_POST['http_server']->task($taskData);
//         return Util::show(config('code.success'), 'ok');

        
/**
 * 以下代码配合server文件里面的http_server服务对应使用
 * 以下注释部分为阿里大于短信验证写法
 * 由于我没有开通大于短信服务，所以使用模拟写法
 */     
        try {
//             $response = Sms::sendSms($phoneNum, $code);
            $response['Code'] = "OK";
        }catch (\Exception $e) {
            return Util::show(config('code.error'), '阿里大于内部异常');
        }
//        if($response->Code === "OK") {
        if($response['Code'] === "OK") {
            // redis
            $redis = new \Swoole\Coroutine\Redis();
            $redis->connect(config('redis.host'), config('redis.port'));
            $redis->set(Redis::smsKey($phoneNum), $code, config('redis.out_time'));

            return Util::show(config('code.success'), 'success');
      } else {
           return Util::show(config('code.error'), '验证码发送失败');
        }

    }
}
