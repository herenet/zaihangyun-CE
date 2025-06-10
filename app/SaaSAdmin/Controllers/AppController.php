<?php

namespace App\SaaSAdmin\Controllers;

use App\Models\App;
use App\Models\User;
use App\Models\Order;
use App\Models\AppleOrder;
use App\Libs\Helpers;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Illuminate\Http\Request;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use App\SaaSAdmin\Facades\SaaSAdmin;
use App\SaaSAdmin\Extensions\Widget\StatsInfoBox;
use Illuminate\Support\Facades\Cache;

class AppController extends Controller
{

    public function index(Content $content)
    {
        return $content
            ->title('创建应用')
            ->body($this->form());
    }

    public function home(Content $content)
    {
        return $content
        ->title('应用列表')
        ->body(function () use ($content) {
            $this->infoBox($content);
        })
        ->body($this->list());
    }

    public function list()
    {
        Admin::style('.box-header { display: none; }');
        $grid = new Grid(new App());
        
        $grid->model()->where('tenant_id', SaaSAdmin::user()->id);
        $grid->model()->orderBy('created_at', 'desc');
        
        // 获取Controller实例，用于在闭包中调用方法
        $controller = $this;
        
        $grid->column('platform_type', '平台')->display(function ($value) {
            return App::$platformIcons[$value] ?? '';
        });
        $grid->column('name', '应用名称')->display(function ($value) {
            /** @var \App\Models\App $this */
            $url = admin_url('app/manager/' . $this->app_key);
            return "<a href='{$url}' onclick='window.location.href=this.href;return false;'>{$value}</a>";
        });
        
        // 今日新增注册
        $grid->column('user_today', '今日新增注册')->display(function () use ($controller) {
            /** @var \App\Models\App $this */
            $stats = $controller->getAppStatsData($this->tenant_id, $this->app_key);
            return number_format($stats['users']['today']);
        });
        
        // 昨日新增注册
        $grid->column('user_yesterday', '昨日新增注册')->display(function () use ($controller) {
            /** @var \App\Models\App $this */
            $stats = $controller->getAppStatsData($this->tenant_id, $this->app_key);
            return number_format($stats['users']['yesterday']);
        });
        
        // 今日新增订单
        $grid->column('order_today', '今日新增订单')->display(function () use ($controller) {
            /** @var \App\Models\App $this */
            $stats = $controller->getAppStatsData($this->tenant_id, $this->app_key);
            return number_format($stats['orders']['today']);
        });
        
        // 昨日新增订单
        $grid->column('order_yesterday', '昨日新增订单')->display(function () use ($controller) {
            /** @var \App\Models\App $this */
            $stats = $controller->getAppStatsData($this->tenant_id, $this->app_key);
            return number_format($stats['orders']['yesterday']);
        });
        
        // 今日新增收入
        $grid->column('income_today', '今日新增收入')->display(function () use ($controller) {
            /** @var \App\Models\App $this */
            $stats = $controller->getAppStatsData($this->tenant_id, $this->app_key);
            return '¥' . number_format($stats['income']['today'] / 100, 2);
        });
        
        // 昨日新增收入
        $grid->column('income_yesterday', '昨日新增收入')->display(function () use ($controller) {
            /** @var \App\Models\App $this */
            $stats = $controller->getAppStatsData($this->tenant_id, $this->app_key);
            return '¥' . number_format($stats['income']['yesterday'] / 100, 2);
        });
        
        $grid->disableActions();
        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->disableFilter();
        $grid->disableRowSelector();
        $grid->disableColumnSelector();
        return $grid;
    }

    protected function infoBox(Content $content)
    {
        // 获取统计数据（带缓存）
        $statsData = $this->getStatsData();
        
        $content->row(function ($row) use ($statsData) {
            // 新增注册
            $row->column(4, new StatsInfoBox(
                '新增注册',
                'users',
                'aqua',
                $statsData['users']['today'],
                $statsData['users']['yesterday'],
                '总用户数',
                $statsData['users']['total'],
                '较上月增长 15.8%'
            ));
            
            // 新增订单
            $row->column(4, new StatsInfoBox(
                '新增订单',
                'shopping-cart',
                'green',
                $statsData['orders']['today'],
                $statsData['orders']['yesterday'],
                '总订单数',
                $statsData['orders']['total'],
                '累计完成订单'
            ));
            
            // 新增收入
            $row->column(4, new StatsInfoBox(
                '新增收入',
                'money',
                'yellow',
                $statsData['income']['today'],
                $statsData['income']['yesterday'],
                '总收入',
                $statsData['income']['total'],
                '本月累计收入'
            ));
        });

        return $content;
    }

