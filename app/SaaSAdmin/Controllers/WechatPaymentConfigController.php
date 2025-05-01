<?php

namespace App\SaaSAdmin\Controllers;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use App\SaaSAdmin\AppKey;
use Encore\Admin\Layout\Content;
use App\Models\WechatPaymentConfig;
use App\SaaSAdmin\Facades\SaaSAdmin;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Encore\Admin\Controllers\AdminController;
use App\SaaSAdmin\Actions\WechatPayInterfaceCheck;
use App\SaaSAdmin\Actions\DownloadWechatPlatformCert;

class WechatPaymentConfigController extends AdminController
{
    use AppKey;

    public function index(Content $content)
    {
        return $content
        ->title('微信商户号配置')
        ->body($this->grid());
    }

    public function grid()
    {
        $grid = new Grid(new WechatPaymentConfig());
        $grid->model()
            ->where('tenant_id', SaaSAdmin::user()->id)
            ->orderBy('id', 'desc');
        $grid->fixColumns(3, -3);
        $grid->column('id', 'ID')->sortable();
        $grid->column('mch_name', '商户名称');
        $grid->column('mch_id', '商户ID')->copyable();
        $grid->column('mch_cert_serial', 'API证书序列号')->copyable();
        $grid->column('mch_api_v3_secret', 'APIv3密钥')->password('*', 6)->copyable();
        $grid->column('mch_private_key_path', '商户私钥')->downloadable();
        $grid->column('mch_platform_cert_path', '平台证书')->downloadable();
        $grid->column('remark', '备注')->limit(20);
        $grid->column('updated_at', '更新时间')->sortable();
        $grid->column('created_at', '创建时间')->sortable();
        $grid->column('interface_check', '配置验证')->action(WechatPayInterfaceCheck::class);

        $grid->actions(function ($actions) {
            $actions->disableView();
        });
        
        $grid->disableExport();
        $grid->disableRowSelector();
        $grid->disableColumnSelector();
        
        return $grid;
    }

    public function title()
    {
        return '微信商户号配置';
    }

    public function edit($id, Content $content)
    {
        $this->clearAPICache($id);
        return parent::edit($id, $content);
    }

    public function update($id)
    {
        $this->clearAPICache($id);
        return parent::update($id);
    }

    public function destroy($id)
    {
        $this->clearAPICache($id);
        return parent::destroy($id);
    }

    public function form()
    {
        $form = new Form(new WechatPaymentConfig());
        $form->html('<div class="alert alert-info">请确保微信开放平台APP已与商户号完成关联绑定。</div>');
        $form->text('mch_name', '商户名称')
            ->rules(['required', 'string', 'max:64'])
            ->help('可填写公司主体名称');
        $form->text('mch_id', '商户ID')
            ->rules(['required', 'string', 'max:128'])
            ->help('请输入微信商户ID，<a href="https://pay.weixin.qq.com/index.php/core/account/info" target="_blank">查看商户号>>></a>');
        $form->text('mch_cert_serial', 'API证书序列号')
            ->rules(['required', 'string', 'max:128'])
            ->help('请输入微信商户API证书的序列号，<a href="https://pay.weixin.qq.com/index.php/core/cert/api_cert#/api-cert-manage" target="_blank">查看证书序列号>>></a>');
        $form->text('mch_api_v3_secret', 'APIv3密钥')
            ->rules(['required', 'string', 'max:128'])
            ->help('请输入微信解密回调APIv3密钥，<a href="https://kf.qq.com/faq/180830E36vyQ180830AZFZvu.html" target="_blank">查看如何获取>>></a>');
        $form->file('mch_private_key_path', '商户私钥')
            ->rules(['required', 'file', 'max:512', 'mimes:pem,txt'])
            ->move(SaaSAdmin::user()->id, function ($file) use ($form) {
                $mch_id = $form->model()->mch_id ?? $form->input('mch_id');
                return 'mch_private_key_'.$mch_id.'.'.$file->getClientOriginalExtension();
            })
            ->disk('SaaSAdmin-mch')
            ->help('请上传微信商户API证书私钥，<a href="https://kf.qq.com/faq/161222NneAJf161222U7fARv.html" target="_blank">查看如何获取>>></a>');
        $form->file('mch_platform_cert_path', '平台证书')
            ->rules(['required', 'file', 'max:512', 'mimes:pem,txt'])
            ->move(SaaSAdmin::user()->id, function ($file) use ($form) {
                $mch_id = $form->model()->mch_id ?? $form->input('mch_id');
                return 'mch_platform_cert_'.$mch_id.'.'.$file->getClientOriginalExtension();
            })
            ->disk('SaaSAdmin-mch')
            ->help('请上传微信支付平台证书，<a href="https://pay.weixin.qq.com/doc/v3/merchant/4012068829" target="_blank">查看如何获取>>></a>');
        $form->text('remark', '备注')
            ->rules(['nullable', 'string', 'max:128']);
        

        $form->tools(function ($tools) {
            $tools->disableDelete();
            $tools->disableView();
        });

        $form->saving(function (Form $form) {
            $form->model()->interface_check = 0;
            $form->model()->tenant_id = SaaSAdmin::user()->id;
        });

        $form->html('<span class="text-danger"><i class="fa fa-warning"></i> 添加成功后，请在列表页点击“验证配置”按钮进行验证，验证通过后，方可使用接口。</span>');

        $form->footer(function ($footer) {
            $footer->disableViewCheck();
            $footer->disableEditingCheck();
            $footer->disableCreatingCheck();
            $footer->disableReset();
        });

        return $form;
    }

    public function checkCallback(Request $request)
    {
        dd($request->all());
    }

    protected function clearAPICache($id)
    {
        $cache_key = 'wechat_payment_config|'.$id;
        Cache::store('api_cache')->forget($cache_key);
    }
}