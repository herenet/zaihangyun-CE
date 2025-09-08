<?php

namespace app\middleware;

use ReflectionClass;
use app\model\UserToken;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class ApiTokenCheck implements MiddlewareInterface
{
    public function process(Request $request, callable $handler): Response
    {
        $controller = new ReflectionClass($request->controller);
        $noNeedAuth = $controller->getDefaultProperties()['noNeedAuth'] ?? [];
        if (in_array($request->action, $noNeedAuth) || in_array('*', $noNeedAuth)) {
            return $handler($request);
        }

        $token = $request->header('Authorization');
        
        if (!$token) {
            return json(['code' => config('const.request_invalid'), 'msg' => 'Token is required']);
        }

        $token = str_replace('Bearer ', '', $token);
        $token = trim($token);

        $user_token_model = new UserToken();
        $token_info = $user_token_model->getTokenInfoByToken($token);
        if (!$token_info) {
            return json(['code' => config('const.request_unauthorized'), 'msg' => 'Unauthorized']);
        }

        if($token_info['expired_at'] < time()){
            return json(['code' => config('const.request_token_expired'), 'msg' => 'Token expired']);
        }

        $request->token_info = $token_info;

        return $handler($request);
    }
}