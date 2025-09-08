<?php

namespace app\controller\api;

use support\Log;
use app\model\App;
use support\Cache;
use app\model\User;
use support\Request;
use app\lib\ZHYToken;
use Webman\Event\Event;
use app\model\UserToken;
use AppleSignIn\ASDecoder;
use Illuminate\Support\Arr;
use app\validate\AppleLogin;
use app\validate\HuaweiLogin;
use app\validate\MobileLogin;
use app\validate\WechatLogin;
use app\service\HuaweiService;
use app\service\WechatService;
use app\model\AliyunAccessConfig;
use app\service\AliyunSmsService;
use app\validate\LoginVerifyCode;
use app\model\LoginInterfaceConfig;
use app\model\WechatOpenPlatformConfig;

class LoginController
{
    protected $noNeedAuth = ['*'];
    
    const CACHE_KEY_VERIFY_CODE = 'verify_code|{appkey}|{phone_number}';

    public function verifyCode(Request $request)
    {
        $validate = new LoginVerifyCode();
        if (!$validate->check($request->all())) {
            return json($validate->getErrorInfo());
        }

        $appkey = $request->input('appkey');
        $login_interface_config_model = new LoginInterfaceConfig();
        $login_interface_config = $login_interface_config_model->getLoginInterfaceConfigByAppKey($appkey);
        if(empty($login_interface_config)){
            return json(['code' => 400198, 'msg' => 'login interface config not found']);
        }

        if($login_interface_config['switch'] == 0){
            return json(['code' => 400197, 'msg' => 'login interface is not enabled']);
        }

        if($login_interface_config['suport_mobile_login'] == 0){
            return json(['code' => 400196, 'msg' => 'mobile login is not open']);
        }

        if(empty($login_interface_config['aliyun_access_config_id'])){
            return json(['code' => 400195, 'msg' => 'aliyun access config not found']);
        }

        if(empty($login_interface_config['aliyun_sms_sign_name'])){
            return json(['code' => 400194, 'msg' => 'aliyun sms sign name not found']);
        }

        if(empty($login_interface_config['aliyun_sms_tmp_code'])){
            return json(['code' => 400193, 'msg' => 'aliyun sms template code not found']);
        }

        $aliyun_access_config_model = new AliyunAccessConfig();
        $aliyun_access_config = $aliyun_access_config_model->getAliyunAccessConfigById($login_interface_config['aliyun_access_config_id']);
        if(empty($aliyun_access_config)){
            return json(['code' => 400192, 'msg' => 'aliyun access_key config not found']);
        }

        $mcode = $request->input('mcode');
        $mobile = $request->input('mobile');
        $cache_ttl = $login_interface_config['aliyun_sms_verify_code_expire'] ?? 5;
        $phone_number = $mcode . $mobile;
        $code = rand(100000, 999999);
        
        try {
            $aliyun_sms_service = new AliyunSmsService();
            $rs = $aliyun_sms_service->sendVerifyCode(
                $phone_number, 
                ['code' => $code],
                $login_interface_config['aliyun_sms_tmp_code'], 
                $aliyun_access_config['access_key'],
                $aliyun_access_config['access_key_secret'],
                $login_interface_config['aliyun_sms_sign_name'],
            );
            if($rs === true){
                $cache_key = str_replace('{appkey}', $appkey, self::CACHE_KEY_VERIFY_CODE);
                $cache_key = str_replace('{phone_number}', $phone_number, $cache_key);
                Cache::set($cache_key, $code, 60*$cache_ttl);
                return json(['code' => config('const.request_success'), 'msg' => 'success']);
            }
            return json(['code' => 400191, 'msg' => $rs]);
        } catch (\Exception $e) {
            return json(['code' => 400190, 'msg' => $e->getMessage()]);
        }
    }

