<?php
namespace App\Admin\Controllers\Manager;

use App\Admin\AppKey;
use Illuminate\Http\Request;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Layout\Content;
use App\Models\AliyunAccessConfig;
use App\Http\Controllers\Controller;
use App\Models\LoginInterfaceConfig;
use Illuminate\Support\Facades\Http;
use AlibabaCloud\Client\AlibabaCloud;
use Illuminate\Support\Facades\Cache;
use App\Admin\Forms\SmsLoginConfig;
use App\Admin\Forms\AppleLoginConfig;
use Illuminate\Support\Facades\Validator;
use App\Admin\Forms\AccessTokenConfig;
use App\Admin\Forms\HuaweiLoginConfig;
use App\Admin\Forms\WechatLoginConfig;

class UserConfigController extends Controller
{
    use AppKey;

    public function index(Content $content)
    {
        $content->title('接口配置');
        $content->description('用户模块');

        $content->body(Tab::forms([
            'basic'    => AccessTokenConfig::class,
            'wechat'   => WechatLoginConfig::class,
            'sms'      => SmsLoginConfig::class,
            'apple'    => AppleLoginConfig::class,
            'huawei'   => HuaweiLoginConfig::class,
        ]));

        return $content;
    }

    public function saveBase(Request $request)
    {
        $switch = $request->input('switch');
        $app_key = $this->getAppKey();

        if ($switch == 1) {
            $validator = Validator::make($request->all(), [
                'token_effective_duration' => 'required|integer|min:1|max:3650',
                'endpoint_allow_count' => 'required|integer|min:1|max:5',
                'cancel_after_days' => 'required|integer|min:1|max:180',
            ]);
           
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $login_config_data = [
                'switch' => $switch,
                'token_effective_duration' => $request->input('token_effective_duration'),
                'endpoint_allow_count' => $request->input('endpoint_allow_count'),
                'cancel_after_days' => $request->input('cancel_after_days'),
            ];

            try {
                app(LoginInterfaceConfig::class)->saveConfig($app_key, $login_config_data);
                $this->clearAPICache($app_key);
                admin_toastr('保存成功', 'success');
                return back();
            } catch (\Exception $e) {
                admin_toastr($e->getMessage(), 'error');
                return back()->withErrors($e->getMessage())->withInput();
            }
        } else {
            app(LoginInterfaceConfig::class)->saveConfig($app_key, ['switch' => $switch]);
            $this->clearAPICache($app_key);
            admin_toastr('保存成功', 'success');
            return back();
        }
    }

