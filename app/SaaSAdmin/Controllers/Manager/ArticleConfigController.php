<?php

namespace App\SaaSAdmin\Controllers\Manager;

use Illuminate\Http\Request;
use App\Models\ArticleConfig;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use App\SaaSAdmin\Facades\SaaSAdmin;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use App\SaaSAdmin\Forms\ArticleBaseConfig;
use App\SaaSAdmin\AppKey;

class ArticleConfigController extends Controller
{
    use AppKey;

    public function index(Content $content)
    {
        $content->title('文档配置');
        $content->description('文档模块');

        $content->body(Tab::forms([
            'base' => ArticleBaseConfig::class,
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
            'list_theme' => 'required|in:'.implode(',', array_keys(ArticleConfig::$listTheme)),
            'content_theme' => 'required|in:'.implode(',', array_keys(ArticleConfig::$contentTheme)),
        ], [
            'switch.required' => '是否启用接口不能为空',
            'switch.in' => '是否启用接口必须为0或1',
            'list_theme.required' => '列表主题不能为空',
            'list_theme.in' => '列表主题必须为'.implode(',', array_keys(ArticleConfig::$listTheme)),
            'content_theme.required' => '内容主题不能为空',
            'content_theme.in' => '内容主题必须为'.implode(',', array_keys(ArticleConfig::$contentTheme)),
        ]);


        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if ($switch == 1) {
            try {
                $config_data = [
                    'switch' => $switch,
                    'list_theme' => $request->input('list_theme'),
                    'content_theme' => $request->input('content_theme'),
                ];
                app(ArticleConfig::class)->saveConfig($app_key, $tenant_id, $config_data);
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
            app(ArticleConfig::class)->saveConfig($app_key, $tenant_id, $config_data);
            $this->clearAPICache($app_key);
            admin_toastr('保存成功', 'success');
            return back();
        }
    }

    protected function clearAPICache($app_key)
    {
        $cache_key = 'article_config|'.$app_key;
        Cache::store('api_cache')->forget($cache_key);
    }
}