<?php
namespace App\SaaSAdmin\Controllers\Manager;

use App\SaaSAdmin\AppKey;
use Illuminate\Http\Request;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\LoginInterfaceConfig;
use App\SaaSAdmin\Facades\SaaSAdmin;
use App\Models\WechatOpenPlatformConfig;
use Illuminate\Support\Facades\Validator;
use App\SaaSAdmin\Forms\AccessTokenConfig;
use App\SaaSAdmin\Forms\WechatLoginConfig;
use App\SaaSAdmin\Forms\SmsLoginConfig;

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
        ]));

        return $content;
    }

    public function saveBase(Request $request)
    {
        $switch = $request->input('switch');
        $tenant_id = SaaSAdmin::user()->id;
        $app_key = $this->getAppKey();

        if ($switch == 1) {
            $validator = Validator::make($request->all(), [
                'token_effective_duration' => 'required|integer|min:1|max:3650',
                'endpoint_allow_count' => 'required|integer|min:0|max:99999',
            ]);
           
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $login_config_data = [
                'switch' => $switch,
                'token_effective_duration' => $request->input('token_effective_duration'),
                'endpoint_allow_count' => $request->input('endpoint_allow_count'),
            ];

            try {
                app(LoginInterfaceConfig::class)->saveConfig($tenant_id, $app_key, $login_config_data);
                admin_toastr('保存成功', 'success');
                return back();
            } catch (\Exception $e) {
                admin_toastr($e->getMessage(), 'error');
                return back()->withErrors($e->getMessage())->withInput();
            }
        } else {
            app(LoginInterfaceConfig::class)->saveConfig($tenant_id, $app_key, ['switch' => $switch]);
            admin_toastr('保存成功', 'success');
            return back();
        }
    }

    public function saveWechat(Request $request)
    {
        $tenant_id = SaaSAdmin::user()->id;
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
            app(LoginInterfaceConfig::class)->saveConfig($tenant_id, $app_key, $login_config_data);
            admin_toastr('保存成功', 'success');
            return back();
        } catch (\Exception $e) {
            admin_toastr($e->getMessage(), 'error');
            return back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function saveSms(Request $request)
    {
        dd($request->all());
    }
}