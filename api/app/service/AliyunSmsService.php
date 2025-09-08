<?php
namespace app\service;

use Mrgoon\AliSms\AliSms;

class AliyunSmsService
{
    private $obj = null;

    public function __construct()
    {
        $this->obj = new AliSms();
    }

    public function sendVerifyCode($phoneNumber, array $params, $templateCode, $accessKey, $accessSecret, $signName)
    {
        $config = [
            'access_key' => $accessKey,
            'access_secret' => $accessSecret,
            'sign_name' => $signName,
        ];
        $response = $this->obj->sendSms($phoneNumber, $templateCode, $params, $config);
        if ($response->Code == 'OK'){
            return true;
        }
        return $response->Message;
    }
}