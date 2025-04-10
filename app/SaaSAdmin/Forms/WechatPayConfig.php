<?php

namespace App\SaaSAdmin\Forms;

use App\SaaSAdmin\AppKey;
use Encore\Admin\Widgets\Form;
use App\SaaSAdmin\Facades\SaaSAdmin;

class WechatPayConfig extends Form
{
    use AppKey;

    public $title = '微信支付配置';

    public function form()
    {
        $this->html('<div class="alert alert-info">请确保微信开放平台APP已与商户号完成关联绑定。</div>');
        $this->text('mch_name', '商户名称')
            ->rules(['required', 'string', 'max:64'])
            ->help('可填写公司主体名称');
        $this->text('mch_id', '商户ID')
            ->rules(['required', 'string', 'max:128'])
            ->help('请输入微信商户ID，<a href="https://pay.weixin.qq.com/index.php/core/account/info" target="_blank">查看商户号>>></a>');
        $this->text('mch_cert_serial', 'API证书序列号')
            ->rules(['required', 'string', 'max:128'])
            ->help('请输入微信商户API证书的序列号，<a href="https://pay.weixin.qq.com/index.php/core/cert/api_cert#/api-cert-manage" target="_blank">查看证书序列号>>></a>');
        $this->text('mch_api_v3_secret', 'APIv3密钥')
            ->rules(['required', 'string', 'max:128'])
            ->help('请输入微信解密回调APIv3密钥，<a href="https://kf.qq.com/faq/180830E36vyQ180830AZFZvu.html" target="_blank">查看如何获取>>></a>');
        $this->file('mch_private_key_path', '商户私钥')
            ->rules(['required', 'file', 'max:512', 'mimes:pem,txt'])
            ->move(SaaSAdmin::user()->id, function ($file) {
                $mch_id = $this->model()->mch_id ?? $this->input('mch_id');
                return 'mch_private_key_'.$mch_id.'.'.$file->getClientOriginalExtension();
            })
            ->disk('SaaSAdmin-mch')
            ->help('请上传微信商户API证书私钥，<a href="https://kf.qq.com/faq/161222NneAJf161222U7fARv.html" target="_blank">查看如何获取>>></a>');
            $this->file('mch_platform_cert_path', '平台证书')
            ->rules(['required', 'file', 'max:512', 'mimes:pem,txt'])
            ->move(SaaSAdmin::user()->id, function ($file) {
                $mch_id = $this->model()->mch_id ?? $this->input('mch_id');
                return 'mch_platform_cert_'.$mch_id.'.'.$file->getClientOriginalExtension();
            })
            ->disk('SaaSAdmin-mch')
            ->help('请上传微信支付平台证书，<a href="https://pay.weixin.qq.com/doc/v3/merchant/4012068829" target="_blank">查看如何获取>>></a>');
        $this->text('remark', '备注')
            ->rules(['nullable', 'string', 'max:128']);
        
        $this->html('<span class="text-danger"><i class="fa fa-warning"></i> 添加成功后，请在列表页点击“验证配置”按钮进行验证，验证通过后，方可使用接口。</span>');

        $this->footer(function ($footer) {
            $footer->disableViewCheck();
            $footer->disableEditingCheck();
            $footer->disableCreatingCheck();
            $footer->disableReset();
        });
    }

    public function data()
    {
        // $tenant_id = SaaSAdmin::user()->id;
        // $config = app(LoginInterfaceConfig::class)->getConfig($tenant_id, $this->getAppKey());
        // return[
        //     'switch' => $config->switch,
        //     'token_effective_duration' => $config->token_effective_duration,
        //     'endpoint_allow_count' => $config->endpoint_allow_count,
        // ];
    }
}