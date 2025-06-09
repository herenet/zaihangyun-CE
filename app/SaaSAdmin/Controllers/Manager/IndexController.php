<?php

namespace App\SaaSAdmin\Controllers\Manager;

use App\Models\App;
use App\Models\User;
use App\Models\Order;
use App\SaaSAdmin\AppKey;
use Illuminate\Support\Arr;
use Encore\Admin\Layout\Row;
use App\Models\ArticleConfig;
use App\Models\MessageConfig;
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
        $app_info = app(App::class)->getAppInfo($app_key);
        $envs = [
            ['name' => '名称',       'value' => $app_info['name']],
            ['name' => 'AppKey',   'value' => $app_info['app_key']],
            ['name' => 'AppSecret',   'value' => $app_info['app_secret']],
            ['name' => '平台',       'value' => App::$platformType[$app_info['platform_type']]],
            ['name' => '创建时间',   'value' => $app_info['created_at']],
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
        $message_config = app(MessageConfig::class)->getConfig($app_key, $tenant_id);
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
                'color' => '#dd4b39'
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

        $user_yesterday = User::where('tenant_id', $tenant_id)
            ->where('app_key', $app_key)
            ->whereBetween('created_at', [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()])
            ->count();

        $order_yesterday = Order::where('tenant_id', $tenant_id)
            ->where('app_key', $app_key)
            ->whereBetween('created_at', [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()])
            ->count();

        $income_yesterday = Order::where('tenant_id', $tenant_id)
            ->where('app_key', $app_key)
            ->whereBetween('created_at', [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()])
            ->where('status', 2)
            ->sum('payment_amount');

        $stats = [
            [
                'title' => '用户统计',
                'icon' => 'users',
                'gradient' => 'linear-gradient(135deg, #4086F5 0%, #6B9BF7 100%)',
                'primary' => [
                    'label' => '今日新增用户',
                    'value' => '156',
                    'trend' => [
                        'type' => 'up',
                        'icon' => 'arrow-up',
                        'text' => '+23.5%'
                    ]
                ],
                'secondary' => [
                    'label' => '总用户数',
                    'value' => '12,845',
                    'subtitle' => '累计总用户数'
                ],
                'yesterday' => [
                    'value' => '126'
                ]
            ],
            [
                'title' => '收入统计',
                'icon' => 'yen',
                'gradient' => 'linear-gradient(135deg, #10B981 0%, #1AE2D6 100%)',
                'primary' => [
                    'label' => '今日新增收入',
                    'value' => '¥8,965',
                    'trend' => [
                        'type' => 'up',
                        'icon' => 'arrow-up',
                        'text' => '+18.2%'
                    ]
                ],
                'secondary' => [
                    'label' => '总收入',
                    'value' => '¥1,286,430',
                    'subtitle' => '累计总收入'
                ],
                'yesterday' => [
                    'value' => '¥7,580'
                ]
            ],
            [
                'title' => '订单统计',
                'icon' => 'shopping-cart',
                'gradient' => 'linear-gradient(135deg, #F59E0B 0%, #FBBF24 100%)',
                'primary' => [
                    'label' => '今日新增订单',
                    'value' => '89',
                    'trend' => [
                        'type' => 'up',
                        'icon' => 'arrow-up',
                        'text' => '+12.8%'
                    ]
                ],
                'secondary' => [
                    'label' => '总订单数',
                    'value' => '5,432',
                    'subtitle' => '累计完成订单'
                ],
                'yesterday' => [
                    'value' => '79'
                ]
            ]
        ];
        
        return $stats;
    }
}