    public function mobile(Request $request)
    {
        $validate = new MobileLogin();

        if (!$validate->check($request->all())) {
            return json($validate->getErrorInfo());
        }

        $appkey = $request->input('appkey');
        $mcode = $request->input('mcode');
        $mobile = $request->input('mobile');
        $verify_code = $request->input('verify_code');
        $version_number = $request->input('version_number');
        $channel = $request->input('channel');
        $need_user_detail = $request->input('need_user_detail', 0);
        $ip = $request->getRealIp();
        $oaid = $request->input('oaid');
        $device_id = $request->input('device_id');

        $app_model = new App();
        $app_info = $app_model->getAppInfoByAppKey($appkey);
        if(empty($app_info)){
            return json(['code' => 400199, 'msg' => 'appkey not found']);
        }

        $login_interface_config_model = new LoginInterfaceConfig();
        $login_interface_config = $login_interface_config_model->getLoginInterfaceConfigByAppKey($appkey);
        if(empty($login_interface_config)){
            return json(['code' => 400198, 'msg' => 'login interface config not found']);
        }

        if($login_interface_config['switch'] == 0){
            return json(['code' => 400197, 'msg' => 'login interface is not enabled']);
        }

        if($login_interface_config['suport_mobile_login'] == 0){
            return json(['code' => 400196, 'msg' => 'mobile login is not open']);
        }

        $phone_number = $mcode . $mobile;

        $cache_key = str_replace('{appkey}', $appkey, self::CACHE_KEY_VERIFY_CODE);
        $cache_key = str_replace('{phone_number}', $phone_number, $cache_key);
        $code = Cache::get($cache_key);
        if(empty($code)){
            return json(['code' => 400195, 'msg' => 'verify code not found or expired']);
        }
        
        if($code != $verify_code){
            return json(['code' => 400194, 'msg' => 'verify code is incorrect']);
        }

        $user_model = new User();
        $exist_user = $user_model->getUserInfoByPhoneNumber($mcode, $mobile, $appkey);
        $uid = Arr::get($exist_user, 'uid');
        if(empty($exist_user)){
            $user_data = [
                'app_key' => $appkey,
                'mcode' => $mcode,
                'mobile' => $mobile,
                'nickname' => $this->hideMobileMiddle($mobile),
                'reg_ip' => $ip,
                'version_number' => $version_number,
                'channel' => $channel,
                'oaid' => $oaid,
                'device_id' => $device_id,
                'reg_from' => User::REG_FROM_PHONE,
            ];
            $uid = $user_model->createUser($user_data);
            if(empty($uid)){
                return json(['code' => 400250, 'msg' => 'create user failed']);
            }
        }

        $token_limit = $login_interface_config['endpoint_allow_count'];
        $token_expired_at = time() + $login_interface_config['token_effective_duration'] * 24*60*60;
        $token_params = [
            'uid' => $uid,
            'app_key' => $appkey,
            'ip' => $ip,
            'oaid' => $oaid,
            'device_id' => $device_id,
        ];
        $access_token = $this->generateAccessToken($token_params, $token_limit, $token_expired_at);
        if(empty($access_token)){
            return json(['code' => 400193, 'msg' => 'generate access token failed']);
        }

        $user_model->updateUserInfoByUid($uid, ['canceled_at' => null]);

        if($need_user_detail == 1){
            $user = $user_model->getUserInfoByUid($uid);
            $user['avatar'] = getAvatarUrl($user['avatar']);
            unset($user['app_key']);
            unset($user['tenant_id']);
            unset($user['updated_at']);
            unset($user['deleted_at']);
            unset($user['canceled_at']);
            return json(['code' => config('const.request_success'), 'msg' => 'success', 'data' => 
                array_merge($user, ['access_token' => $access_token, 'token_expired_at' => $token_expired_at])
            ]);
        }

        return json(['code' => config('const.request_success'), 'msg' => 'success', 'data' => [
            'uid' => (int) $uid,
            'access_token' => $access_token,
            'token_expired_at' => $token_expired_at,
        ]]);
    }

