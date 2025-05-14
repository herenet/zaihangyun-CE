<?php

namespace App\SaaSAdmin\Controllers;

use App\Models\App;
use App\Libs\Helpers;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Row;
use Illuminate\Http\Request;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\InfoBox;
use App\Http\Controllers\Controller;
use App\SaaSAdmin\Facades\SaaSAdmin;

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
            return $value == 1 ? '<i class="fa fa-android"></i>' : ($value == 2 ? '<i class="fa fa-apple"></i>' : '<i class="fa fa-circle-o"></i>');
        });
        $grid->column('name', '应用名称')->display(function ($value) {
            /** @var \App\Models\App $this */
            $url = admin_url('app/manager/' . $this->app_key);
            return "<a href='{$url}' onclick='window.location.href=this.href;return false;'>{$value}</a>";
        });
        $grid->column('app_key', 'AppKey');
        
        $grid->column('user_increate', '新增注册')->sortable();
        $grid->column('order_increate', '新增订单')->sortable();
        $grid->column('income_increate', '新增收入')->sortable();
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
        $content->row(function ($row) {
            $row->column(4, new InfoBox('当日新增注册', 'users', 'aqua', '/demo/users', '1024'));
            $row->column(4, new InfoBox('当日新增订单', 'shopping-cart', 'green', '/demo/orders', '150%'));
            $row->column(4, new InfoBox('当日新增收入', 'money', 'yellow', '/demo/articles', '2786'));
        });

        return $content;
    }

    public function form()
    {
        $form = new Form(new App());
        $form->setTitle('应用信息');
        $form->text('name', '应用名称');
        $form->radio('platform_type', '平台')->options([
            1 => 'Android', 
            2 => 'iOS',
            3 => 'HarmonyOS',
        ])->default(1);

        $form->tools(function (Form\Tools $tools) {
            $tools->disableList();
        });

        $form->setAction(admin_url('apps'));

        $form->footer(function (Form\Footer $footer) {
            $footer->disableViewCheck();
            $footer->disableEditingCheck();
            $footer->disableCreatingCheck();
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