<?php

namespace App\SaaSAdmin\Forms;

use App\SaaSAdmin\AppKey;
use Encore\Admin\Widgets\Form;
use App\Models\WechatPaymentConfig;
use App\Models\OrderInterfaceConfig;
use App\SaaSAdmin\Facades\SaaSAdmin;

class WechatPayConfig extends Form
{
    use AppKey;

    public $title = '微信支付配置';

    public function form()
    {
        $this->radio('suport_wechat_pay', '启用微信支付')
            ->required()
            ->options([
                0 => '关闭',
                1 => '开启',
            ])
            ->help('微信支付需要配置微信商户号，<a href="'.admin_url('global/config/wechat/payment').'">添加微信商户号>>></a>')
            ->when(1, function (Form $form) {
                $form->select('wechat_payment_config_id', '微信商户号')
                    ->required()
                    ->config('allowClear', false)
                    ->config('minimumResultsForSearch', 'Infinity')
                    ->options(function () {
                        return WechatPaymentConfig::where(['tenant_id' => SaaSAdmin::user()->id, 'interface_check' => 1])->get()->pluck('mch_name', 'id');
                    })
                    ->help('请选择微信商户号，只能选择配置验证已经通过的商户号');
            });
        $this->action(admin_url('app/manager/'.$this->getAppKey().'/order/config/wechat'))->method('post');
        $this->disableReset();
    }

    public function data()
    {
        $tenant_id = SaaSAdmin::user()->id;
        $config = app(OrderInterfaceConfig::class)->getConfig($tenant_id, $this->getAppKey());
        return[
            'suport_wechat_pay' => $config->suport_wechat_pay ?? 0,
            'wechat_payment_config_id' => $config->wechat_payment_config_id ?? '',
        ];
    }
}