    public function apple(Request $request)
    {
        $validate = new AppleLogin();
        if (!$validate->check($request->all())) {
            return json($validate->getErrorInfo());
        }

        $appkey = $request->input('appkey');
        $user = $request->input('user');
        $full_name = $request->input('full_name');
        $token = $request->input('token');
        $version_number = $request->input('version_number');
        $channel = $request->input('channel');
        $need_user_detail = $request->input('need_user_detail', 0);
        $ip = $request->getRealIp();
        $oaid = $request->input('oaid');
        $device_id = $request->input('device_id');

        $app_model = new App();
        $app_info = $app_model->getAppInfoByAppKey($appkey);
        if(empty($app_info)){
            return json(['code' => 400199, 'msg' => 'appkey not found']);
        }

        $login_interface_config_model = new LoginInterfaceConfig();
        $login_interface_config = $login_interface_config_model->getLoginInterfaceConfigByAppKey($appkey);
        if(empty($login_interface_config)){
            return json(['code' => 400198, 'msg' => 'login interface config not found']);
        }

        if($login_interface_config['switch'] == 0){
            return json(['code' => 400197, 'msg' => 'login interface is not enabled']);
        }

        if($login_interface_config['suport_apple_login'] == 0){
            return json(['code' => 400196, 'msg' => 'apple login is not open']);
        }
        
        try {
            $apple_signin_info = ASDecoder::getAppleSignInPayload($token);
            $apple_user_id = $apple_signin_info->getUser();
            $is_valid = $apple_signin_info->verifyUser($user);
            if(!$is_valid){
                return json(['code' => 400195, 'msg' => 'apple user verify failed']);
            }
        } catch (\Exception $e) {
            return json(['code' => 400194, 'msg' => $e->getMessage()]);
        }

        $user_model = new User();
        $exist_user = $user_model->getUserInfoByAppleUserId($apple_user_id, $appkey);
        $uid = Arr::get($exist_user, 'uid');
        if(empty($exist_user)){
            $uid = generateUserId();
            if(empty($full_name)){
                if($login_interface_config['apple_nickname_prefix']){
                    $full_name = $login_interface_config['apple_nickname_prefix'] . random_int(100000, 999999);
                }else{
                    $full_name = $uid;
                }
            }

            $user_data = [
                'app_key' => $appkey,
                'tenant_id' => $login_interface_config['tenant_id'],
                'apple_userid' => $apple_user_id,
                'nickname' => $full_name,
                'reg_ip' => $ip,
                'version_number' => $version_number,
                'channel' => $channel,
                'oaid' => $oaid,
                'device_id' => $device_id,
                'reg_from' => User::REG_FROM_APPLE,
            ];
            $uid = $user_model->createUser($user_data, $uid);
            if(empty($uid)){
                return json(['code' => 400250, 'msg' => 'create user failed']);
            }
        }

        $token_limit = $login_interface_config['endpoint_allow_count'];
        $token_expired_at = time() + $login_interface_config['token_effective_duration'] * 24*60*60;
        $token_params = [
            'uid' => $uid,
            'app_key' => $appkey,
            'tenant_id' => $login_interface_config['tenant_id'],
            'ip' => $ip,
            'oaid' => $oaid,
            'device_id' => $device_id,
        ];
        $access_token = $this->generateAccessToken($token_params, $token_limit, $token_expired_at);
        if(empty($access_token)){
            return json(['code' => 400193, 'msg' => 'generate access token failed']);
        }

        $user_model->updateUserInfoByUid($uid, ['canceled_at' => null]);

        if($need_user_detail == 1){
            $user = $user_model->getUserInfoByUid($uid);
            $user['avatar'] = getAvatarUrl($user['avatar']);
            unset($user['app_key']);
            unset($user['tenant_id']);
            unset($user['updated_at']);
            unset($user['deleted_at']);
            unset($user['canceled_at']);
            return json(['code' => config('const.request_success'), 'msg' => 'success', 'data' => 
                array_merge($user, ['access_token' => $access_token, 'token_expired_at' => $token_expired_at])
            ]);
        }

        return json(['code' => config('const.request_success'), 'msg' => 'success', 'data' => [
            'uid' => (int) $uid,
            'access_token' => $access_token,
            'token_expired_at' => $token_expired_at,
        ]]);
    }
    
