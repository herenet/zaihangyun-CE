<?php

namespace app\exception;

use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;
use support\exception\Handler;
use Webman\RateLimiter\RateLimitException;

class ExceptionHandle extends Handler
{

    public function render(Request $request, Throwable $exception): Response
    {
        if($exception instanceof RateLimitException){
            return response(json_encode([
                "code" => config('const.request_rate_limit'),
                "message" => $exception->getMessage(),
            ],JSON_UNESCAPED_UNICODE));
        }
        return parent::render($request, $exception);
    }
}