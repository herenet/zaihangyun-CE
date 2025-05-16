<?php

namespace App\SaaSAdmin\Controllers\Manager;

use App\Models\App;
use App\Models\User;
use App\Models\Order;
use App\SaaSAdmin\AppKey;
use Illuminate\Support\Arr;
use Encore\Admin\Layout\Row;
use App\Models\ArticleConfig;
use Encore\Admin\Layout\Content;
use App\Models\LoginInterfaceConfig;
use App\Models\OrderInterfaceConfig;
use App\SaaSAdmin\Facades\SaaSAdmin;
use Encore\Admin\Controllers\AdminController;

class IndexController extends AdminController
{
    use AppKey;

    public function index(Content $content)
    {
        $stats = $this->stats();
        return $content
        ->title('应用概况')
        ->row(view('manager.partials.stats', compact('stats')))
        ->row(function (Row $row) {
            $row->column(4, $this->appinfo());
            $row->column(8, $this->moduleList());
        });
    }

    public function appinfo()
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

    protected function moduleList()
    {
        $app_key = $this->getAppKey();
        $tenant_id = SaaSAdmin::user()->id;
        
        $order_interface_config = app(OrderInterfaceConfig::class)->getConfig($tenant_id, $app_key);
        $user_interface_config = app(LoginInterfaceConfig::class)->getConfig($tenant_id, $app_key);
        $article_config = app(ArticleConfig::class)->getConfig($app_key, $tenant_id);
        // 模块列表及其描述
        $modules = [
            [
                'name' => '应用设置',
                'description' => '管理APP版本、渠道信息及应用参数，支持多渠道统一配置，所有配置项可通过API接入。',
                'enabled' => true, // 假设此模块已开通
                'icon' => 'cog',
                'color' => '#3c8dbc'
            ],
            [
                'name' => '用户管理',
                'description' => '支持用户信息管理及多种登录方式（微信、手机号、Apple登录），提供完整API接口。',
                'enabled' => isset($user_interface_config['switch']) ? ($user_interface_config['switch'] ? true : false) : false,
                'url' => admin_url('app/manager/'.$this->getAppKey().'/user/config'),
                'icon' => 'users',
                'color' => '#00a65a'
            ],
            [
                'name' => '订单管理',
                'description' => '提供商品、订单及收退款管理，兼容微信、支付宝、Apple Pay等支付通道，支持API调用。',
                'enabled' => isset($order_interface_config['switch']) ? ($order_interface_config['switch'] ? true : false) : false,
                'url' => admin_url('app/manager/'.$this->getAppKey().'/order/config'),
                'icon' => 'shopping-cart',
                'color' => '#f39c12'
            ],
            [
                'name' => '文档管理',
                'description' => '管理帮助文档、协议内容等资料，支持文档分类与内容维护，便于接入端渲染与展示。',
                'enabled' => isset($article_config['switch']) ? ($article_config['switch'] ? true : false) : false,
                'url' => admin_url('app/manager/'.$this->getAppKey().'/article/config'),
                'icon' => 'file-text',
                'color' => '#605ca8'
            ],
            [
                'name' => '用户互动',
                'description' => '提供用户反馈记录和通知内容管理接口，便于收集意见和同步历史通知数据。',
                'enabled' => isset($message_config['switch']) ? ($message_config['switch'] ? true : false) : false,
                'url' => admin_url('app/manager/'.$this->getAppKey().'/message/config'),
                'icon' => 'comments',
                'color' => '#605ca8'
            ],
        ];
        return view('manager.partials.modules', compact('modules'));
    }

    protected function stats()
    {
        $app_key = $this->getAppKey();
        $tenant_id = SaaSAdmin::user()->id;

        $user_count = User::where('tenant_id', $tenant_id)
            ->where('app_key', $app_key)
            ->count();
        $user_increate = User::where('tenant_id', $tenant_id)
            ->where('app_key', $app_key)->where('created_at', '>=', now()->startOfDay())
            ->count();
        $order_increate = Order::where('tenant_id', $tenant_id)
            ->where('app_key', $app_key)
            ->where('created_at', '>=', now()->startOfDay())
            ->count();
        $income_increate = Order::where('tenant_id', $tenant_id)
            ->where('app_key', $app_key)
            ->where('created_at', '>=', now()->startOfDay())
            ->where('status', 2)
            ->sum('payment_amount');
        $total_income = Order::where('tenant_id', $tenant_id)
            ->where('app_key', $app_key)
            ->where('status', 2)
            ->sum('payment_amount');

        $stats = [
            [
                'title' => '总用户数',
                'value' => $user_count ?? 0,
                'icon' => 'users',
                'color' => '#0073b7'
            ],
            [
                'title' => '当日新增用户',
                'value' => $user_increate ?? 0,
                'icon' => 'user-plus',
                'color' => '#00c0ef'
            ],
            [
                'title' => '总收入',
                'value' => '¥' . number_format($total_income/100 ?? 0, 2),
                'icon' => 'rmb',
                'color' => '#dd4b39'
            ],
            [
                'title' => '当日新增订单',
                'value' => $order_increate ?? 0,
                'icon' => 'shopping-cart',
                'color' => '#00a65a'
            ],
            [
                'title' => '当日新增收入',
                'value' => '¥' . number_format($income_increate/100 ?? 0, 2),
                'icon' => 'line-chart',
                'color' => '#f39c12'
            ]
        ];
        
        return $stats;
    }
}