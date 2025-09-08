<?php

namespace App\Admin\Forms;

use App\Admin\AppKey;
use Encore\Admin\Widgets\Form;
use App\Models\LoginInterfaceConfig;

class HuaweiLoginConfig extends Form
{
    use AppKey;

    public $title = '华为登录配置';

    public function form()
    {
        $this->radio('suport_huawei_login', '启用华为登录')
            ->required()
            ->options([
                0 => '关闭',
                1 => '开启',
            ])
            ->help('华为登录需要配置华为AppGallery Connect信息，登录注册功能一体。')
            ->when(1, function (Form $form) {
                $form->text('huawei_oauth_client_id', 'Client ID')
                    ->help('请输入华为AppGallery Connect信息Client ID');
                $form->text('huawei_oauth_client_secret', 'Client Secret')
                    ->help('请输入华为AppGallery Connect信息Client Secret');
                $form->interfaceCheck('interface_check', '验证配置')
                    ->buttonText('测试配置是否正确')
                    ->dependentOn(['huawei_oauth_client_id', 'huawei_oauth_client_secret'])
                    ->default(0)
                    ->testUrl(admin_url('app/manager/'.$this->getAppKey().'/user/config/huawei/check-interface'))
                    ->help('通过从华为AppGallery Connect信息获取AccessToken的方式来验证配置是否正确');

                $this->html('<span class="text-danger"><i class="fa fa-warning"></i> 请在华为的AppGallery Connect中添加当前应用，获取OAuth2.0客户端的Client ID和Client Secret。马上配置<a href="https://developer.huawei.com/consumer/cn/service/josp/agc/index.html#/myProject" target="_blank">点击查看</a></span>');
            });
        $this->action(admin_url('app/manager/'.$this->getAppKey().'/user/config/huawei'))->method('post');
        $this->disableReset();
    }

    public function data()
    {
        $config = app(LoginInterfaceConfig::class)->getConfig($this->getAppKey());

        return[
            'suport_huawei_login' => $config->suport_huawei_login ?? 0,
            'huawei_oauth_client_id' => $config->huawei_oauth_client_id ?? '',
            'huawei_oauth_client_secret' => $config->huawei_oauth_client_secret ?? '',
        ];
    }
}