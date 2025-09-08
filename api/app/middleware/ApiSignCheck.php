<?php

namespace app\middleware;

use app\model\App;
use Webman\Http\Request;
use Webman\Http\Response;
use Illuminate\Support\Arr;
use Webman\MiddlewareInterface;

class ApiSignCheck implements MiddlewareInterface
{
    public function process(Request $request, callable $handler): Response
    {
        $params = $request->all();
        $sign = Arr::get($params, 'sign');
        $appkey = Arr::get($params, 'appkey');
        $timestamp = Arr::get($params, 'timestamp');

        if (empty($sign) || empty($appkey) || empty($timestamp)) {
            return json(['code' => config('const.request_invalid'), 'msg' => 'sign, appkey, timestamp is required']);
        }

        if(strlen($timestamp) != 10){
            return json(['code' => config('const.request_invalid'), 'msg' => 'timestamp only needs seconds']);
        }

        $app_model = new App();
        $app_info = $app_model->getAppInfoByAppKey($appkey);
        if(empty($app_info)){
            return json(['code' => config('const.request_invalid'), 'msg' => 'appkey not found']);
        }

        $now = time();
        $time_diff = abs($now - $timestamp);
        if ($time_diff > 300) {
            return json(['code' => config('const.request_unauthorized'), 'msg' => 'timestamp is invalid']);
        }

        $generate_sign = md5($appkey . $timestamp . $app_info['app_secret']);

        if (strtolower($sign) !== $generate_sign) {
            return json(['code' => config('const.request_unauthorized'), 'msg' => 'sign is invalid']);
        }

        return $handler($request);
    }
}