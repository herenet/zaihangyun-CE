<?php
namespace app\service;

use Exception;
use GuzzleHttp\Client;

class HuaweiService
{
    const REQUEST_SUCCESS_HTTP_CODE = 200;
    
    private $_client_id, $_client_secret;
    private Client $_client;
    
    public function __construct($clientId, $clientSecret)
    {
        $this->_client_id = $clientId;
        $this->_client_secret = $clientSecret;
        $this->_client = new Client();
    }
    
    /**
     * 用APP传来的Authorization Code换取Access Token
     * @param string $code APP端获取的授权码
     * @return array
     * @throws Exception
     */
    public function getAccessToken(string $code): array
    {
        $url = "https://oauth-login.cloud.huawei.com/oauth2/v3/token";
        $params = [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => $this->_client_id,
                'client_secret' => $this->_client_secret,
                'code' => $code,
            ]
        ];
        $response = $this->_client->post($url, $params);
        return $this->_getResponseContent($response);
    }
    
    /**
     * 通过Access Token获取用户信息（包括UnionID、OpenID、手机号等）
     * @param string $accessToken
     * @return array
     * @throws Exception
     */
    public function getUserInfo(string $accessToken): array
    {
        $url = "https://account.cloud.huawei.com/rest.php?nsp_svc=GOpen.User.getInfo";
        $params = [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'form_params' => [
                'access_token' => $accessToken,
                'getNickName' => 1,
            ]
        ];
        $response = $this->_client->post($url, $params);
        return $this->_getResponseContent($response);
    }
    
    /**
     * 处理HTTP响应
     * @param object $response
     * @return array
     * @throws Exception
     */
    private function _getResponseContent(object $response): array
    {
        $content = '';
        if ($response->getStatusCode() == self::REQUEST_SUCCESS_HTTP_CODE) {
            $content = json_decode($response->getBody()->getContents(), true);
            // 华为接口成功时不会返回error字段
            if (!isset($content['error'])) {
                return $content;
            }
        }
        throw new Exception(json_encode($content));
    }
}