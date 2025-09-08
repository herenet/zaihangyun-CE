<?php
namespace App\Services;

use Mrgoon\AliSms\AliSms;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private $obj = null;

    public function __construct()
    {
        $this->obj = new AliSms();
    }

    public function sendVerifyCode($phoneNumber, $code)
    {
        $response = $this->obj->sendSms($phoneNumber, config('aliyunsms.code_tmp_id'), ['code' => $code]);
        if ($response->Code == 'OK'){
            return true;
        }
        Log::channel('sms')->error($response->Message);
        return $response->Message;
    }
}