    public function wechat(Request $request)
    {
        $validate = new WechatLogin();
        if (!$validate->check($request->all())) {
            return json($validate->getErrorInfo());
        }

        $appkey = $request->input('appkey');
        $code = $request->input('code');
        $version_number = $request->input('version_number');
        $channel = $request->input('channel');
        $need_user_detail = $request->input('need_user_detail', 0);
        $ip = $request->getRealIp();
        $oaid = $request->input('oaid');
        $device_id = $request->input('device_id');

        $app_model = new App();
        $app_info = $app_model->getAppInfoByAppKey($appkey);
        if(empty($app_info)){
            return json(['code' => 400199, 'msg' => 'appkey not found']);
        }

        $login_interface_config_model = new LoginInterfaceConfig();
        $login_interface_config = $login_interface_config_model->getLoginInterfaceConfigByAppKey($appkey);
        if(empty($login_interface_config)){
            return json(['code' => 400198, 'msg' => 'login interface config not found']);
        }

        if($login_interface_config['switch'] == 0){
            return json(['code' => 400197, 'msg' => 'login interface is not enabled']);
        }

        if($login_interface_config['suport_wechat_login'] == 0){
            return json(['code' => 400196, 'msg' => 'wechat login is not open']);
        }

        $wechat_open_platform_config_model = new WechatOpenPlatformConfig();
        $wechat_open_platform_config = $wechat_open_platform_config_model->getWechatOpenPlatformConfig(
            $login_interface_config['wechat_platform_config_id'], 
            $login_interface_config['tenant_id']
        );
        
        if(empty($wechat_open_platform_config)){
            return json(['code' => 400195, 'msg' => 'wechat open platform config not found']);
        }
        
        try {
            $wechat_appkey = $wechat_open_platform_config['wechat_appid'];
            $wechat_secret = $wechat_open_platform_config['wechat_appsecret'];
            $tenantId = $wechat_open_platform_config['tenant_id'];
            $wechat_service = new WechatService($wechat_appkey, $wechat_secret);
            $access_token_info = $wechat_service->getAccessToken($code);

            $open_id = $access_token_info['openid'];
            $access_token = $access_token_info['access_token'];
            $user_info = $wechat_service->getUserInfo($access_token, $open_id);
            $open_id = $user_info['openid'];
        } catch (\Exception $e) {
            return json(['code' => 400194, 'msg' => $e->getMessage()]);
        }

        $open_id = $user_info['openid'];
        $user_model = new User();
        $exist_user = $user_model->getUserInfoByOpenId($open_id, $appkey);
        $uid = Arr::get($exist_user, 'uid');
        if(empty($exist_user)){
            $union_id = Arr::get($user_info,'unionid');
            $nickname = Arr::get($user_info,'nickname');
            $avatar = Arr::get($user_info,'headimgurl');
            $version_number = $request->input('version_number');
            $channel = $request->input('channel');
            $user_data = [
                'app_key' => $appkey,
                'tenant_id' => $tenantId,
                'wechat_openid' => $open_id,
                'wechat_unionid' => $union_id,
                'nickname' => $nickname,
                'avatar' => $avatar,
                'reg_ip' => $ip,
                'version_number' => $version_number,
                'channel' => $channel,
                'oaid' => $oaid,
                'device_id' => $device_id,
                'reg_from' => User::REG_FROM_WECHAT,
            ];
            $uid = $user_model->createUser($user_data);
            if(empty($uid)){
                return json(['code' => 400250, 'msg' => 'create user failed']);
            }
            Event::emit('user.download.avatar', ['uid' => $uid, 'avatar_url' => Arr::get($user_info,'headimgurl')]);
        }

        $token_limit = $login_interface_config['endpoint_allow_count'];
        $token_expired_at = time() + $login_interface_config['token_effective_duration'] * 24*60*60;
        $token_params = [
            'uid' => $uid,
            'app_key' => $appkey,
            'tenant_id' => $tenantId,
            'ip' => $ip,
            'oaid' => $oaid,
            'device_id' => $device_id,
        ];
        $access_token = $this->generateAccessToken($token_params, $token_limit, $token_expired_at);
        if(empty($access_token)){
            return json(['code' => 400193, 'msg' => 'generate access token failed']);
        }

        $user_model->updateUserInfoByUid($uid, ['canceled_at' => null]);

        if($need_user_detail == 1){
            $user = $user_model->getUserInfoByUid($uid);
            $user['avatar'] = getAvatarUrl($user['avatar']);
            unset($user['app_key']);
            unset($user['tenant_id']);
            unset($user['updated_at']);
            unset($user['deleted_at']);
            unset($user['canceled_at']);
            return json(['code' => config('const.request_success'), 'msg' => 'success', 'data' => 
                array_merge($user, ['access_token' => $access_token, 'token_expired_at' => $token_expired_at])
            ]);
        }

        return json(['code' => config('const.request_success'), 'msg' => 'success', 'data' => [
            'uid' => (int) $uid,
            'access_token' => $access_token,
            'token_expired_at' => $token_expired_at,
        ]]);
    }