    /**
     * 获取统计数据（带缓存）
     * 缓存时间：60秒
     */
    protected function getStatsData()
    {
        $tenant_id = SaaSAdmin::user()->id;
        $cache_key = "app_stats_data_tenant_{$tenant_id}";
        
        return Cache::remember($cache_key, 60, function () use ($tenant_id) {
            return $this->calculateStatsData($tenant_id);
        });
    }

    /**
     * 计算统计数据
     */
    protected function calculateStatsData($tenant_id)
    {
        // 获取当前租户的所有应用
        $apps = App::where('tenant_id', $tenant_id)->get(['app_key', 'platform_type']);
        
        if ($apps->isEmpty()) {
            return $this->getEmptyStatsData();
        }

        // 时间范围定义
        $today_start = now()->startOfDay();
        $today_end = now()->endOfDay();
        $yesterday_start = now()->subDay()->startOfDay();
        $yesterday_end = now()->subDay()->endOfDay();

        // 按平台类型分组应用
        $android_apps = $apps->where('platform_type', App::PLATFORM_TYPE_ANDROID)->pluck('app_key')->toArray();
        $ios_apps = $apps->where('platform_type', App::PLATFORM_TYPE_IOS)->pluck('app_key')->toArray();

        // 用户统计（所有平台通用）
        $all_app_keys = $apps->pluck('app_key')->toArray();
        $users_today = User::where('tenant_id', $tenant_id)
            ->whereIn('app_key', $all_app_keys)
            ->whereBetween('created_at', [$today_start, $today_end])
            ->count();

        $users_yesterday = User::where('tenant_id', $tenant_id)
            ->whereIn('app_key', $all_app_keys)
            ->whereBetween('created_at', [$yesterday_start, $yesterday_end])
            ->count();

        $users_total = User::where('tenant_id', $tenant_id)
            ->whereIn('app_key', $all_app_keys)
            ->count();

        // Android订单统计
        $android_orders_today = 0;
        $android_orders_yesterday = 0;
        $android_orders_total = 0;
        $android_income_today = 0;
        $android_income_yesterday = 0;
        $android_income_total = 0;

        if (!empty($android_apps)) {
            $android_orders_today = Order::where('tenant_id', $tenant_id)
                ->whereIn('app_key', $android_apps)
                ->whereBetween('created_at', [$today_start, $today_end])
                ->count();

            $android_orders_yesterday = Order::where('tenant_id', $tenant_id)
                ->whereIn('app_key', $android_apps)
                ->whereBetween('created_at', [$yesterday_start, $yesterday_end])
                ->count();

            $android_orders_total = Order::where('tenant_id', $tenant_id)
                ->whereIn('app_key', $android_apps)
                ->count();

            // Android使用payment_amount字段，status字段
            $android_income_today = Order::where('tenant_id', $tenant_id)
                ->whereIn('app_key', $android_apps)
                ->where('status', 2)
                ->whereBetween('created_at', [$today_start, $today_end])
                ->sum('payment_amount');

            $android_income_yesterday = Order::where('tenant_id', $tenant_id)
                ->whereIn('app_key', $android_apps)
                ->where('status', 2)
                ->whereBetween('created_at', [$yesterday_start, $yesterday_end])
                ->sum('payment_amount');

            $android_income_total = Order::where('tenant_id', $tenant_id)
                ->whereIn('app_key', $android_apps)
                ->where('status', 2)
                ->sum('payment_amount');
        }

        // iOS订单统计
        $ios_orders_today = 0;
        $ios_orders_yesterday = 0;
        $ios_orders_total = 0;
        $ios_income_today = 0;
        $ios_income_yesterday = 0;
        $ios_income_total = 0;

        if (!empty($ios_apps)) {
            $ios_orders_today = AppleOrder::where('tenant_id', $tenant_id)
                ->whereIn('app_key', $ios_apps)
                ->whereBetween('created_at', [$today_start, $today_end])
                ->count();

            $ios_orders_yesterday = AppleOrder::where('tenant_id', $tenant_id)
                ->whereIn('app_key', $ios_apps)
                ->whereBetween('created_at', [$yesterday_start, $yesterday_end])
                ->count();

            $ios_orders_total = AppleOrder::where('tenant_id', $tenant_id)
                ->whereIn('app_key', $ios_apps)
                ->count();

            // iOS使用amount字段，payment_status字段
            $ios_income_today = AppleOrder::where('tenant_id', $tenant_id)
                ->whereIn('app_key', $ios_apps)
                ->where('payment_status', AppleOrder::PAYMENT_STATUS_SUCCESS)
                ->whereBetween('created_at', [$today_start, $today_end])
                ->sum('amount');

            $ios_income_yesterday = AppleOrder::where('tenant_id', $tenant_id)
                ->whereIn('app_key', $ios_apps)
                ->where('payment_status', AppleOrder::PAYMENT_STATUS_SUCCESS)
                ->whereBetween('created_at', [$yesterday_start, $yesterday_end])
                ->sum('amount');

            $ios_income_total = AppleOrder::where('tenant_id', $tenant_id)
                ->whereIn('app_key', $ios_apps)
                ->where('payment_status', AppleOrder::PAYMENT_STATUS_SUCCESS)
                ->sum('amount');
        }

        // 合并统计结果
        return [
            'users' => [
                'today' => $users_today,
                'yesterday' => $users_yesterday,
                'total' => $users_total
            ],
            'orders' => [
                'today' => $android_orders_today + $ios_orders_today,
                'yesterday' => $android_orders_yesterday + $ios_orders_yesterday,
                'total' => $android_orders_total + $ios_orders_total
            ],
            'income' => [
                'today' => ($android_income_today ?? 0) + ($ios_income_today ?? 0), // 以分为单位存储
                'yesterday' => ($android_income_yesterday ?? 0) + ($ios_income_yesterday ?? 0),
                'total' => ($android_income_total ?? 0) + ($ios_income_total ?? 0)
            ]
        ];
    }

