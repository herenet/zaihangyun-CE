<?php

namespace App\SaaSAdmin\Forms;

use App\Libs\Helpers;
use App\SaaSAdmin\AppKey;
use Encore\Admin\Widgets\Form;
use App\Models\OrderInterfaceConfig;
use App\SaaSAdmin\Facades\SaaSAdmin;
use App\Models\AlipayConfig as AlipayConfigModel;

class AlipayConfig extends Form
{
    use AppKey;

    public $title = '支付宝配置';

    public function form()
    {
        $this->radio('suport_alipay', '启用支付宝支付')
            ->required()
            ->options([
                0 => '关闭',
                1 => '开启',
            ])
            ->help('支付宝支付需要配置支付宝商户号并在支付宝开放平台添加当前应用，并配置为<b style="color: red;">普通公钥模式</b>，<a href="https://open.alipay.com/develop/manage" target="_blank">添加支付宝应用>>></a>')
            ->when(1, function (Form $form) {
                $form->text('alipay_app_id', '支付宝应用ID')
                    ->rules(['string', 'max:32'])
                    ->help('请输入支付宝应用ID，在支付宝开放平台添加应用后获取。');
                $form->textarea('app_private_cert', '支付宝应用私钥')
                    ->rules(['string', 'max:2048'])
                    ->help('支付宝开放平台中设置接口加签方式后，获取应用私钥。<a href="https://opendocs.alipay.com/common/02kdnc#%E7%AC%AC%E4%B8%89%E6%AD%A5%EF%BC%9A%E8%8E%B7%E5%8F%96%E6%94%AF%E4%BB%98%E5%AE%9D%E5%85%AC%E9%92%A5%2F%E8%AF%81%E4%B9%A6" target="_blank">如何获取>>></a>');
                $form->textarea('alipay_public_cert', '支付宝公钥')
                    ->rules(['string', 'max:1028'])
                    ->help('支付宝公钥，在支付宝开放平台设置接口加签方式后获取。');
                // $form->display('alipay_callback_url', '支付宝授权回调地址')->with(function ($value) {
                //     $params = Helpers::simpleEncode($this->getAppKey().'-'.SaaSAdmin::user()->id);
                //     return config('app.api_url').'/v1/order/callback/alipay/'.$params;
                // })->help('请复制本地址作为支付宝授权回调地址');

                $form->interfaceCheck('alipay_interface_check', '验证配置')
                    ->buttonText('验证配置是否正确')
                    ->dependentOn(['alipay_app_id', 'app_private_cert', 'alipay_public_cert', ])
                    ->default(0)
                    ->testUrl(admin_url('app/manager/'.$this->getAppKey().'/order/config/alipay/check-interface'))
                    ->help('通过调用支付宝接口的方式来验证配置是否正确');

                $this->html('<span class="text-danger"><i class="fa fa-warning"></i> 请在支付宝消息服务中必须订阅 <code>alipay.fund.trans.order.changed</code> 和 <code>alipay.fund.trans.refund.success</code> 消息，否则无法正常更新订单状态。</span>');
            });
        $this->action(admin_url('app/manager/'.$this->getAppKey().'/order/config/alipay'))->method('post');
        $this->disableReset();
    }

    public function data()
    {
        $tenant_id = SaaSAdmin::user()->id;
        $config = app(OrderInterfaceConfig::class)->getConfig($tenant_id, $this->getAppKey());
        $alipay_config = app(AlipayConfigModel::class)->getConfig($tenant_id, $this->getAppKey());
        return[
            'suport_alipay' => $config->suport_alipay ?? 0,
            'alipay_app_id' => $alipay_config->alipay_app_id ?? '',
            'app_private_cert' => $alipay_config->app_private_cert ?? '',
            'alipay_public_cert' => $alipay_config->alipay_public_cert ?? '',
        ];
    }
}