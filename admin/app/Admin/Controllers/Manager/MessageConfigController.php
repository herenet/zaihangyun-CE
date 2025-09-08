<?php

namespace App\Admin\Controllers\Manager;

use App\Admin\AppKey;
use Illuminate\Http\Request;
use App\Models\MessageConfig;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use App\Admin\Forms\MessageBaseConfig;

class MessageConfigController extends Controller
{
    use AppKey;
    public function index(Content $content)
    {
        $content->title('接口配置');
        $content->description('消息模块');
        $content->body(Tab::forms([
            'base' => MessageBaseConfig::class,
        ]));
        return $content;
    }

    public function saveBase(Request $request)
    {
        $switch = $request->input('switch');
        $app_key = $this->getAppKey();

        $validator = Validator::make($request->all(), [
            'switch' => 'required|in:0,1',
        ], [
            'switch.required' => '是否启用接口不能为空',
            'switch.in' => '是否启用接口必须为0或1',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

        if ($switch == 1) {
            try {
                $config_data = [
                    'switch' => $switch,
                ];
                app(MessageConfig::class)->saveConfig($app_key, $config_data);
                $this->clearAPICache($app_key);
                admin_toastr('保存成功', 'success');
                return back();
            } catch (\Exception $e) {
                admin_toastr($e->getMessage(), 'error');
                return back()->withErrors($e->getMessage())->withInput();
            }
        }else{
            $config_data = [
                'switch' => $switch,
            ];
            app(MessageConfig::class)->saveConfig($app_key, $config_data);
            $this->clearAPICache($app_key);
            admin_toastr('保存成功', 'success');
            return back();
        }
    }

    public function clearAPICache($app_key)
    {
        $cacheKey = 'message_config|' . $app_key;
        Cache::forget($cacheKey);
    }
}
