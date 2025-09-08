<?php
namespace app\service;

use Exception;
use GuzzleHttp\Client;

class WechatService
{
    const REQUEST_SUCCESS_HTTP_CODE = 200;

    private $_ak, $_sk;
    private $_grant_type = 'authorization_code';
    private Client $_client;

    public function __construct($wechatAppkey, $wechatSecret)
    {
        $this->_ak = $wechatAppkey;
        $this->_sk = $wechatSecret;
        $this->_client = new Client();
    }

    public function getAccessToken(string $code)
    {
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token";
        $params = [
            'query' => [
                'appid' => $this->_ak,
                'secret' => $this->_sk,
                'code' => $code,
                'grant_type' => $this->_grant_type,
            ]
        ];
        $response = $this->_client->get($url, $params);
        return $this->_getResponseContent($response);
    }

    public function getUserinfo(string $accessToken, string $openId) : array
    {
        $url = 'https://api.weixin.qq.com/sns/userinfo';
        $params = [
            'query'=> [
                'access_token' => $accessToken,
                'openid' => $openId,
            ]
        ];
        $response = $this->_client->get($url, $params);
        return $this->_getResponseContent($response);
    }

    /**
     * @throws Exception
     */
    private function _getResponseContent(object $response) : array
    {
        $content = '';
        if ($response->getStatusCode() == self::REQUEST_SUCCESS_HTTP_CODE) {
            $content = json_decode($response->getBody()->getContents(), true);
            if (!isset($content['errcode'])) {
                return $content;
            }
        }
        throw new Exception(json_encode($content));
    }
}