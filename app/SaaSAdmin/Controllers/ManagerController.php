<?php

namespace App\SaaSAdmin\Controllers;

use App\Models\App;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\SaaSAdmin\Actions\DeleteApp;
use App\SaaSAdmin\Facades\SaaSAdmin;
use Encore\Admin\Controllers\AdminController;

class ManagerController extends AdminController
{
    public function index(Content $content)
    {
        return $content
            ->title('应用管理')
            ->body($this->grid());
    }

    protected function grid()
    {
        Admin::style('.box-header { display: none; }');
        $grid = new Grid(new App());
        
        // 重要：只显示当前租户的应用
        $grid->model()->where('tenant_id', SaaSAdmin::user()->id);
        
        $grid->column('', '');
        $grid->column('platform_type', '平台')->display(function ($value) {
            return App::$platformIcons[$value] ?? '';
        })->filter(App::$platformType);
        $grid->column('name', '应用名称');
        $grid->column('app_key', 'AppKey');
        $grid->column('app_secret', 'AppSecret')->password('*');
        
        $grid->column('updated_at', '更新时间');
        $grid->column('created_at', '创建时间');

        $grid->disableBatchActions();
        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->disableFilter();
        $grid->disableRowSelector();
        $grid->disableColumnSelector();

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            // 添加自定义删除按钮
            $actions->add(new DeleteApp());
        });
        return $grid;
    }

    /**
     * 编辑页面 - 添加权限验证
     */
    public function edit($id, Content $content)
    {
        // 验证应用是否属于当前租户
        $app = App::where('app_key', $id)
                 ->where('tenant_id', SaaSAdmin::user()->id)
                 ->first();
                 
        if (!$app) {
            admin_error('错误', '应用不存在或无权限访问');
            return redirect(admin_url('app'));
        }

        return $content
            ->title('编辑应用')
            ->body($this->form()->edit($id));
    }

    /**
     * 更新操作 - 添加权限验证
     */
    public function update($id)
    {
        // 验证应用是否属于当前租户
        $app = App::where('app_key', $id)
                 ->where('tenant_id', SaaSAdmin::user()->id)
                 ->first();
                 
        if (!$app) {
            return response()->json([
                'status' => false,
                'message' => '应用不存在或无权限访问'
            ], 403);
        }

        return $this->form()->update($id);
    }

    /**
     * 显示页面 - 添加权限验证
     */
    public function show($id, Content $content)
    {
        // 验证应用是否属于当前租户
        $app = App::where('app_key', $id)
                 ->where('tenant_id', SaaSAdmin::user()->id)
                 ->first();
                 
        if (!$app) {
            admin_error('错误', '应用不存在或无权限访问');
            return redirect(admin_url('app'));
        }

        return $content
            ->title('应用详情')
            ->body($this->detail($id));
    }

    public function form()
    {
        $form = new Form(new App());
        
        // 添加模型查询限制
        $form->model()->where('tenant_id', SaaSAdmin::user()->id);
        
        $form->text('name', '应用名称');
        $form->text('app_key', 'AppKey')->readonly();
        $form->text('app_secret', 'AppSecret')->readonly();

        $form->saving(function (Form $form) {
            // 再次验证权限
            if ($form->isEditing()) {
                $app = App::where('app_key', $form->model()->app_key)
                         ->where('tenant_id', SaaSAdmin::user()->id)
                         ->first();
                         
                if (!$app) {
                    admin_error('错误', '无权限操作此应用');
                    return redirect(admin_url('app'));
                }
            }
            
            $form->app_key = $form->model()->app_key;
            $form->app_secret = $form->model()->app_secret;
        });

        $form->tools(function(Form\Tools $tools){
            $tools->disableView();
            $tools->disableDelete();
        });

        $form->footer(function(Form\Footer $footer){
            $footer->disableViewCheck();
            $footer->disableEditingCheck();
            $footer->disableCreatingCheck();
            $footer->disableReset();
        });

        return $form;
    }

    public function sendDeleteAppCode(Request $request)
    {
        return DeleteApp::sendDeleteCode($request);
    }

    public function title()
    {
        return '应用管理';
    }

    /**
     * 详情页面
     */
    protected function detail($id)
    {
        $show = new Show(App::where('app_key', $id)->first());
        $show->field('name', '应用名称');
        $show->field('platform_type', '平台')->as(function ($value) {
            return App::$platformType[$value] ?? '';
        });
        $show->field('app_key', 'AppKey');
        $show->field('app_secret', 'AppSecret')->password('*');
        $show->field('created_at', '创建时间');
        $show->field('updated_at', '更新时间');
        $show->panel()
        ->tools(function ($tools) {
            $tools->disableEdit();
            $tools->disableDelete();
        });
        return $show;
    }
}