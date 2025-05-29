<?php

namespace App\SaaSAdmin\Forms;

use App\Libs\Helpers;
use Encore\Admin\Admin;
use App\SaaSAdmin\AppKey;
use Encore\Admin\Widgets\Form;
use App\Models\AppleDevS2SConfig;
use App\Models\OrderInterfaceConfig;
use App\SaaSAdmin\Facades\SaaSAdmin;
use App\Models\IAPConfig as IAPConfigModel;

class IAPConfig extends Form
{
    use AppKey;

    public $title = '苹果IAP配置';

    public function form()
    {
        $this->radio('suport_apple_pay', '启用苹果IAP购买')
            ->required()
            ->options([
                0 => '关闭',
                1 => '开启',
            ])
            ->help('苹果IAP购买功能包含：App内购买和订阅，请根据IAP的配置情况填下以下配置。')
            ->when(1, function (Form $form) {
                $form->text('bundle_id', '应用包名（Bundle ID）')
                    ->required()
                    ->rules(['required', 'string', 'max:128'])
                    ->help('请输入当前应用对应的苹果应用包名，在苹果App Store Connect后台App信息中可以找到，例如：com.example.app。');
                $form->text('app_apple_id', '苹果应用ID（App ID）')
                    ->required()
                    ->rules(['required', 'integer'])
                    ->help('请输入当前应用对应的AppleID，在苹果App Store Connect后台App信息中可以找到，例如：123456789。');

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

                        $form->select('apple_dev_s2s_config_id', '苹果服务端API证书')
                            ->config('allowClear', false)
                            ->config('minimumResultsForSearch', 'Infinity')
                            ->options(function () {
                                return AppleDevS2SConfig::where(['tenant_id' => SaaSAdmin::user()->id, 'interface_check' => 1])->get()->pluck('dev_account_name', 'id');
                            })
                            ->help('请选择当前应用所属的苹果开发者账号对应的苹果服务端API证书，如未配置请先<a href="'.admin_url('global/config/apple/apicert').'">添加苹果服务端API证书>>></a>');

                        // $form->display('apple_pay_callback_url_sbx', '苹果IAP回调地址（沙盒环境）')
                        //     ->readonly()
                        //     ->with(function ($value) {
                        //         $params = Helpers::simpleEncode($this->getAppKey().'-'.SaaSAdmin::user()->id);
                        //         return url('api/sandbox/apple/verify/notify/'.$params);
                        //     });
                        
                        $form->display('apple_pay_callback_url_prd', '苹果IAP回调地址')
                            ->readonly()
                            ->with(function ($value) {
                                $params = Helpers::simpleEncode($this->getAppKey().'-'.SaaSAdmin::user()->id);
                                return config('app.api_url').'/v1/order/callback/apple/'.$params;
                            })->help('请复制以上地址作为苹果IAP回调地址，沙盒和生产环境均使用同一个地址。<button type="button" class="btn btn-xs btn-info" id="callback-help-btn"><i class="fa fa-question-circle"></i> 如何配置</button>');

                        $this->html('<span class="text-danger"><i class="fa fa-warning"></i> 如用户为订阅购买：<br/>1、系统将会以S2S通知中的expiresDate作为用户VIP的过期时间。<br/>2、必须配置共享密钥及回调地址。</span>');
                    
                        $form->iapCallbackCheck('apple_subscrip_check', '验证配置')
                            ->buttonText('验证配置是否正确')
                            ->dependentOn(['bundle_id', 'app_apple_id', 'shared_secret', 'apple_dev_s2s_config_id'])
                            ->default(0)
                            ->callbackUrl(config('app.api_url').'/v1/order/config/apple/callback-verify-status')
                            ->testUrl(admin_url('app/manager/'.$this->getAppKey().'/order/config/apple/verify-notify'))
                            ->help('为确保订阅功能正常使用，需要使用苹果IAP接口请求苹果发送回调验证。<br/>
                            验证接口文档：<a href="https://developer.apple.com/documentation/AppStoreServerAPI/Request-a-Test-Notification" target="_blank">查看文档>>></a>');
                    })->when(0, function (Form $form) {
                        $form->iapSingleCheck('apple_iap_single_check', '验证配置')
                            ->buttonText('验证配置是否正确')
                            ->dependentOn(['bundle_id', 'app_apple_id'])
                            ->default(0)
                            ->testUrl(admin_url('app/manager/'.$this->getAppKey().'/order/config/apple/verify-one-time-purchase'))
                            ->help('通过调用苹果IAP接口的方式来验证配置是否正确');
                    });
            });
        $this->action(admin_url('app/manager/'.$this->getAppKey().'/order/config/iap'))->method('post');
        $this->disableReset();
        Admin::html(<<<HTML
    <div class="modal fade" id="callback-help-modal" tabindex="-1" role="dialog" aria-labelledby="callback-help-modal-label">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="callback-help-modal-label">如何配置苹果IAP回调地址</h4>
                </div>
                <div class="modal-body" style="padding: 30px;">
                    <h4>配置步骤</h4>
                    <ol>
                        <li>登录 <a href="https://appstoreconnect.apple.com" target="_blank">App Store Connect</a></li>
                        <li>选择您的应用</li>
                        <li>在App信息里找到App Store 服务器通知</li>
                        <li>点击对应的生产环境服务器和沙盒环境服务器中间的“设置URL”</li>
                        <li>在弹出的窗口中，粘贴本页面显示的回调地址（沙盒和生产环境均使用同一个地址）</li>
                        <li>点击“存储”按钮完成配置</li>
                    </ol>
                    
                    <div class="alert alert-warning">
                        <i class="fa fa-info-circle"></i> <strong>重要提示：</strong><br>
                        - 请同时配置沙盒环境和生产环境，否则会导致苹果IAP回调失败<br>
                        - 配置完成后，建议在沙盒环境进行测试，确认回调正常工作
                    </div>

                    <h4>常见问题</h4>
                    <div class="panel-group" id="callback-faq">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#callback-faq" href="#faq1">
                                        配置后多久生效？
                                    </a>
                                </h4>
                            </div>
                            <div id="faq1" class="panel-collapse collapse">
                                <div class="panel-body">
                                    回调地址配置通常会在几分钟内生效，但有时可能需要等待最多24小时。
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#callback-faq" href="#faq2">
                                        如何验证回调是否正常工作？
                                    </a>
                                </h4>
                            </div>
                            <div id="faq2" class="panel-collapse collapse">
                                <div class="panel-body">
                                    在沙盒环境中进行测试购买，然后检查系统订单记录，确认是否收到了来自苹果的回调通知。
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#callback-faq" href="#faq3">
                                        回调地址有什么要求？
                                    </a>
                                </h4>
                            </div>
                            <div id="faq3" class="panel-collapse collapse">
                                <div class="panel-body">
                                    苹果要求回调地址必须使用HTTPS协议，并且需要有有效的SSL证书。我们提供的回调地址已满足这些要求。
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>
HTML);
        Admin::script(<<<JS
$(function () {
    // 点击帮助按钮时显示modal
    $('#callback-help-btn').on('click', function(e) {
        e.preventDefault();
        $('#callback-help-modal').modal('show');
    });
});
JS);
    }

    public function data()
    {
        $tenant_id = SaaSAdmin::user()->id;
        $config = app(OrderInterfaceConfig::class)->getConfig($tenant_id, $this->getAppKey());
        $iap_config = app(IAPConfigModel::class)->getConfig($tenant_id, $this->getAppKey());
        return[
            'suport_apple_pay' => $config->suport_apple_pay ?? 0,
            'bundle_id' => $iap_config->bundle_id ?? '',
            'app_apple_id' => $iap_config->app_apple_id ?? '',
            'apple_dev_s2s_config_id' => $iap_config->apple_dev_s2s_config_id ?? '',
            'subscrip_switch' => $iap_config->subscrip_switch ?? 0,
            'shared_secret' => $iap_config->shared_secret ?? '',
        ];
    }
}