<?php

namespace App\Admin;

trait AppKey
{
    protected $appKey;

    public function getAppKey()
    {
        //从请求头中获取app_key
        $this->appKey = request()->route('app_key');
        return $this->appKey;
    }
}