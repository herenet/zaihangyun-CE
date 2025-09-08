<?php

namespace App\Admin\Forms;

use App\Admin\AppKey;
use Encore\Admin\Widgets\Form;
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
        ])->required()->when(1, function (Form $form) {
            $this->html('<span class="text-danger"><i class="fa fa-warning"></i> 启用后，必须至少开启一种支付方式。</span>');
            $this->text('oid_prefix', '订单号前缀')->rules('max:4')
                ->attribute('pattern', '[a-zA-Z0-9]+')
                ->help('订单号前缀，可用于区分不同应用的订单号。最大长度为4个字符，只能包含数字和字母。');
            $this->action(admin_url('app/manager/'.$this->getAppKey().'/order/config/base'))->method('post');
            $this->disableReset();
        });
        
    }

    public function data()
    {
        $config = app(OrderInterfaceConfig::class)->getConfig($this->getAppKey());
        return[
            'switch' => $config->switch ?? 0,
            'oid_prefix' => $config->oid_prefix ?? '',
        ];
    }
}