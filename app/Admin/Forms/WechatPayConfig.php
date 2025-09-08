<?php

namespace App\Admin\Forms;

use App\Admin\AppKey;
use Encore\Admin\Widgets\Form;
use App\Models\WechatPaymentConfig;
use App\Models\OrderInterfaceConfig;
use App\Models\WechatOpenPlatformConfig;

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
                    ->config('allowClear', false)
                    ->config('minimumResultsForSearch', 'Infinity')
                    ->options(function () {
                        return WechatPaymentConfig::where(['interface_check' => 1])->get()->pluck('mch_name', 'id');
                    })
                    ->help('请选择微信商户号，只能选择配置验证已经通过的商户号');

                $form->select('wechat_platform_config_id', '微信开放平台APP')
                    ->config('allowClear', false)
                    ->config('minimumResultsForSearch', 'Infinity')
                    ->options(function () {
                        return WechatOpenPlatformConfig::get()->pluck('app_name', 'id');
                    })
                    ->help('请选择与所选商户号关联的微信开放平台APP');
            });
        $this->action(admin_url('app/manager/'.$this->getAppKey().'/order/config/wechat'))->method('post');
        $this->disableReset();
    }

    public function data()
    {
        $config = app(OrderInterfaceConfig::class)->getConfig($this->getAppKey());
        return[
            'suport_wechat_pay' => $config->suport_wechat_pay ?? 0,
            'wechat_platform_config_id' => $config->wechat_platform_config_id ?? '',
            'wechat_payment_config_id' => $config->wechat_payment_config_id ?? '',
        ];
    }
}