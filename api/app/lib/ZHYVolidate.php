<?php

namespace app\lib;

use think\Validate;
use Illuminate\Support\Str;


class ZHYVolidate extends Validate
{
    public function __construct()
    {
        parent::extend('json', function($value) {
            if(is_array($value)){
                return true;
            }
            return Str::isJson($value);
        });
        parent::__construct();
    }
    /**
     * 获取错误信息
     * @param bool  $withKey 是否包含字段信息
     * @return array|string
     */
    public function getErrorInfo(bool $withKey = false)
    {
        if ($withKey || count($this->error) > 1) {
            return $this->error;
        }
        $first_error = array_values($this->error)[0];
        if(str_contains($first_error, '|')){
            $message_info = explode('|', $first_error);
            list($code, $error) = $message_info;
            return ['code' => $code, 'msg' => $error];
        }else{
            return empty($this->error) ? '' : $first_error;
        }
    }
    
}