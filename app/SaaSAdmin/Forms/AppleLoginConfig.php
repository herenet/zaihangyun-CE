<?php

namespace App\SaaSAdmin\Forms;

use App\SaaSAdmin\AppKey;
use Encore\Admin\Widgets\Form;
use App\Models\LoginInterfaceConfig;
use App\SaaSAdmin\Facades\SaaSAdmin;

class AppleLoginConfig extends Form
{
    use AppKey;

    public $title = '苹果登录配置';

    public function form()
    {
        $this->radioButton('suport_apple_login', '启用苹果登录')->options([
            1 => '启用',
            0 => '关闭',
        ])->required()->when(1, function (Form $form) {
            $this->html('<span class="text-danger"><i class="fa fa-warning"></i> 启用后才能使用苹果登录，同时需要配置苹果开发者账号：<a href="https://developer.apple.com/account/resources/identifiers" target="_blank">配置苹果证书>>></a></span>');
            $this->text('apple_nickname_prefix', '苹果昵称前缀')
                ->rules('max:8')
                ->attribute('pattern', '[a-zA-Z0-9]+')
                ->help('在获取不到苹果昵称的情况下，默认使用昵称前缀拼接随机数，留空则使用UID作为昵称。最大长度为8个字符，只能包含数字和字母');

            $this->action(admin_url('app/manager/'.$this->getAppKey().'/user/config/apple'))->method('post');
            $this->disableReset();
        });
    }

    public function data()
    {
        $tenant_id = SaaSAdmin::user()->id;
        $config = app(LoginInterfaceConfig::class)->getConfig($tenant_id, $this->getAppKey());
        return [
            'suport_apple_login' => $config['suport_apple_login'] ?? 0,
            'apple_nickname_prefix' => $config['apple_nickname_prefix'] ?? '',
        ];
    }
}

