<?php
namespace App\SaaSAdmin\Controllers\Manager;

use App\Models\App;
use App\Libs\Helpers;
use Firebase\JWT\JWT;
use App\SaaSAdmin\AppKey;
use Illuminate\Http\Request;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Layout\Content;
use Alipay\EasySDK\Kernel\Config;
use App\Models\AppleDevS2SConfig;
use App\SaaSAdmin\Forms\IAPConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\OrderInterfaceConfig;
use App\SaaSAdmin\Facades\SaaSAdmin;
use Illuminate\Support\Facades\Http;
use App\SaaSAdmin\Forms\AlipayConfig;
use Illuminate\Support\Facades\Cache;
use App\SaaSAdmin\Forms\OrderBaseConfig;
use App\SaaSAdmin\Forms\WechatPayConfig;
use Illuminate\Support\Facades\Validator;
use Readdle\AppStoreServerAPI\Environment;
use Readdle\AppStoreServerAPI\AppStoreServerAPI;
use App\Models\AlipayConfig as AlipayConfigModel;
use Readdle\AppStoreServerAPI\Exception\AppStoreServerAPIException;

class OrderConfigController extends Controller
{
    use AppKey;

    public function index(Content $content)
    {
        $app_key = $this->getAppKey();
        $app_info = app(App::class)->getAppInfo($app_key);
        $content->title('接口配置');
        $content->description('订单模块');

        if($app_info['platform_type'] == App::PLATFORM_TYPE_IOS) {
            $content->body(Tab::forms([
                'base' => OrderBaseConfig::class,
                'iap' => IAPConfig::class,
            ]));
        }else{
            $content->body(Tab::forms([
                'base' => OrderBaseConfig::class,
                'wechat_pay' => WechatPayConfig::class,
                'alipay' => AlipayConfig::class,
            ]));
        }

        return $content;
    }