    public function saveWechat(Request $request)
    {
        $app_key = $this->getAppKey();

        $validator = Validator::make($request->all(), [
            'suport_wechat_login' => 'required|in:0,1',
            'wechat_platform_config_id' => 'required_if:suport_wechat_login,1|exists:wechat_open_platform_config,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $login_config_data = [
            'suport_wechat_login' => $request->input('suport_wechat_login'),
            'wechat_platform_config_id' => $request->input('wechat_platform_config_id'),
        ];

        try {
            app(LoginInterfaceConfig::class)->saveConfig($app_key, $login_config_data);
            $this->clearAPICache($app_key);
            admin_toastr('保存成功', 'success');
            return back();
        } catch (\Exception $e) {
            admin_toastr($e->getMessage(), 'error');
            return back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function saveApple(Request $request)
    {
        $app_key = $this->getAppKey();

        $validator = Validator::make($request->all(), [
            'suport_apple_login' => 'required|in:0,1',
            'apple_nickname_prefix' => 'nullable|string|max:8',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $login_config_data = [
            'suport_apple_login' => $request->input('suport_apple_login'),
            'apple_nickname_prefix' => $request->input('apple_nickname_prefix') ?? '',
        ];

        try {
            app(LoginInterfaceConfig::class)->saveConfig($app_key, $login_config_data);
            $this->clearAPICache($app_key);
            admin_toastr('保存成功', 'success');
            return back();
        } catch (\Exception $e) {
            admin_toastr($e->getMessage(), 'error');
            return back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function saveHuawei(Request $request)
    {
        $app_key = $this->getAppKey();

        $validator = Validator::make($request->all(), [
            'suport_huawei_login' => 'required|in:0,1',
            'huawei_oauth_client_id' => 'required_if:suport_huawei_login,1|string|max:32',
            'huawei_oauth_client_secret' => 'required_if:suport_huawei_login,1|string|max:128',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $interface_check = $request->input('interface_check');
        if($interface_check == 0 && $request->input('suport_huawei_login') == 1){
            admin_error('请先验证配置是否正确');
            return back()->withInput();
        }

        if($request->input('suport_huawei_login') == 0){
            $login_config_data = [
                'suport_huawei_login' => $request->input('suport_huawei_login'),
            ];
        }else{
            $login_config_data = [
                'suport_huawei_login' => $request->input('suport_huawei_login'),
                'huawei_oauth_client_id' => $request->input('huawei_oauth_client_id'),
                'huawei_oauth_client_secret' => $request->input('huawei_oauth_client_secret'),
            ];
        }

        try {
            app(LoginInterfaceConfig::class)->saveConfig($app_key, $login_config_data);
            $this->clearAPICache($app_key);
            admin_toastr('保存成功', 'success');
            return back();
        } catch (\Exception $e) {
            admin_toastr($e->getMessage(), 'error');
            return back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function saveSms(Request $request)
    {
        $app_key = $this->getAppKey();

        $validator = Validator::make($request->all(), [
            'suport_mobile_login' => 'required|in:0,1',
            'aliyun_access_config_id' => 'required_if:suport_mobile_login,1|exists:aliyun_access_config,id',
            'aliyun_sms_sign_name' => 'required_if:suport_mobile_login,1|string|max:255',
            'aliyun_sms_tmp_code' => 'required_if:suport_mobile_login,1|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $interface_check = $request->input('aliyun_sms_check');
        if($interface_check == 0 && $request->input('suport_mobile_login') == 1){
            admin_error('请先验证配置是否正确');
            return back()->withInput();
        }

        if($request->input('suport_mobile_login') == 0){
            $login_config_data = [
                'suport_mobile_login' => $request->input('suport_mobile_login'),
            ];
        }else{
            $login_config_data = [
                'suport_mobile_login' => $request->input('suport_mobile_login'),
                'aliyun_access_config_id' => $request->input('aliyun_access_config_id'),
                'aliyun_sms_sign_name' => $request->input('aliyun_sms_sign_name'),
                'aliyun_sms_tmp_code' => $request->input('aliyun_sms_tmp_code'),
            ];
        }

        try {
            app(LoginInterfaceConfig::class)->saveConfig($app_key, $login_config_data);
            $this->clearAPICache($app_key);
            admin_toastr('保存成功', 'success');
            return back();
        } catch (\Exception $e) {
            admin_toastr($e->getMessage(), 'error');
            return back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function checkSmsInterface(Request $request)
    {
        $mobile = $request->input('mobile');
        $params = $request->input('params');
        $aliyun_access_config_id = $request->input('aliyun_access_config_id');
        $aliyun_sms_sign_name = $request->input('aliyun_sms_sign_name');
        $aliyun_sms_tmp_code = $request->input('aliyun_sms_tmp_code');
        
        if(empty($aliyun_access_config_id)){
            return response()->json(['status' => false, 'message' => '阿里云AccessKey值不能为空']);
        }

        $aliyun_access_config = AliyunAccessConfig::find($aliyun_access_config_id);
        if(empty($aliyun_access_config)){
            return response()->json(['status' => false, 'message' => '阿里云AccessKey值不存在']);
        }

        if(empty($aliyun_sms_sign_name)){
            return response()->json(['status' => false, 'message' => '短信签名不能为空']);
        }

        if(empty($aliyun_sms_tmp_code)){
            return response()->json(['status' => false, 'message' => '短信模板Code不能为空']);
        }

        if(!preg_match('/^1[3-9]\d{9}$/', $mobile)){
            return response()->json(['status' => false, 'message' => '手机号格式不正确']);
        }

        $params_obj = json_decode($params, true);
        if(empty($params_obj['code'])){
            return response()->json(['status' => false, 'message' => '参数格式不正确']);
        }
        

        AlibabaCloud::accessKeyClient($aliyun_access_config->access_key, $aliyun_access_config->access_key_secret)
                ->regionId('cn-hangzhou')
                ->asDefaultClient();
        
        $result = AlibabaCloud::rpc()
            ->product('Dysmsapi')
            ->version('2017-05-25')
            ->action('SendSms')
            ->method('POST')
            ->options([
                'query' => [
                    'PhoneNumbers' => $mobile,
                    'SignName' => $aliyun_sms_sign_name,
                    'TemplateCode' => $aliyun_sms_tmp_code,
                    'TemplateParam' => $params,
                ],
            ])->request();

        if($result['Code'] == 'OK'){
            return response()->json(['status' => true, 'message' => '短信发送成功']);
        }else{
            return response()->json(['status' => false, 'message' => '短信发送失败']);
        }
            
    }

    protected function clearAPICache($app_key)
    {
        $cache_key = 'login_interface_config|'.$app_key;
        Cache::store('api_cache')->forget($cache_key);
    }

    public function checkHWInterface(Request $request)
    {
        $client_id = $request->input('huawei_oauth_client_id');
        $client_secret = $request->input('huawei_oauth_client_secret');
        if (empty($client_id) || empty($client_secret)) {
            return response()->json(['status' => false, 'message' => 'Client ID和Client Secret不能为空']);
        }
        
        // 华为AGC获取access_token的接口
        $url = 'https://oauth-login.cloud.huawei.com/oauth2/v3/token';
        $response = Http::asForm()->post($url, [
            'grant_type' => 'client_credentials',
            'client_id' => $client_id,
            'client_secret' => $client_secret,
        ]);
        
        $data = $response->json();

        // dump($data);
        
        // 根据华为AGC官方文档，成功时返回access_token、expires_in等字段
        if (isset($data['access_token']) && !empty($data['access_token'])) {
            return response()->json(['status' => true]);
        }
        
        // 处理华为AGC的错误响应
        if (isset($data['error'])) {
            $error_msg = isset($data['error_description']) ? $data['error_description'] : $data['error'];
            return response()->json(['status' => false, 'message' => '配置错误: ['.$data['sub_error'].']' . $error_msg]);
        }
        
        // 处理其他可能的错误格式
        if (isset($data['ret']) && $data['ret']['code'] != 0) {
            $error_msg = isset($data['ret']['msg']) ? $data['ret']['msg'] : '未知错误';
            return response()->json(['status' => false, 'message' => '配置错误: ['.$data['ret']['code'].']' . $error_msg]);
        }
        
        // 其他未知错误
        return response()->json([
            'status' => false, 
            'message' => '配置验证失败，返回数据格式异常'
        ]);
    }
}