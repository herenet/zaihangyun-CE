<?php

namespace App\SaaSAdmin\Forms;

use App\SaaSAdmin\AppKey;
use Encore\Admin\Widgets\Form;
use App\Models\LoginInterfaceConfig;
use App\SaaSAdmin\Facades\SaaSAdmin;
use App\Models\WechatOpenPlatformConfig;

class WechatLoginConfig extends Form
{
    use AppKey;

    public $title = '微信登录配置';

    public function form()
    {
        $this->radio('suport_wechat_login', '启用微信登录')
            ->required()
            ->options([
                0 => '关闭',
                1 => '开启',
            ])
            ->help('微信登录需要配置微信开放平台，登录注册功能一体。')
            ->when(1, function (Form $form) {
                $form->select('wechat_platform_config_id', '微信开放平台')
                    ->required()
                    ->config('allowClear', false)
                    ->config('minimumResultsForSearch', 'Infinity')
                    ->options(function () {
                        return WechatOpenPlatformConfig::where(['tenant_id' => SaaSAdmin::user()->id, 'interface_check' => 1])->get()->pluck('app_name', 'id');
                    })
                    ->help('请选择当前应用所属的微信开放平台，如未配置请先<a href="'.admin_url('global/config/wechat/platform').'">添加微信开放平台>>></a>');
            });
        $this->action(admin_url('app/manager/'.$this->getAppKey().'/user/config/wechat'))->method('post');
        $this->disableReset();
    }

    public function data()
    {
        $tenant_id = SaaSAdmin::user()->id;
        $config = app(LoginInterfaceConfig::class)->getConfig($tenant_id, $this->getAppKey());

        return[
            'suport_wechat_login' => $config->suport_wechat_login ?? 0,
            'wechat_platform_config_id' => $config->wechat_platform_config_id ?? '',
        ];
    }
}