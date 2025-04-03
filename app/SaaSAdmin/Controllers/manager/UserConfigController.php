<?php
namespace App\SaaSAdmin\Controllers\Manager;

use App\SaaSAdmin\AppKey;
use Illuminate\Http\Request;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use App\Models\LoginInterfaceConfig;
use Illuminate\Support\Facades\Validator;
use App\SaaSAdmin\Forms\UserInterfaceConfig;
use App\SaaSAdmin\Facades\SaaSAdmin;

class UserConfigController extends Controller
{
    use AppKey;

    public function index(Content $content)
    {
        $content->title('接口配置');
        $content->description('用户模块');

        // $this->showFormParameters($content);
        $tab = new Tab();

        $tab->add('登录接口配置', new UserInterfaceConfig());

        $content->body($tab);

        return $content;
    }

    public function save(Request $request)
    {
        $switch = $request->input('switch');
        if ($switch == 1) {
            $validator = Validator::make($request->all(), [
                'token_effective_duration' => 'required|integer|min:1|max:3650',
                'jwt_payload_fields' => 'required|array|min:1|max:7',
                'suport_wechat_login' => 'required|in:0,1',
                'selected_wechat_open_platform_id' => 'required_if:suport_wechat_login,1',
                'suport_mobile_login' => 'required|in:0,1',
            ]);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            if ($request->input('suport_wechat_login') == 'off' && $request->input('suport_mobile_login') == 'off') {
                admin_error('请至少启用一种登录方式');
                return back()->withInput();
            }
            $data = [
                'switch' => $switch,
                'token_effective_duration' => $request->input('token_effective_duration'),
                'jwt_payload_fields' => array_filter($request->input('jwt_payload_fields')),
                'suport_wechat_login' => $request->input('suport_wechat_login'),
                'selected_wechat_open_platform_id' => $request->input('selected_wechat_open_platform_id'),
                'suport_mobile_login' => $request->input('suport_mobile_login')
            ];
        }
        try {
            $tenant_id = SaaSAdmin::user()->id;
            $app_key = $this->getAppKey();
            $config = app(LoginInterfaceConfig::class)->saveConfig($tenant_id, $app_key, $data);
            if ($config) {
                admin_toastr('保存成功', 'success');
                return back();
            } else {
                admin_toastr('保存失败', 'error');
                return back()->withErrors('保存失败')->withInput();
            }
        } catch (\Exception $e) {
            admin_toastr($e->getMessage(), 'error');
            return back()->withErrors($e->getMessage())->withInput();
        }
    }
}