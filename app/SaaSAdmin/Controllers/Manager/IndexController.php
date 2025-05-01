<?php

namespace App\SaaSAdmin\Controllers\Manager;

use App\Models\App;
use App\SaaSAdmin\AppKey;
use Illuminate\Support\Arr;
use Encore\Admin\Layout\Row;
use Encore\Admin\Layout\Content;
use App\SaaSAdmin\Facades\SaaSAdmin;
use Encore\Admin\Controllers\AdminController;

class IndexController extends AdminController
{
    use AppKey;

    public function index(Content $content)
    {
        return $content
        ->title('应用概况')
        ->row(view('manager.partials.stats'))
        ->row(function (Row $row) {
            $row->column(4, $this->environment());
        });
    }

    public function environment()
    {
        $app_key = $this->getAppKey();
        $app_info = app(App::class)->where(['app_key' => $app_key, 'tenant_id' => SaaSAdmin::user()->id])->first();
        $envs = [
            ['name' => '名称',       'value' => $app_info->name],
            ['name' => 'AppKey',   'value' => $app_info->app_key],
            ['name' => 'AppSecret',   'value' => $app_info->app_secret],
        ];


        return view('saas.dashboard.environment', compact('envs'));
    }
}