    /**
     * 获取空的统计数据（当没有应用时）
     */
    protected function getEmptyStatsData()
    {
        return [
            'users' => [
                'today' => 0,
                'yesterday' => 0,
                'total' => 0
            ],
            'orders' => [
                'today' => 0,
                'yesterday' => 0,
                'total' => 0
            ],
            'income' => [
                'today' => 0,
                'yesterday' => 0,
                'total' => 0
            ]
        ];
    }

    /**
     * 获取模拟统计数据
     * 后续可以替换为真实的数据库查询
     */
    protected function getMockStatsData()
    {
        return [
            'users' => [
                'today' => 1024,
                'yesterday' => 856,
                'total' => 12845
            ],
            'orders' => [
                'today' => 120,
                'yesterday' => 150,
                'total' => 5432
            ],
            'income' => [
                'today' => 278600, // 以分为单位
                'yesterday' => 245300,
                'total' => 128643000
            ]
        ];
    }

    public function form()
    {
        $form = new Form(new App());
        $form->setTitle('应用信息');
        $form->text('name', '应用名称');
        $form->radio('platform_type', '平台')->options(App::$platformType)->default(App::PLATFORM_TYPE_ANDROID);

        $form->setAction(admin_url('apps'));

        $form->saved(function (Form $form) {
            $app_key = $form->model()->app_key;
            app(App::class)->clearAppInfoCache($app_key);
            
            // 清除统计数据缓存
            $this->clearAllStatsCache();
        });

        $form->footer(function (Form\Footer $footer) {
            $footer->disableViewCheck();
            $footer->disableEditingCheck();
            $footer->disableCreatingCheck();
            $footer->disableReset();
        });

        return $form;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:32',
            'platform_type' => 'required|integer|in:1,2,3',
        ], [
            'name.required' => '应用名称不能为空',
            'name.string' => '应用名称必须为字符串',
            'name.max' => '应用名称不能超过32个字符', 
            'platform_type.required' => '平台不能为空',
            'platform_type.integer' => '平台必须为整数',
            'platform_type.in' => '平台必须为1或2',
        ]);
        $data['app_key'] = Helpers::generateAppKey();
        $data['app_secret'] = Helpers::generateAppSecret();
        $data['tenant_id'] = SaaSAdmin::user()->id;
        App::create($data);
        
        // 清除统计数据缓存
        $this->clearAllStatsCache();
        
        admin_toastr('创建成功', 'success');
        return redirect()->to(admin_url('/'));
    }

    /**
     * 清除所有统计数据缓存
     * 可以在数据发生变化时调用此方法
     */
    public function clearAllStatsCache()
    {
        $tenant_id = SaaSAdmin::user()->id;
        
        // 清除租户级别的缓存
        $cache_key = "app_stats_data_tenant_{$tenant_id}";
        Cache::forget($cache_key);
        
        // 清除所有应用的缓存
        $app_keys = App::where('tenant_id', $tenant_id)->pluck('app_key')->toArray();
        foreach ($app_keys as $app_key) {
            $app_cache_key = "single_app_stats_data_tenant_{$tenant_id}_app_{$app_key}";
            Cache::forget($app_cache_key);
        }
    }

    /**
     * 获取单个应用的统计数据（带缓存）
     * 缓存时间：60秒
     */
    public function getAppStatsData($tenant_id, $app_key)
    {
        $cache_key = "single_app_stats_data_tenant_{$tenant_id}_app_{$app_key}";
        
        return Cache::remember($cache_key, 60, function () use ($tenant_id, $app_key) {
            return $this->calculateSingleAppStatsData($tenant_id, $app_key);
        });
    }

    /**
     * 计算单个应用的统计数据
     */
    protected function calculateSingleAppStatsData($tenant_id, $app_key)
    {
        // 获取应用平台类型
        $app = App::where('tenant_id', $tenant_id)
            ->where('app_key', $app_key)
            ->first(['platform_type']);
            
        if (!$app) {
            return [
                'users' => ['today' => 0, 'yesterday' => 0, 'total' => 0],
                'orders' => ['today' => 0, 'yesterday' => 0, 'total' => 0],
                'income' => ['today' => 0, 'yesterday' => 0, 'total' => 0]
            ];
        }

        // 时间范围定义
        $today_start = now()->startOfDay();
        $today_end = now()->endOfDay();
        $yesterday_start = now()->subDay()->startOfDay();
        $yesterday_end = now()->subDay()->endOfDay();

        // 用户统计（所有平台通用）
        $users_today = User::where('tenant_id', $tenant_id)
            ->where('app_key', $app_key)
            ->whereBetween('created_at', [$today_start, $today_end])
            ->count();

        $users_yesterday = User::where('tenant_id', $tenant_id)
            ->where('app_key', $app_key)
            ->whereBetween('created_at', [$yesterday_start, $yesterday_end])
            ->count();

        $users_total = User::where('tenant_id', $tenant_id)
            ->where('app_key', $app_key)
            ->count();

        // 根据平台类型选择订单表和相关字段
        $is_ios = ($app->platform_type == App::PLATFORM_TYPE_IOS);
        $order_model = $is_ios ? AppleOrder::class : Order::class;
        $amount_field = $is_ios ? 'amount' : 'payment_amount';
        $status_field = $is_ios ? 'payment_status' : 'status';
        $success_status = $is_ios ? AppleOrder::PAYMENT_STATUS_SUCCESS : Order::STATUS_PAID;

        // 订单统计
        $orders_today = $order_model::where('tenant_id', $tenant_id)
            ->where('app_key', $app_key)
            ->whereBetween('created_at', [$today_start, $today_end])
            ->count();

        $orders_yesterday = $order_model::where('tenant_id', $tenant_id)
            ->where('app_key', $app_key)
            ->whereBetween('created_at', [$yesterday_start, $yesterday_end])
            ->count();

        $orders_total = $order_model::where('tenant_id', $tenant_id)
            ->where('app_key', $app_key)
            ->count();

        // 收入统计（只统计已支付的订单）
        $income_today = $order_model::where('tenant_id', $tenant_id)
            ->where('app_key', $app_key)
            ->where($status_field, $success_status)
            ->whereBetween('created_at', [$today_start, $today_end])
            ->sum($amount_field);

        $income_yesterday = $order_model::where('tenant_id', $tenant_id)
            ->where('app_key', $app_key)
            ->where($status_field, $success_status)
            ->whereBetween('created_at', [$yesterday_start, $yesterday_end])
            ->sum($amount_field);

        $income_total = $order_model::where('tenant_id', $tenant_id)
            ->where('app_key', $app_key)
            ->where($status_field, $success_status)
            ->sum($amount_field);

        return [
            'users' => [
                'today' => $users_today,
                'yesterday' => $users_yesterday,
                'total' => $users_total
            ],
            'orders' => [
                'today' => $orders_today,
                'yesterday' => $orders_yesterday,
                'total' => $orders_total
            ],
            'income' => [
                'today' => $income_today ?? 0, // 以分为单位存储
                'yesterday' => $income_yesterday ?? 0,
                'total' => $income_total ?? 0
            ]
        ];
    }

    /**
     * 清除统计数据缓存
     * 可以在数据发生变化时调用此方法
     */
    public function clearStatsCache()
    {
        $this->clearAllStatsCache();
    }
}