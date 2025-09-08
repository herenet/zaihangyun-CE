<?php

namespace app\controller\api;

use support\Log;
use support\Cache;
use app\model\User;
use support\Request;
use app\model\UserToken;
use app\validate\UpdateUser;
use app\validate\ModifyMobile;
use app\model\AliyunAccessConfig;
use app\service\AliyunSmsService;
use app\validate\LoginVerifyCode;
use app\model\LoginInterfaceConfig;
use Intervention\Image\ImageManager;
use WebmanTech\LaravelFilesystem\Facades\Storage;

class UserController
{

    const AVATAR_SCALE_SIZE = 300;

    const CACHE_KEY_VERIFY_CODE = 'modify_verify_code|{appkey}|{phone_number}';

    public function info(Request $request)
    {
        $token_info = $request->token_info;
        $user_model = new User();
        $user = $user_model->getUserInfoByUid($token_info['uid']);
        if(empty($user)){
            return json(['code' => 400199, 'msg' => 'user not found']);
        }
        $user['avatar'] = getAvatarUrl($user['avatar']);
        
        // 在vip_expired_at之后插入is_vip字段
        $vip_status = $this->isVip($user['vip_expired_at']);
        $user = $this->insertAfterKey($user, 'vip_expired_at', 'is_vip', $vip_status);
        
        return json(['code' => config('const.request_success'), 'msg' => 'success', 'data' => $user]);
    }

    private function isVip($vip_expire_time)
    {
        if(empty($vip_expire_time)){
            return 0;
        }
        if(strtotime($vip_expire_time) < time()){
            return 0;
        }
        return 1;
    }

    /**
     * 在指定键之后插入新的键值对
     */
    private function insertAfterKey($array, $afterKey, $newKey, $newValue)
    {
        $keys = array_keys($array);
        $values = array_values($array);
        
        $afterIndex = array_search($afterKey, $keys);
        if ($afterIndex === false) {
            // 如果找不到指定的键，就直接添加到末尾
            $array[$newKey] = $newValue;
            return $array;
        }
        
        $newKeys = array_merge(
            array_slice($keys, 0, $afterIndex + 1),
            [$newKey],
            array_slice($keys, $afterIndex + 1)
        );
        
        $newValues = array_merge(
            array_slice($values, 0, $afterIndex + 1),
            [$newValue],
            array_slice($values, $afterIndex + 1)
        );
        
        return array_combine($newKeys, $newValues);
    }

    public function cancel(Request $request)
    {
        $token_info = $request->token_info;
        $user_model = new User();
        $rs = $user_model->cancelUserByUid($token_info['uid']);
        $token_model = new UserToken();
        $token_model->deleteUserTokenById($token_info['id'], $token_info['token']);
        if($rs === false){
            return json(['code' => 400250, 'msg' => 'cancel user failed']);
        }
        return json(['code' => config('const.request_success'), 'msg' => 'success']);
    }   

    public function update(Request $request)
    {
        $token_info = $request->token_info;
        $user_model = new User();
        $user = $user_model->getUserInfoByUid($token_info['uid']);
        
        $validate = new UpdateUser();
        if (!$validate->check($request->all())) {
            return json($validate->getErrorInfo());
        }

        if(empty($user)){
            return json(['code' => 400199, 'msg' => 'user not found']);
        }

        $update_data = $request->only([
            'nickname', 
            'gender', 
            'birthday', 
            'oaid', 
            'device_id', 
            'password', 
            'email', 
            'country', 
            'province', 
            'city', 
            'enter_pass', 
            'ext_data'
        ]);

        $update_data['mcode'] = empty($update_data['mcode']) ? '+86' : $update_data['mcode'];
        $update_data['birthday'] = empty($update_data['birthday']) ? null : $update_data['birthday'];
        $update_data['password'] = empty($update_data['password']) ? null : md5($update_data['password']);

        try{
            $user_model->updateUserInfoByUid($token_info['uid'], $update_data);
            return json(['code' => config('const.request_success'), 'msg' => 'success']);
        }catch(\Exception $e){
            return json(['code' => 400250, 'msg' => 'update user info failed']);
        }
    }

