<?php
namespace App\SaaSAdmin\Controllers\Manager;

use App\SaaSAdmin\AppKey;
use Illuminate\Http\Request;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\SaaSAdmin\Facades\SaaSAdmin;
use App\SaaSAdmin\Forms\OrderBaseConfig;
use App\SaaSAdmin\Forms\WechatPayConfig;
use Illuminate\Support\Facades\Validator;
use App\Models\OrderInterfaceConfig;

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
        ]));

        return $content;
    }

    public function saveBase(Request $request)
    {
        $switch = $request->input('switch');
        $tenant_id = SaaSAdmin::user()->id;
        $app_key = $this->getAppKey();

        if ($switch == 1) {
            $order_config_data = [
                'switch' => $switch,
            ];

            try {
                app(OrderInterfaceConfig::class)->saveConfig($tenant_id, $app_key, $order_config_data);
                admin_toastr('保存成功', 'success');
                return back();
            } catch (\Exception $e) {
                admin_toastr($e->getMessage(), 'error');
                return back()->withErrors($e->getMessage())->withInput();
            }
        } else {
            app(OrderInterfaceConfig::class)->saveConfig($tenant_id, $app_key, ['switch' => $switch]);
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
            admin_toastr('保存成功', 'success');
            return back();
        } catch (\Exception $e) {
            admin_toastr($e->getMessage(), 'error');
            return back()->withErrors($e->getMessage())->withInput();
        }
    }

}