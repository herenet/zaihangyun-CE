<?php
namespace App\SaaSAdmin\Controllers\Manager;

use App\SaaSAdmin\AppKey;
use Illuminate\Http\Request;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Layout\Content;
use Alipay\EasySDK\Kernel\Config;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\OrderInterfaceConfig;
use App\SaaSAdmin\Facades\SaaSAdmin;
use App\SaaSAdmin\Forms\AlipayConfig;
use Illuminate\Support\Facades\Cache;
use App\SaaSAdmin\Forms\OrderBaseConfig;
use App\SaaSAdmin\Forms\WechatPayConfig;
use Illuminate\Support\Facades\Validator;
use App\Models\AlipayConfig as AlipayConfigModel;

class OrderConfigController extends Controller
{
    use AppKey;

    public function index(Content $content)
    {
        $content->title('接口配置');
        $content->description('订单模块');

        $content->body(Tab::forms([
            'base' => OrderBaseConfig::class,
            'wechat_pay' => WechatPayConfig::class,
            'alipay' => AlipayConfig::class,
        ]));

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
}