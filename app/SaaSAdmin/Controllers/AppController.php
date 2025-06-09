<?php

namespace App\SaaSAdmin\Controllers;

use App\Models\App;
use App\Libs\Helpers;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Illuminate\Http\Request;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use App\SaaSAdmin\Facades\SaaSAdmin;
use App\SaaSAdmin\Extensions\Widget\StatsInfoBox;

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
        $grid->column('platform_type', '平台')->display(function ($value) {
            return App::$platformIcons[$value] ?? '';
        });
        $grid->column('name', '应用名称')->display(function ($value) {
            /** @var \App\Models\App $this */
            $url = admin_url('app/manager/' . $this->app_key);
            return "<a href='{$url}' onclick='window.location.href=this.href;return false;'>{$value}</a>";
        });
        
        $grid->column('user_increate', '今日新增注册')->sortable();
        $grid->column('user_increate', '昨日新增注册')->sortable();
        $grid->column('order_increate', '今日新增订单')->sortable();
        $grid->column('order_increate', '昨日新增订单')->sortable();
        $grid->column('income_increate', '今日新增收入')->sortable();
        $grid->column('income_increate', '昨日新增收入')->sortable();
        $grid->disableActions();
        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->disableFilter();
        $grid->disableRowSelector();
        $grid->disableRowSelector();
        $grid->disableColumnSelector();
        return $grid;
    }

    protected function infoBox(Content $content)
    {
        // 模拟数据 - 后续可以替换为真实数据查询
        $statsData = $this->getMockStatsData();
        
        $content->row(function ($row) use ($statsData) {
            // 新增注册
            $row->column(4, new StatsInfoBox(
                '新增注册',
                'users',
                'aqua',
                $statsData['users']['today'],
                $statsData['users']['yesterday']
            ));
            
            // 新增订单
            $row->column(4, new StatsInfoBox(
                '新增订单',
                'shopping-cart',
                'green',
                $statsData['orders']['today'],
                $statsData['orders']['yesterday']
            ));
            
            // 新增收入
            $row->column(4, new StatsInfoBox(
                '新增收入',
                'money',
                'yellow',
                $statsData['income']['today'],
                $statsData['income']['yesterday']
            ));
        });

        return $content;
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
                'yesterday' => 856
            ],
            'orders' => [
                'today' => 120,  // 改为下降的数据
                'yesterday' => 150
            ],
            'income' => [
                'today' => 278600, // 以分为单位，显示时会转换为元
                'yesterday' => 245300
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
        admin_toastr('创建成功', 'success');
        return redirect()->to(admin_url('/'));
    }
}