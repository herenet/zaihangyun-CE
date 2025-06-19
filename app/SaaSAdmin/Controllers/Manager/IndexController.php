<?php

namespace App\SaaSAdmin\Controllers\Manager;

use App\Models\App;
use App\Models\User;
use App\Models\Order;
use GuzzleHttp\Client;
use App\SaaSAdmin\AppKey;
use App\Models\AppleOrder;
use Encore\Admin\Layout\Row;
use App\Models\ArticleConfig;
use App\Models\MessageConfig;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\Log;
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
            ['name' => '今日API调用',   'value' => $this->getApiRequestCount() . ' 次'],
        ];

        return view('saas.dashboard.environment', compact('envs'));
    }

    protected function getApiRequestCount()
    {
        $app_key = $this->getAppKey();
        $tenant_id = SaaSAdmin::user()->id;
        $today = date('Y-m-d');
        $client = new Client();
        try{
            $ret = $client->get(config('app.api_url').'/v1/stats/app?appkey='.$app_key.'&tenant_id='.$tenant_id.'&date='.$today, [
                'timeout' => 5, // 设置超时时间
                'connect_timeout' => 3
            ]);
            if ($ret->getStatusCode() == 200) {
                $data = json_decode($ret->getBody(), true);
                if (isset($data['code']) && $data['code'] == 200 && isset($data['data']['total_calls'])) {
                    return $data['data']['total_calls'];
                }
            }
            return 0;
        } catch (\Exception $e) {
            Log::error('API用量统计失败: ' . $e->getMessage());
            return 0;
        }
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

        // 获取应用信息以判断平台类型
        $app = App::where('tenant_id', $tenant_id)
            ->where('app_key', $app_key)
            ->first(['platform_type']);
            
        if (!$app) {
            // 如果应用不存在，返回空数据
            return $this->getEmptyStats();
        }

        // 根据平台类型选择订单表和相关字段
        $is_ios = ($app->platform_type == App::PLATFORM_TYPE_IOS);
        $order_model = $is_ios ? AppleOrder::class : Order::class;
        $amount_field = $is_ios ? 'amount' : 'payment_amount';
        $status_field = $is_ios ? 'payment_status' : 'status';
        $success_status = $is_ios ? AppleOrder::PAYMENT_STATUS_SUCCESS : Order::STATUS_PAID;

        // 时间范围定义
        $today_start = now()->startOfDay();
        $today_end = now()->endOfDay();
        $yesterday_start = now()->subDay()->startOfDay();
        $yesterday_end = now()->subDay()->endOfDay();

        // 用户统计
        $user_count = User::where('tenant_id', $tenant_id)
            ->where('app_key', $app_key)
            ->count();
        $user_increate = User::where('tenant_id', $tenant_id)
            ->where('app_key', $app_key)
            ->whereBetween('created_at', [$today_start, $today_end])
            ->count();
        $user_yesterday = User::where('tenant_id', $tenant_id)
            ->where('app_key', $app_key)
            ->whereBetween('created_at', [$yesterday_start, $yesterday_end])
            ->count();

        // 订单统计
        $order_total = $order_model::where('tenant_id', $tenant_id)
            ->where('app_key', $app_key)
            ->count();
        $order_increate = $order_model::where('tenant_id', $tenant_id)
            ->where('app_key', $app_key)
            ->whereBetween('created_at', [$today_start, $today_end])
            ->count();
        $order_yesterday = $order_model::where('tenant_id', $tenant_id)
            ->where('app_key', $app_key)
            ->whereBetween('created_at', [$yesterday_start, $yesterday_end])
            ->count();

        // 收入统计（只统计已支付的订单）
        $total_income = $order_model::where('tenant_id', $tenant_id)
            ->where('app_key', $app_key)
            ->where($status_field, $success_status)
            ->sum($amount_field);
        $income_increate = $order_model::where('tenant_id', $tenant_id)
            ->where('app_key', $app_key)
            ->whereBetween('created_at', [$today_start, $today_end])
            ->where($status_field, $success_status)
            ->sum($amount_field);
        $income_yesterday = $order_model::where('tenant_id', $tenant_id)
            ->where('app_key', $app_key)
            ->whereBetween('created_at', [$yesterday_start, $yesterday_end])
            ->where($status_field, $success_status)
            ->sum($amount_field);

        // 计算用户增长率
        $user_growth_rate = 0;
        $user_growth_type = 'neutral';
        $user_growth_icon = 'minus';
        if ($user_yesterday > 0) {
            $user_growth_rate = (($user_increate - $user_yesterday) / $user_yesterday) * 100;
            if ($user_growth_rate > 0) {
                $user_growth_type = 'up';
                $user_growth_icon = 'arrow-up';
            } elseif ($user_growth_rate < 0) {
                $user_growth_type = 'down';
                $user_growth_icon = 'arrow-down';
            }
        } elseif ($user_increate > 0) {
            $user_growth_rate = 100;
            $user_growth_type = 'up';
            $user_growth_icon = 'arrow-up';
        }

        // 计算订单增长率
        $order_growth_rate = 0;
        $order_growth_type = 'neutral';
        $order_growth_icon = 'minus';
        if ($order_yesterday > 0) {
            $order_growth_rate = (($order_increate - $order_yesterday) / $order_yesterday) * 100;
            if ($order_growth_rate > 0) {
                $order_growth_type = 'up';
                $order_growth_icon = 'arrow-up';
            } elseif ($order_growth_rate < 0) {
                $order_growth_type = 'down';
                $order_growth_icon = 'arrow-down';
            }
        } elseif ($order_increate > 0) {
            $order_growth_rate = 100;
            $order_growth_type = 'up';
            $order_growth_icon = 'arrow-up';
        }

        // 计算收入增长率
        $income_growth_rate = 0;
        $income_growth_type = 'neutral';
        $income_growth_icon = 'minus';
        if ($income_yesterday > 0) {
            $income_growth_rate = (($income_increate - $income_yesterday) / $income_yesterday) * 100;
            if ($income_growth_rate > 0) {
                $income_growth_type = 'up';
                $income_growth_icon = 'arrow-up';
            } elseif ($income_growth_rate < 0) {
                $income_growth_type = 'down';
                $income_growth_icon = 'arrow-down';
            }
        } elseif ($income_increate > 0) {
            $income_growth_rate = 100;
            $income_growth_type = 'up';
            $income_growth_icon = 'arrow-up';
        }

        $stats = [
            [
                'title' => '用户统计',
                'icon' => 'users',
                'gradient' => 'linear-gradient(135deg, #4086F5 0%, #6B9BF7 100%)',
                'primary' => [
                    'label' => '今日新增用户',
                    'value' => number_format($user_increate),
                    'trend' => [
                        'type' => $user_growth_type,
                        'icon' => $user_growth_icon,
                        'text' => ($user_growth_rate >= 0 ? '+' : '') . number_format($user_growth_rate, 1) . '%'
                    ]
                ],
                'secondary' => [
                    'label' => '总用户数',
                    'value' => number_format($user_count),
                    'subtitle' => '累计总用户数'
                ],
                'yesterday' => [
                    'value' => number_format($user_yesterday)
                ]
            ],
            [
                'title' => '订单统计',
                'icon' => 'shopping-cart',
                'gradient' => 'linear-gradient(135deg, #F59E0B 0%, #FBBF24 100%)',
                'primary' => [
                    'label' => '今日新增订单',
                    'value' => number_format($order_increate),
                    'trend' => [
                        'type' => $order_growth_type,
                        'icon' => $order_growth_icon,
                        'text' => ($order_growth_rate >= 0 ? '+' : '') . number_format($order_growth_rate, 1) . '%'
                    ]
                ],
                'secondary' => [
                    'label' => '总订单数',
                    'value' => number_format($order_total),
                    'subtitle' => '累计完成订单'
                ],
                'yesterday' => [
                    'value' => number_format($order_yesterday)
                ]
            ],
            [
                'title' => '收入统计',
                'icon' => 'yen',
                'gradient' => 'linear-gradient(135deg, #10B981 0%, #1AE2D6 100%)',
                'primary' => [
                    'label' => '今日新增收入',
                    'value' => '¥' . number_format($income_increate / 100, 2),
                    'trend' => [
                        'type' => $income_growth_type,
                        'icon' => $income_growth_icon,
                        'text' => ($income_growth_rate >= 0 ? '+' : '') . number_format($income_growth_rate, 1) . '%'
                    ]
                ],
                'secondary' => [
                    'label' => '总收入',
                    'value' => '¥' . number_format($total_income / 100, 2),
                    'subtitle' => '累计总收入'
                ],
                'yesterday' => [
                    'value' => '¥' . number_format($income_yesterday / 100, 2)
                ]
            ]
        ];
        
        return $stats;
    }

    /**
     * 获取空的统计数据（当应用不存在时）
     */
    protected function getEmptyStats()
    {
        return [
            [
                'title' => '用户统计',
                'icon' => 'users',
                'gradient' => 'linear-gradient(135deg, #4086F5 0%, #6B9BF7 100%)',
                'primary' => [
                    'label' => '今日新增用户',
                    'value' => '0',
                    'trend' => [
                        'type' => 'neutral',
                        'icon' => 'minus',
                        'text' => '0%'
                    ]
                ],
                'secondary' => [
                    'label' => '总用户数',
                    'value' => '0',
                    'subtitle' => '累计总用户数'
                ],
                'yesterday' => [
                    'value' => '0'
                ]
            ],
            [
                'title' => '订单统计',
                'icon' => 'shopping-cart',
                'gradient' => 'linear-gradient(135deg, #F59E0B 0%, #FBBF24 100%)',
                'primary' => [
                    'label' => '今日新增订单',
                    'value' => '0',
                    'trend' => [
                        'type' => 'neutral',
                        'icon' => 'minus',
                        'text' => '0%'
                    ]
                ],
                'secondary' => [
                    'label' => '总订单数',
                    'value' => '0',
                    'subtitle' => '累计完成订单'
                ],
                'yesterday' => [
                    'value' => '0'
                ]
            ],
            [
                'title' => '收入统计',
                'icon' => 'yen',
                'gradient' => 'linear-gradient(135deg, #10B981 0%, #1AE2D6 100%)',
                'primary' => [
                    'label' => '今日新增收入',
                    'value' => '¥0.00',
                    'trend' => [
                        'type' => 'neutral',
                        'icon' => 'minus',
                        'text' => '0%'
                    ]
                ],
                'secondary' => [
                    'label' => '总收入',
                    'value' => '¥0.00',
                    'subtitle' => '累计总收入'
                ],
                'yesterday' => [
                    'value' => '¥0.00'
                ]
            ]
        ];
    }
}