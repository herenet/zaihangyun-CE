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
            ->help('微信登录需要配置微信开放平台，登录注册功能一体')
            ->when(1, function (Form $form) {
                $form->text('app_name', 'APP名称')
                    ->help('请输入微信开放平台APP名称，以便后续辨别');
                    $form->text('wechat_appid', '微信APPID')
                    ->help('请输入微信开放平台APPID');
                $form->text('wechat_appsecret', '微信APPSECRET')
                    ->help('请输入微信开放平台APPSECRET');
                $form->interfaceCheck('wechat_login_interface_check', '验证配置')
                    ->buttonText('测试配置是否正确')
                    ->dependentOn(['wechat_appid', 'wechat_appsecret'])
                    ->default(0)
                    ->testUrl(admin_url('app/manager/' . $this->getAppKey() . '/config/wechat/platform/check-interface'))
                    ->help('通过从微信开放平台获取AccessToken的方式来验证配置是否正确');
            });
        $this->action(admin_url('app/manager/'.$this->getAppKey().'/user/config/wechat'))->method('post');
        $this->disableReset();
    }

    public function data()
    {
        $tenant_id = SaaSAdmin::user()->id;
        $config = app(LoginInterfaceConfig::class)->getConfig($tenant_id, $this->getAppKey());
        $wechat_open_platform_config = app(WechatOpenPlatformConfig::class)->getConfig($tenant_id, $this->getAppKey());

        return[
            'suport_wechat_login' => $config->suport_wechat_login ?? 0,
            'app_name' => $wechat_open_platform_config->app_name ?? '',
            'wechat_appid' => $wechat_open_platform_config->wechat_appid ?? '',
            'wechat_appsecret' => $wechat_open_platform_config->wechat_appsecret ?? '',
        ];
    }
}