<?php

namespace App\SaaSAdmin\Forms;

use App\Models\User;
use App\SaaSAdmin\AppKey;
use Encore\Admin\Widgets\Form;
use App\Models\LoginInterfaceConfig;
use App\SaaSAdmin\Facades\SaaSAdmin;
use App\Models\WechatOpenPlatformConfig;

class UserInterfaceConfig extends Form
{
    use AppKey;
    public function __handle()
    {
        admin_success('Processed successfully.');

        return back();
    }

    public function form()
    {
        $this->radioButton('switch', '是否启用接口')->options([
            1 => '启用',
            0 => '关闭',
        ])->when(1, function (Form $form) {
            $this->text('token_effective_duration', 'JWT有效时长')
            ->append('天')
            ->required()
            ->default(365)
            ->help('使用JWT作为登录凭证，token的有效时长，单位：天');
            $this->listbox('jwt_payload_fields', 'JWT载荷字段')
                ->options(app(User::class)->fields())
                ->rules('required|array|min:1|max:6')
                ->default(['uid', 'email', 'nickname', 'avatar'])
                ->help('请至少选择1项，最多选择6项；建议payload长度不超过128字符');

            $this->divider('登录方式（请至少启用一种登录方式）');
            $this->radio('suport_wechat_login', '启用微信登录')
            ->options([
                0 => '关闭',
                1 => '开启',
            ])
            ->when(1, function (Form $form) {
                $form->select('selected_wechat_open_platform_id', '关联选择')
                ->options(WechatOpenPlatformConfig::all()->pluck('app_name', 'id'))
                ->help('选择关联微信开放平台的APP');
            });
            $this->radio('suport_mobile_login', '启用手机验证码登录')
            ->options([
                0 => '关闭',
                1 => '开启',
            ])
            ->help('手机验证码登录需要配置短信接口');
            $this->action(admin_url('app/manager/'.$this->getAppKey().'/user/config'))->method('post');
            $this->disableReset();
        });
        
    }

    public function data()
    {
        $tenant_id = SaaSAdmin::user()->id;
        $config = app(LoginInterfaceConfig::class)->getConfig($tenant_id, $this->getAppKey());
        return[
            'switch' => $config->switch,
            'token_effective_duration' => $config->token_effective_duration,
            'jwt_payload_fields' => json_decode($config->jwt_payload_fields, true),
            'suport_wechat_login' => $config->suport_wechat_login,
            'selected_wechat_open_platform_id' => $config->selected_wechat_open_platform_id,
            'suport_mobile_login' => $config->suport_mobile_login,
        ];
    }
}