    public function saveBase(Request $request)
    {
        $switch = $request->input('switch');
        $tenant_id = SaaSAdmin::user()->id;
        $app_key = $this->getAppKey();

        $validator = Validator::make($request->all(), [
            'switch' => 'required|in:0,1',
            'oid_prefix' => 'required_if:switch,1|string|max:4',
        ], [
            'switch.required' => '是否启用接口不能为空',
            'switch.in' => '是否启用接口必须为0或1',
            'oid_prefix.required_if' => '必须启用接口后才能设置订单号前缀',
            'oid_prefix.string' => '订单号前缀必须为字符串',
            'oid_prefix.max' => '订单号前缀最大长度为4个字符',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if ($switch == 1) {
            $order_config_data = [
                'switch' => $switch,
                'oid_prefix' => $request->input('oid_prefix'),
            ];

            try {
                app(OrderInterfaceConfig::class)->saveConfig($tenant_id, $app_key, $order_config_data);
                $this->clearAPICache($app_key);
                admin_toastr('保存成功', 'success');
                return back();
            } catch (\Exception $e) {
                admin_toastr($e->getMessage(), 'error');
                return back()->withErrors($e->getMessage())->withInput();
            }
        } else {
            app(OrderInterfaceConfig::class)->saveConfig($tenant_id, $app_key, ['switch' => $switch]);
            $this->clearAPICache($app_key);
            admin_toastr('保存成功', 'success');
            return back();
        }
    }

    public function saveWechat(Request $request)
    {
        $tenant_id = SaaSAdmin::user()->id;
        $app_key = $this->getAppKey();

        $validator = Validator::make($request->all(), [
            'suport_wechat_pay' => 'required|in:0,1',
            'wechat_platform_config_id' => 'required_if:suport_wechat_pay,1|exists:wechat_open_platform_config,id',
            'wechat_payment_config_id' => 'required_if:suport_wechat_pay,1|exists:wechat_payment_config,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $order_config_data = [
            'suport_wechat_pay' => $request->input('suport_wechat_pay'),
            'wechat_platform_config_id' => $request->input('wechat_platform_config_id'),
            'wechat_payment_config_id' => $request->input('wechat_payment_config_id'),
        ];

        try {
            app(OrderInterfaceConfig::class)->saveConfig($tenant_id, $app_key, $order_config_data);
            $this->clearAPICache($app_key);
            admin_toastr('保存成功', 'success');
            return back();
        } catch (\Exception $e) {
            admin_toastr($e->getMessage(), 'error');
            return back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function saveAlipay(Request $request)
    {
        $tenant_id = SaaSAdmin::user()->id;
        $app_key = $this->getAppKey();

        $validator = Validator::make($request->all(), [ 
            'suport_alipay' => 'required|in:0,1',
            'alipay_app_id' => 'required_if:suport_alipay,1|string|max:32',
            'app_private_cert' => 'required_if:suport_alipay,1|string|max:2048',
            'alipay_public_cert' => 'required_if:suport_alipay,1|string|max:1024',
        ],[
            'alipay_app_id.required' => '支付宝应用ID不能为空',
            'app_private_cert.required' => '支付宝应用私钥不能为空',
            'alipay_public_cert.required' => '支付宝公钥不能为空',
        ]); 

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $order_config_data = [
            'suport_alipay' => $request->input('suport_alipay'),
        ];

        try {
            DB::beginTransaction();
            if ($request->input('suport_alipay') == 1) {
                if($request->input('alipay_interface_check') == 0) {
                    DB::rollBack();
                    admin_error('请先验证支付宝配置是否正确');
                    return back()->withInput();
                }

                $alipay_config_data = [
                    'alipay_app_id' => $request->input('alipay_app_id'),
                    'app_private_cert' => $request->input('app_private_cert'),
                    'alipay_public_cert' => $request->input('alipay_public_cert'),
                ];
                app(AlipayConfigModel::class)->saveConfig($tenant_id, $app_key, $alipay_config_data);
            }

            app(OrderInterfaceConfig::class)->saveConfig($tenant_id, $app_key, $order_config_data);
            
            $this->clearAPICache($app_key);
            DB::commit();
            admin_toastr('保存成功', 'success');
            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            admin_toastr($e->getMessage(), 'error');
            return back()->withErrors($e->getMessage())->withInput();
        }
    }

    protected function clearAPICache($app_key)
    {
        $cache_key = 'order_interface_config|'.$app_key;
        $alipay_cache_key = 'alipay_config|'.$app_key;
        Cache::store('api_cache')->forget($cache_key);
        Cache::store('api_cache')->forget($alipay_cache_key);
    }

    public function checkAlipayInterface(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'alipay_app_id' => 'required|string|max:32',
            'app_private_cert' => 'required|string|max:2048',
            'alipay_public_cert' => 'required|string|max:1024',
        ],[
            'alipay_app_id.required' => '支付宝应用ID不能为空',
            'app_private_cert.required' => '支付宝应用私钥不能为空',
            'alipay_public_cert.required' => '支付宝公钥不能为空',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

        try {
            // 创建支付宝配置
            $options = new Config();

            $options->protocol = "https";
            $options->gatewayHost = "openapi.alipay.com";
            $options->signType = "RSA2";
            $options->appId = $request->input('alipay_app_id');
            $options->merchantPrivateKey = $request->input('app_private_cert');
            $options->alipayPublicKey = $request->input('alipay_public_cert');

            // 创建支付宝客户端实例
            $alipay = \Alipay\EasySDK\Kernel\Factory::setOptions($options);
            
            // 发送一个简单的查询请求来测试配置
            $result = $alipay->payment()->common()->query('TEST_TRADE_NO_'.time());

            // 如果没有抛出异常，说明配置基本正确，但可能需要进一步检查其他错误
            if (isset($result->code) && $result->code != '10000') {
                // 有错误但不是配置错误（如查询的订单不存在）
                if ($result->code == '40004') {
                    // 订单号不存在是正常的，说明配置正确
                    
                    return response()->json([
                        'status' => true, 
                        'message' => '支付宝配置验证成功',
                    ]);
                }
                
                return response()->json([
                    'status' => false, 
                    'message' => '支付宝返回错误：'.$result->msg.' ('.$result->code.')'
                ]);
            }
        } catch (\Exception $e) {
            // 捕获到异常，说明配置有误
            // dump($e->getTraceAsString());
            if ($e->getMessage() == '验签失败，请检查支付宝公钥设置是否正确。') {
                return response()->json([
                    'status' => false, 
                    'message' => '支付宝配置验证失败: 验签失败，请检查支付宝公钥设置或者支付宝应用ID是否正确。',
                ]);
            }
            return response()->json([
                'status' => false, 
                'message' => '支付宝配置验证失败: ' . $e->getMessage(),
            ]);
        }
    }

    public function verifyOneTimePurchase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bundle_id' => 'required|string|max:128',
            'app_apple_id' => 'required|integer',
            'receipt' => 'required|string',
        ],[
            'bundle_id.required' => '应用包名不能为空',
            'bundle_id.string' => '应用包名必须为字符串',
            'bundle_id.max' => '应用包名最大长度为128个字符',
            'app_apple_id.required' => '苹果应用ID不能为空',
            'app_apple_id.integer' => '苹果应用ID必须为整数',
            'receipt.required' => '支付凭证不能为空',
            'receipt.string' => '支付凭证必须为字符串',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

        $receipt = $request->input('receipt');
        $bundle_id = $request->input('bundle_id');
        $app_apple_id = $request->input('app_apple_id');

        try {
            $response = $this->callAppleVerifyApi($receipt);
            if ($response['status'] === 0 && isset($response['receipt'])) {
                // 验证成功
                if($response['receipt']['bundle_id'] == $bundle_id) {
                    return response()->json([
                        'status' => true,
                        'data' => '验证成功',
                    ]);
                }else{
                    return response()->json([
                        'status' => false,
                        'message' => '验证失败: 应用包名不匹配',
                    ]);
                }
            }
    
            return response()->json([
                'status' => false,
                'message' => Helpers::getAppleReceiptStatusMessage($response['status']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => '验证失败:'.$e->getMessage(),
            ]);
        }
    }

    public function verifyNotify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bundle_id' => 'required|string|max:128',
            'app_apple_id' => 'required|integer',
            'shared_secret' => 'required|string',
            'apple_dev_s2s_config_id' => 'required|integer',
        ],[
            'bundle_id.required' => '应用包名不能为空',
            'bundle_id.string' => '应用包名必须为字符串',
            'bundle_id.max' => '应用包名最大长度为128个字符',
            'app_apple_id.required' => '苹果应用ID不能为空',
            'app_apple_id.integer' => '苹果应用ID必须为整数',
            'shared_secret.required' => '共享密钥不能为空',
            'shared_secret.string' => '共享密钥必须为字符串',
            'apple_dev_s2s_config_id.required' => '苹果服务端API证书不能为空',
            'apple_dev_s2s_config_id.integer' => '苹果服务端API证书必须为整数',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }
        
        $bundle_id = $request->input('bundle_id');
        $app_apple_id = $request->input('app_apple_id');
        $shared_secret = $request->input('shared_secret');
        $apple_dev_s2s_config_id = $request->input('apple_dev_s2s_config_id');

        $config = AppleDevS2SConfig::find($apple_dev_s2s_config_id);
        if (!$config) {
            return ['status' => false, 'message' => '找不到对应的苹果API证书配置'];
        }

        try {
            
            $api = new AppStoreServerAPI(
                Environment::SANDBOX,
                $config->issuer_id,
                $bundle_id,
                $config->key_id,
                $config->p8_cert_content
            );

            try {
                $testNotification = $api->requestTestNotification();
                Log::channel('callback')->info('苹果IAP测试通知', ['response' => $testNotification->getTestNotificationToken()]);
                
                return [
                    'status' => true,
                    'message' => '测试通知已发送，请检查回调接口是否收到通知:'.$testNotification->getTestNotificationToken()
                ];

            } catch (AppStoreServerAPIException $e) {
                return ['status' => false, 'message' => '请求测试通知失败: ' . $e->getMessage()];
            }

        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => '请求异常: ' . $e->getMessage()
            ];
        }
    }

    private function callAppleVerifyApi(string $receiptData, $sharedSecret = null): array
    {
        $payload = [
            'receipt-data' => $receiptData,
        ];

        if ($sharedSecret) {
            $payload['password'] = $sharedSecret;
        }

        $url = 'https://buy.itunes.apple.com/verifyReceipt';

        // 可选切换到 sandbox（用于测试环境）
        $sandboxFallback = false;

        $response = Http::post($url, $payload)->json();

        // 自动 fallback 到 sandbox（只用于测试环境）
        if ($response['status'] == 21007 && !$sandboxFallback) {
            $sandboxFallback = true;
            $response = Http::post('https://sandbox.itunes.apple.com/verifyReceipt', $payload)->json();
        }

        return $response;
    }
}