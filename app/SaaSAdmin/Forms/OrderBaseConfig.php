<?php

namespace App\SaaSAdmin\Forms;

use App\SaaSAdmin\AppKey;
use Encore\Admin\Widgets\Form;
use App\Models\LoginInterfaceConfig;
use App\SaaSAdmin\Facades\SaaSAdmin;

class OrderBaseConfig extends Form
{
    use AppKey;

    public $title = '基础配置';

    public function form()
    {
        $this->radioButton('switch', '是否启用接口')->options([
            1 => '启用',
            0 => '关闭',
        ])->when(1, function (Form $form) {
            $this->html('<span class="text-danger"><i class="fa fa-warning"></i> 启用后，必须至少开启一种登录方式。</span>');
            $this->text('token_effective_duration', 'Token有效时长')
            ->append('天')
            ->required()
            ->default(365)
            ->help('Token的有效时长，单位：天');
            $this->number('endpoint_allow_count', '允许登录设备数')
            ->required()
            ->default(1)
            ->help('同一用户同时最多允许登录设备数');
            $this->action(admin_url('app/manager/'.$this->getAppKey().'/user/config/base'))->method('post');
            $this->disableReset();
        });
        
    }

    public function data()
    {
        $tenant_id = SaaSAdmin::user()->id;
        $config = app(LoginInterfaceConfig::class)->getConfig($tenant_id, $this->getAppKey());
        return[
            'switch' => $config->switch ?? 0,
            'token_effective_duration' => $config->token_effective_duration ?? 365,
            'endpoint_allow_count' => $config->endpoint_allow_count ?? 1,
        ];
    }
}