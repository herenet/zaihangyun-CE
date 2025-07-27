<?php

namespace App\SaaSAdmin\Forms;

use App\Models\AliyunAccessConfig;
use App\SaaSAdmin\AppKey;
use Encore\Admin\Widgets\Form;
use App\Models\LoginInterfaceConfig;
use App\SaaSAdmin\Facades\SaaSAdmin;

class SmsLoginConfig extends Form
{
    use AppKey;

    public $title = '短信登录配置';

    public function form()
    {
        $this->radio('suport_mobile_login', '启用短信登录')
            ->required()
            ->options([
                0 => '关闭',
                1 => '开启',
            ])
            ->help('短信登录需要配置阿里云AccessKey，<a href="'.admin_url('global/config/aliyun/access').'">添加阿里云AccessKey>>></a>')
            ->when(1, function (Form $form) {
                $form->select('aliyun_access_config_id', '阿里云AccessKey')
                    ->config('allowClear', false)
                    ->config('minimumResultsForSearch', 'Infinity')
                    ->options(function () {
                        return AliyunAccessConfig::where(['tenant_id' => SaaSAdmin::user()->id, 'interface_check' => 1])->get()->pluck('name', 'id');
                    });
                $form->text('aliyun_sms_sign_name', '短信签名');
                $form->text('aliyun_sms_tmp_code', '模板Code')->help('请设置您短信模板变量名为"code"，模板内容请兼容手机号修改或绑定的场景，<a href="https://dysms.console.aliyun.com/domestic/text/template/add" target="_blank">添加阿里云短信模板>>>></a>');
                $form->number('aliyun_sms_verify_code_expire', '验证码有效期')->default(5)->max(60)->min(1)->help('单位：分钟，默认5分钟，最小1分钟，最大60分钟');
                $form->aliyunSmsCheck('aliyun_sms_check', '验证配置')
                    ->buttonText('测试配置是否正确')
                    ->dependentOn(['aliyun_access_config_id', 'aliyun_sms_sign_name', 'aliyun_sms_tmp_code'])
                    ->default(0)
                    ->testUrl(admin_url('app/manager/'.$this->getAppKey().'/user/config/sms/check-interface'))
                    ->help('通过调用阿里云短信接口的方式来验证配置是否正确');
            });
        $this->action(admin_url('app/manager/'.$this->getAppKey().'/user/config/sms'))->method('post');
        $this->disableReset();
    }

    public function data()
    {
        $tenant_id = SaaSAdmin::user()->id;
        $config = app(LoginInterfaceConfig::class)->getConfig($tenant_id, $this->getAppKey());

        return[
            'suport_mobile_login' => $config->suport_mobile_login ?? 0,
            'aliyun_access_config_id' => $config->aliyun_access_config_id ?? '',
            'aliyun_sms_sign_name' => $config->aliyun_sms_sign_name ?? '',
            'aliyun_sms_tmp_code' => $config->aliyun_sms_tmp_code ?? '',
            'aliyun_sms_verify_code_expire' => $config->aliyun_sms_verify_code_expire ?? 5,
        ];
    }
}