    /**
     * 华为登录
     *
     * @param Request $request
     * @return void
     */
    public function huawei(Request $request)
    {
        $validate = new HuaweiLogin();
        if (!$validate->check($request->all())) {
            return json($validate->getErrorInfo());
        }

        $appkey = $request->input('appkey');
        $code = $request->input('code');
        $version_number = $request->input('version_number');
        $channel = $request->input('channel');
        $need_user_detail = $request->input('need_user_detail', 0);
        $ip = $request->getRealIp();
        $oaid = $request->input('oaid');
        $device_id = $request->input('device_id');

        $app_model = new App();
        $app_info = $app_model->getAppInfoByAppKey($appkey);
        if(empty($app_info)){
            return json(['code' => 400199, 'msg' => 'appkey not found']);
        }

        $login_interface_config_model = new LoginInterfaceConfig();
        $login_interface_config = $login_interface_config_model->getLoginInterfaceConfigByAppKey($appkey);
        if(empty($login_interface_config)){
            return json(['code' => 400198, 'msg' => 'login interface config not found']);
        }

        if($login_interface_config['switch'] == 0){
            return json(['code' => 400197, 'msg' => 'login interface is not enabled']);
        }

        if($login_interface_config['suport_huawei_login'] == 0){
            return json(['code' => 400196, 'msg' => 'huawei login is not open']);
        }

        // 这里需要根据实际配置表结构获取华为配置
        // 假设在login_interface_config表中有huawei_client_id和huawei_client_secret字段
        if(empty($login_interface_config['huawei_oauth_client_id']) || empty($login_interface_config['huawei_oauth_client_secret'])){
            return json(['code' => 400195, 'msg' => 'huawei config not found']);
        }
        
        try {
            $huawei_client_id = $login_interface_config['huawei_oauth_client_id'];
            $huawei_client_secret = $login_interface_config['huawei_oauth_client_secret'];
            $tenantId = $login_interface_config['tenant_id'];
            $huawei_service = new HuaweiService($huawei_client_id, $huawei_client_secret);
            $access_token_info = $huawei_service->getAccessToken($code);

            $access_token = $access_token_info['access_token'];
            $user_info = $huawei_service->getUserInfo($access_token);
            
            // 华为返回的用户信息字段可能包括：openid, unionid, displayName, headPictureURL等
            $open_id = $user_info['openID'] ?? '';
            $union_id = $user_info['unionID'] ?? '';
        } catch (\Exception $e) {
            return json(['code' => 400194, 'msg' => $e->getMessage()]);
        }

        $user_model = new User();
        $exist_user = $user_model->getUserInfoByHuaweiOpenId($open_id, $appkey);
        $uid = Arr::get($exist_user, 'uid');
        if(empty($exist_user)){
            $nickname = $user_info['displayName'] ?? '';
            $avatar = $user_info['headPictureURL'] ?? '';
            
            $user_data = [
                'app_key' => $appkey,
                'tenant_id' => $tenantId,
                'huawei_openid' => $open_id,
                'huawei_unionid' => $union_id,
                'nickname' => $nickname,
                'avatar' => $avatar,
                'reg_ip' => $ip,
                'version_number' => $version_number,
                'channel' => $channel,
                'oaid' => $oaid,
                'device_id' => $device_id,
                'reg_from' => User::REG_FROM_HUAWEI,
            ];
            $uid = $user_model->createUser($user_data);
            if(empty($uid)){
                return json(['code' => 400250, 'msg' => 'create user failed']);
            }
            
            // 如果有头像URL，触发下载头像事件
            if(!empty($avatar)){
                Event::emit('user.download.avatar', ['uid' => $uid, 'avatar_url' => $avatar]);
            }
        }

        $token_limit = $login_interface_config['endpoint_allow_count'];
        $token_expired_at = time() + $login_interface_config['token_effective_duration'] * 24*60*60;
        $token_params = [
            'uid' => $uid,
            'app_key' => $appkey,
            'tenant_id' => $tenantId,
            'ip' => $ip,
            'oaid' => $oaid,
            'device_id' => $device_id,
        ];
        $access_token = $this->generateAccessToken($token_params, $token_limit, $token_expired_at);
        if(empty($access_token)){
            return json(['code' => 400193, 'msg' => 'generate access token failed']);
        }

        $user_model->updateUserInfoByUid($uid, ['canceled_at' => null]);

        if($need_user_detail == 1){
            $user = $user_model->getUserInfoByUid($uid);
            $user['avatar'] = getAvatarUrl($user['avatar']);
            unset($user['app_key']);
            unset($user['tenant_id']);
            unset($user['updated_at']);
            unset($user['deleted_at']);
            unset($user['canceled_at']);
            return json(['code' => config('const.request_success'), 'msg' => 'success', 'data' => 
                array_merge($user, ['access_token' => $access_token, 'token_expired_at' => $token_expired_at])
            ]);
        }

        return json(['code' => config('const.request_success'), 'msg' => 'success', 'data' => [
            'uid' => (int) $uid,
            'access_token' => $access_token,
            'token_expired_at' => $token_expired_at,
        ]]);
    }

