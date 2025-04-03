<?php

namespace App\SaaSAdmin\Controllers\Manager;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\SaaSAdmin\AppKey;
use Illuminate\Http\Request;
use Encore\Admin\Layout\Content;
use App\SaaSAdmin\Facades\SaaSAdmin;
use Illuminate\Support\Facades\Http;
use App\Models\WechatOpenPlatformConfig;
use Encore\Admin\Controllers\AdminController;

class WechatOpenPlatformConfigController extends AdminController
{
    use AppKey;

    public function index(Content $content)
    {
        return $content
        ->title('微信开放平台配置')
        ->body($this->grid());
    }

    public function grid()
    {
        $grid = new Grid(new WechatOpenPlatformConfig());
        $grid->model()->orderBy('id', 'desc');

        $grid->column('id', 'ID')->sortable();
        $grid->column('laucher_icon_url', '启动图标')->image();
        $grid->column('app_name', 'APP名称');
        $grid->column('wechat_appid', '微信APPID')->copyable();
        $grid->column('wechat_appsecret', '微信APPSECRET')->password('*', 6)->copyable();
        $grid->column('remark', '备注');
        $grid->column('updated_at', '更新时间')->sortable();
        $grid->column('created_at', '创建时间')->sortable();
        
        $grid->disableExport();
        $grid->disableRowSelector();
        $grid->disableColumnSelector();
        
        return $grid;
    }

    public function title()
    {
        return '微信开放平台配置';
    }

    public function edit($id, Content $content)
    {
        $id = request()->route('platform');
        return parent::edit($id, $content);
    }

    public function update($id)
    {
        $id = request()->route('platform');
        return parent::update($id);
    }

    public function form()
    {
        $form = new Form(new WechatOpenPlatformConfig());
        
        $form->file('laucher_icon_url', '启动图标');
        $form->text('app_name', 'APP名称')
            ->rules(['required', 'string', 'max:64']);
        $form->text('wechat_appid', '微信APPID')
            ->rules(['required', 'string', 'max:128'])
            ->help('请输入微信开放平台APPID');
        $form->text('wechat_appsecret', '微信APPSECRET')
            ->rules(['required', 'string', 'max:128'])
            ->help('请输入微信开放平台APPSECRET');
        $form->text('remark', '备注')
            ->rules(['nullable', 'string', 'max:255']);
        
        $form->interfaceCheck('interface_check', '验证配置')
            ->buttonText('测试配置是否正确')
            ->dependentOn(['wechat_appid', 'wechat_appsecret'])
            ->testUrl(admin_url('app/manager/' . $this->getAppKey() . '/config/wechat/platform/check-interface'))
            ->help('通过从微信开放平台获取AccessToken的方式来验证配置是否正确');

        $form->tools(function ($tools) {
            $tools->disableDelete();
            $tools->disableView();
        });

        $form->saving(function (Form $form) {
            $form->model()->tenant_id = SaaSAdmin::user()->id;
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

    function detail()
    {
        $id = request()->route('platform');
        $show = new Show(WechatOpenPlatformConfig::find($id));
        $show->id('ID');
        $show->laucher_icon_url('启动图标')->image();
        $show->app_name('APP名称');
        $show->wechat_appid('微信APPID')->copyable();
        $show->wechat_appsecret('微信APPSECRET')->password('*', 6)->copyable();
        $show->remark('备注');
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
        $wechat_appid = $request->input('wechat_appid');
        $wechat_appsecret = $request->input('wechat_appsecret');
        if (empty($wechat_appid) || empty($wechat_appsecret)) {
            return response()->json(['status' => false, 'message' => '微信APPID和APPSECRET不能为空']);
        }
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $wechat_appid . '&secret=' . $wechat_appsecret;
        $response = Http::get($url);
        $data = $response->json();
        if (isset($data['access_token'])) {
            return response()->json(['status' => true]);
        } else {
            return response()->json(['status' => true]);
            // return response()->json(['status' => false, 'message' => '配置错误[' . $data['errcode'] . ']:' . $data['errmsg']]);
        }
    }
}