    public function avatar(Request $request)
    {
        $token_info = $request->token_info;

        $avatar = $request->file('avatar');
        if(empty($avatar)){
            return json(['code' => 400101, 'msg' => 'avatar is required']);
        }

        $file_size = $avatar->getSize();
        if($file_size > 1024 * 1024 * 2){
            return json(['code' => 400102, 'msg' => 'avatar size must be less than 2MB']);
        }

        $file_ext = $avatar->getUploadExtension();
        if($file_ext != 'jpg' && $file_ext != 'png' && $file_ext != 'jpeg'){
            return json(['code' => 400103, 'msg' => 'avatar must be a jpg, png or jpeg file']);
        }

        $user_model = new User();
        $user = $user_model->getUserInfoByUid($token_info['uid']);
        if(empty($user)){
            return json(['code' => 400199, 'msg' => 'user not found']);
        }

        try{
            // 生成用户文件路径
            $uid_hash = md5($token_info['uid']);
            $base_path = $token_info['app_key'].'/'.$token_info['uid'];
            $file_ext = $avatar->getUploadExtension();
            $file_name = $uid_hash . '.' . $file_ext;
            $file_path = $base_path . '/' . $file_name;

            // 使用 Storage 保存文件
            $avatar_storage = Storage::disk('local_avatar');
            $avatar_storage->deleteDirectory($base_path);  // 删除旧目录
            $avatar_storage->makeDirectory($base_path);    // 创建新目录
            $avatar_storage->move($avatar->getRealPath(), $file_path);  // 保存文件
            $avatar_path = $file_path;

            // 使用 intervention/image 2.7 版本 API
            $manager = new ImageManager(['driver' => 'gd']);
            $image = $manager->make($avatar->getRealPath());
            $image->resize(self::AVATAR_SCALE_SIZE, self::AVATAR_SCALE_SIZE, function ($constraint) {
                $constraint->aspectRatio();
            });
            
            // 保存调整大小后的图片
            $image_data = (string)$image->encode();
            $avatar_storage->put($file_path, $image_data);

            // 更新用户头像路径
            $user_model->updateUserInfoByUid($token_info['uid'], ['avatar' => $avatar_path]);
            return json(['code' => config('const.request_success'), 'msg' => 'success', 'data' => ['avatar' => getAvatarUrl($avatar_path)]]);
        }catch(\Exception $e){
            Log::error('update user avatar failed', ['error' => $e->getMessage()]);
            return json(['code' => 400250, 'msg' => 'update user info failed']);
        }
    }

    public function verifyCode(Request $request)
    {
        $validate = new LoginVerifyCode();
        if (!$validate->check($request->all())) {
            return json($validate->getErrorInfo());
        }

        $token_info = $request->token_info;
        $appkey = $token_info['app_key'];
        $login_interface_config_model = new LoginInterfaceConfig();
        $login_interface_config = $login_interface_config_model->getLoginInterfaceConfigByAppKey($appkey);
        if(empty($login_interface_config)){
            return json(['code' => 400198, 'msg' => 'login interface config not found']);
        }

        if(empty($login_interface_config['aliyun_access_config_id'])){
            return json(['code' => 400197, 'msg' => 'aliyun access config not found']);
        }

        if(empty($login_interface_config['aliyun_sms_sign_name'])){
            return json(['code' => 400196, 'msg' => 'aliyun sms sign name not found']);
        }

        if(empty($login_interface_config['aliyun_sms_tmp_code'])){
            return json(['code' => 400195, 'msg' => 'aliyun sms template code not found']);
        }

        $aliyun_access_config_model = new AliyunAccessConfig();
        $aliyun_access_config = $aliyun_access_config_model->getAliyunAccessConfigById($login_interface_config['aliyun_access_config_id']);
        if(empty($aliyun_access_config)){
            return json(['code' => 400194, 'msg' => 'aliyun access_key config not found']);
        }

        $mcode = $request->input('mcode');
        $mobile = $request->input('mobile');
        $cache_ttl = $login_interface_config['aliyun_sms_verify_code_expire'] ?? 5;
        $phone_number = $mcode.$mobile;
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
            return json(['code' => 400193, 'msg' => $rs]);
        } catch (\Exception $e) {
            return json(['code' => 400192, 'msg' => $e->getMessage()]);
        }
    }

    public function mobile(Request $request)
    {
        $validate = new ModifyMobile();
        if (!$validate->check($request->all())) {
            return json($validate->getErrorInfo());
        }

        $token_info = $request->token_info;
        $appkey = $token_info['app_key'];
        $mcode = $request->input('mcode');
        $mobile = $request->input('mobile');
        $phone_number = $mcode.$mobile;
        $verify_code = $request->input('verify_code');

        $cache_key = str_replace('{appkey}', $appkey, self::CACHE_KEY_VERIFY_CODE);
        $cache_key = str_replace('{phone_number}', $phone_number, $cache_key);
        $cache_code = Cache::get($cache_key);
        if(empty($cache_code)){
            return json(['code' => 400191, 'msg' => 'verify code expired']);
        }

        if($cache_code != $verify_code){
            return json(['code' => 400190, 'msg' => 'verify code error']);
        }
        
        $user_model = new User();
        $uid = $request->token_info['uid'];
        $user_model->updateUserInfoByUid($uid, ['mcode' => $mcode, 'mobile' => $mobile]);
        return json(['code' => config('const.request_success'), 'msg' => 'success']);
    }

    public function logout(Request $request)
    {
        $token_info = $request->token_info;
        $token_model = new UserToken();
        $token_model->deleteUserTokenById($token_info['id'], $token_info['token']);
        return json(['code' => config('const.request_success'), 'msg' => 'success']);
    }
}
