<?php

namespace App\SaaSAdmin\Forms;

use App\SaaSAdmin\AppKey;
use Encore\Admin\Widgets\Form;
use App\SaaSAdmin\Facades\SaaSAdmin;
use App\Models\OrderInterfaceConfig;
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
            $this->html('<span class="text-danger"><i class="fa fa-warning"></i> 启用后，必须至少开启一种支付方式。</span>');
            $this->action(admin_url('app/manager/'.$this->getAppKey().'/order/config/base'))->method('post');
            $this->disableReset();
        });
        
    }

    public function data()
    {
        $tenant_id = SaaSAdmin::user()->id;
        $config = app(OrderInterfaceConfig::class)->getConfig($tenant_id, $this->getAppKey());
        return[
            'switch' => $config->switch ?? 0,
        ];
    }
}