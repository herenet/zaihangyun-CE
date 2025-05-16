<?php

namespace App\SaaSAdmin\Forms;

use Encore\Admin\Widgets\Form;
use App\SaaSAdmin\AppKey;
use App\SaaSAdmin\Facades\SaaSAdmin;
use App\Models\MessageConfig;

class MessageBaseConfig extends Form
{
    use AppKey;

    public $title = '基础配置';

    public function form()
    {
        $this->radioButton('switch', '是否启用接口')->options([
            1 => '启用',
            0 => '关闭',
        ])->required();
        $this->html('<span class="text-danger"><i class="fa fa-warning"></i> 启用后才能使用用户互动模块相关接口</span>');
        $this->action(admin_url('app/manager/'.$this->getAppKey().'/message/config/base'))->method('post');
        $this->disableReset();
    }

    public function data()
    {
        $tenant_id = SaaSAdmin::user()->id;
        $config = app(MessageConfig::class)->getConfig($this->getAppKey(), $tenant_id);
        return[
            'switch' => $config['switch'] ?? 0,
        ];
    }
}