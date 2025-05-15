<?php

namespace App\SaaSAdmin\Controllers\Manager;

use App\Models\App;
use App\Models\User;
use App\Models\Order;
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
        $stats = $this->stats();
        return $content
        ->title('应用概况')
        ->row(view('manager.partials.stats', compact('stats')))
        ->row(function (Row $row) {
            $row->column(4, $this->appinfo());
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
                'value' => '¥' . number_format($total_income ?? 0, 2),
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
                'value' => '¥' . number_format($income_increate ?? 0, 2),
                'icon' => 'line-chart',
                'color' => '#f39c12'
            ]
        ];
        
        return $stats;
    }
}