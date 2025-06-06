<?php

namespace App\SaaSAdmin\Controllers;

use App\Models\App;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\SaaSAdmin\Actions\DeleteApp;
use Illuminate\Http\Request;
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

    public function form()
    {
        $form = new Form(new App());
        $form->text('name', '应用名称');
        $form->text('app_key', 'AppKey')->readonly();
        $form->text('app_secret', 'AppSecret')->readonly();

        $form->saving(function (Form $form) {
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

    public function detail($id)
    {
        $app = App::find($id);
        $show = new Show($app);
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