    /**
     * 隐藏手机号中间部分数字
     */
    private function hideMobileMiddle($mobile)
    {
        $length = strlen($mobile);
        
        // 如果手机号长度小于7位，直接返回原号码
        if ($length < 7) {
            return $mobile;
        }
        
        // 显示前3位和后4位，中间用****代替
        if ($length >= 11) {
            return substr($mobile, 0, 3) . '****' . substr($mobile, -4);
        }
        
        // 对于7-10位的号码，显示前3位和后2位
        return substr($mobile, 0, 3) . '****' . substr($mobile, -2);
    }

    protected function generateAccessToken($params, $limit, $expired_at)
    {
        try {
            $user_token_model = new UserToken();
            $token = ZHYToken::generateToken($params['uid'], $params['app_key']);
            $exist_token_list = $user_token_model->getTokenListByUid($params['uid']);
            $token_cnt = 0;
            if(!empty($exist_token_list)){
                $token_cnt = count($exist_token_list);
                //找出最早的token
                while($token_cnt >= $limit){
                    $earliest_token = $exist_token_list[0];
                    $user_token_model->deleteUserTokenById($earliest_token['id'], $earliest_token['token']);
                    $token_cnt--;
                }
            }
            $rs = $user_token_model->addUserToken([
                'uid' => $params['uid'],
                'token' => $token,
                'app_key' => $params['app_key'],
                'oaid' => $params['oaid'],
                'device_id' => $params['device_id'],
                'ip' => $params['ip'],
                'expired_at' => date('Y-m-d H:i:s', $expired_at),
            ]);

            if($rs){
                return $token;
            }
        } catch (\Exception $e) {
            Log::error('generateAccessToken error: ' . $e->getMessage());
            return null;
        }
    }
}