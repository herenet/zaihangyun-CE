<?php

namespace App\SaaSAdmin\Forms;

use App\Models\App;
use App\SaaSAdmin\AppKey;
use Encore\Admin\Widgets\Form;
use App\SaaSAdmin\Facades\SaaSAdmin;
use App\Models\OrderInterfaceConfig;
use App\Models\AppleVerifyConfig as AppleVerifyConfigModel;

class AppleVerifyConfig extends Form
{
    use AppKey;

    /**
     * The form title.
     *
     * @var string
     */
    public $title = '苹果票据验证配置';

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->radio('suport_apple_verify', '启用苹果票据验证')
            ->required()
            ->options([
                0 => '关闭',
                1 => '开启',
            ])
            ->help('启用后允许客户端验证苹果购买票据')
            ->when(1, function (Form $form) {
                $form->text('bundle_id', '应用包名(Bundle ID)')
                    ->rules('required_if:suport_apple_verify,1|string|max:128')
                    ->help('苹果应用的Bundle ID，用于验证苹果票据是否有效，请正确填写。');

                $form->switch('multiple_verify', '是否允许重复验证')
                    ->states([
                        'on' => ['value' => 1, 'text' => '允许'],
                        'off' => ['value' => 0, 'text' => '不允许'],
                    ])
                    ->default(0)
                    ->help('允许：同一个票据可以多次验证；不允许：同一个票据只能验证一次');

                $form->radioButton('subscrip_switch', '订阅功能')
                    ->options([
                        0 => '关闭',
                        1 => '开启',
                    ])
                    ->required()
                    ->default(0)
                    ->help('如在App Store Connect中配置了订阅，请选择开启订阅功能。并配置好以下共享密钥及回调地址。')
                    ->when(1, function (Form $form) {
                        $form->text('shared_secret', '共享密钥')
                            ->rules(['string', 'max:255'])
                            ->help('请输入共享密钥，在App Store Connect > App 信息 > App 专用共享密码，生成后获取。');

                        $form->iapSingleCheck('interface_check', '验证配置')
                            ->buttonText('验证配置是否正确')
                            ->dependentOn(['bundle_id', 'shared_secret'])
                            ->modalFieldName('支付凭证（必须是订阅购买的票据）')
                            ->default(0)
                            ->testUrl(admin_url('app/manager/'.$this->getAppKey().'/order/config/apple/verify-subscription'))
                            ->help('通过调用苹果IAP接口的方式来验证配置是否正确');
                    });
            });

        

        $this->html('<div class="form-group">
            <label class="col-sm-2 control-label"></label>
            <div class="col-sm-12">
                <div class="alert alert-info">
                    <i class="icon fa fa-info-circle"></i>
                    <strong>提示：</strong>
                    <span>该功能仅验证苹果票据是否有效，不与本系统中订单进行关联。用户无需登录即可验证票据。详细说明请参考</span> <a style="color: orange;" href="/docs/1.x/apis/order_list" target="_blank">接口文档>>></a>
                </div>
            </div>
        </div>');

        $this->action(admin_url('app/manager/'.$this->getAppKey().'/order/config/apple-verify'))->method('post');
        $this->disableReset();
    }

    /**
     * The data of the form.
     *
     * @return array $data
     */
    public function data()
    {
        $tenant_id = SaaSAdmin::user()->id;
        $app_key = $this->getAppKey();

        // 获取订单接口配置
        $order_config = app(OrderInterfaceConfig::class)->getConfig($tenant_id, $app_key);
        $suport_apple_verify = $order_config->suport_apple_verify ?? 0;

        // 获取苹果票据验证配置
        $apple_verify_config = app(AppleVerifyConfigModel::class)->getConfig($tenant_id, $app_key);
        $bundle_id = $apple_verify_config->bundle_id ?? '';
        $multiple_verify = $apple_verify_config->multiple_verify ?? 0;
        $subscrip_switch = $apple_verify_config->subscrip_switch ?? 0;
        $shared_secret = $apple_verify_config->shared_secret ?? '';
        
        return [
            'suport_apple_verify' => $suport_apple_verify,
            'bundle_id' => $bundle_id,
            'multiple_verify' => $multiple_verify,
            'subscrip_switch' => $subscrip_switch,
            'shared_secret' => $shared_secret,
        ];
    }
} 