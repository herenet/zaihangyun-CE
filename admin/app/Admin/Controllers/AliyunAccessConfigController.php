<?php

namespace App\Admin\Controllers;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Encore\Admin\Layout\Content;
use App\Models\AliyunAccessConfig;
use Encore\Admin\Controllers\AdminController;
use AlibabaCloud\Client\AlibabaCloud;

class AliyunAccessConfigController extends AdminController
{
    public function index(Content $content)
    {
        return $content
        ->title('阿里云AccessKey配置')
        ->body($this->grid());
    }

    public function grid()
    {
        $grid = new Grid(new AliyunAccessConfig());
        $grid->column('id', 'ID')->hide();
        $grid->column('name', '名称');
        $grid->column('access_key', 'AccessKey')->copyable();
        $grid->column('access_key_secret', 'AccessKeySecret')->password('*', 6)->copyable();
        $grid->column('interface_check', '接口验证')->using([0 => '未验证', 1 => '已验证'])->label(['success' => '已验证', 'danger' => '未验证']);
        $grid->column('remark', '备注');
        $grid->column('updated_at', '更新时间')->sortable();
        $grid->column('created_at', '创建时间')->sortable();

        $grid->actions(function ($actions) {
            $actions->disableView();
        });

        $grid->disableExport();
        $grid->disableRowSelector();
        $grid->disableColumnSelector();
        $grid->disableFilter();
        
        return $grid;
    }

    public function form()
    {
        $form = new Form(new AliyunAccessConfig());
        $form->text('name', '名称')
            ->rules(['required', 'string', 'max:64'])
            ->help('输入名称以便区分不同阿里云AccessKey主体');
        $form->text('access_key', 'AccessKey')
            ->rules(['required', 'string', 'max:64'])
            ->help('阿里云AccessKey获取地址：<a href="https://ram.console.aliyun.com/profile/access-keys" target="_blank">点击获取>>></a>');
        $form->text('access_key_secret', 'AccessKeySecret')
            ->rules(['required', 'string', 'max:64'])
            ->help('阿里云AccessKeySecret获取地址：<a href="https://ram.console.aliyun.com/profile/access-keys" target="_blank">点击获取>>></a>');
        $form->textarea('remark', '备注')
            ->rules(['nullable', 'string', 'max:255']);

        $form->interfaceCheck('interface_check', '接口验证')
        ->buttonText('测试配置是否正确')
        ->dependentOn(['access_key', 'access_key_secret'])
        ->default(0)
        ->testUrl(admin_url('global/config/aliyun/access/check-interface'))
        ->help('通过从阿里云获取DescribeRegions的方式来验证配置是否正确');

        $form->tools(function ($tools) {
            $tools->disableDelete();
            $tools->disableView();
        });

        $form->saving(function (Form $form) {
            $interface_check = $form->input('interface_check');
            if ($interface_check == 0) {
                admin_error('请先验证配置是否正确');
                return back()->withInput();
            }
        });

        $form->footer(function ($footer) {
            $footer->disableViewCheck();
            $footer->disableEditingCheck();
            $footer->disableCreatingCheck();
            $footer->disableReset();
        });

        return $form;
    }

    public function title()
    {
        return '阿里云AccessKey配置';
    }

    public function detail($id)
    {
        $show = new Show(AliyunAccessConfig::find($id));
        $show->id('ID');
        $show->name('名称');
        $show->access_key('AccessKey');
        $show->access_key_secret('AccessKeySecret');
        $show->remark('备注');
        $show->interface_check('接口验证')->using([0 => '未验证', 1 => '已验证'])->label(['success' => '已验证', 'danger' => '未验证']);
        $show->updated_at('更新时间');
        $show->created_at('创建时间');
        $show->panel()
        ->tools(function ($tools) {
            $tools->disableEdit();
            $tools->disableDelete();
        });
        return $show;
    }

    public function checkInterface(Request $request)
    {
        $access_key = $request->input('access_key');
        $access_key_secret = $request->input('access_key_secret');
        
        if (empty($access_key) || empty($access_key_secret)) {
            return response()->json(['status' => false, 'message' => 'AccessKey和AccessKeySecret不能为空']);
        }

        try {
            // 使用新的方式创建客户端
            AlibabaCloud::accessKeyClient($access_key, $access_key_secret)
                ->regionId('cn-hangzhou')
                ->asDefaultClient();

            // 测试调用一个简单的 API
            $result = AlibabaCloud::rpc()
                ->product('Ecs')
                ->version('2014-05-26')
                ->action('DescribeRegions')
                ->method('POST')
                ->request();
            
            return response()->json([
                'status' => true,
                'message' => '配置正确',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}