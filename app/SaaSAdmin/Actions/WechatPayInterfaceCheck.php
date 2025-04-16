<?php

namespace App\SaaSAdmin\Actions;

use Storage;
use Illuminate\Http\Request;
use App\Services\WechatPayService;
use App\Models\WechatPaymentConfig;
use Encore\Admin\Actions\RowAction;
use Illuminate\Support\Facades\Http;
use App\Models\WechatOpenPlatformConfig;

class WechatPayInterfaceCheck extends RowAction
{
    public $name = '验证配置';

    // 这个方法定义在列表中如何显示
    public function display($value)
    {
        if ($value == 1) {
            return '<span class="label label-success">验证通过</span>';
        } else {
            // 显示一个可点击的按钮
            return <<<HTML
            <span class="interface-check-btn" data-id="{$this->getKey()}">
                <button class="btn btn-xs btn-warning">点击验证</button>
            </span>
            <span class="interface-check-loading" style="display:none">
                <i class="fa fa-spinner fa-spin"></i> 验证中...
            </span>
            HTML;
        }
    }

    public function form()
    { 
        $this->select('wechat_appid', '选择微信开放平台APP')
        ->options(WechatOpenPlatformConfig::where('tenant_id', \App\SaaSAdmin\Facades\SaaSAdmin::user()->id)
        ->get()->pluck('app_name', 'wechat_appid'))
        ->required();
    }

    // 这个方法定义点击后的处理逻辑
    public function handle(WechatPaymentConfig $config, Request $request)
    {
        try {
            // 获取选择的微信开放平台APP
            $wechat_appid = $request->get('wechat_appid');
            
            // 调用验证接口
            $result = $this->validateInterface($config, $wechat_appid);
            
            if ($result['status']) {
                // 验证成功，更新数据库
                $config->interface_check = 1;
                $config->save();
                
                return $this->response()
                    ->success('配置验证成功')
                    ->html('<span class="label label-success">验证通过</span>')
                    ->refresh(); // 刷新列表
            } else {
                // 验证失败
                $config->interface_check = 0;
                $config->save();
                
                return $this->response()
                    ->error('配置验证失败: ' . ($result['message'] ?? '未知错误'))
                    ->refresh(false); // 不刷新列表
            }
        } catch (\Exception $e) {
            return $this->response()
                ->error('验证过程发生错误: ' . $e->getMessage())
                ->refresh(false);
        }
    }

    // 实际的接口验证逻辑
    protected function validateInterface($config, $wechatAppid)
    {
        $fileExists = Storage::disk('SaaSAdmin-mch')->exists($config->mch_private_key_path);
        if (!$fileExists) {
            return ['status' => false, 'message' => '商户私钥文件不存在'];
        }

        $fileExists = Storage::disk('SaaSAdmin-mch')->exists($config->mch_platform_cert_path);
        if (!$fileExists) {
            return ['status' => false, 'message' => '平台证书文件不存在'];
        }

        // $notify_url = route('wechat.payment.check-callback', ['config_id' => $config->id]);
        $notify_url = 'https://www.zaihangyun.com/api/wechat/payment/check-callback/'.$config->id;
        try {
            $wechatPayService = new WechatPayService(
                $wechatAppid, 
                $config->mch_id, 
                $config->mch_cert_serial, 
                $config->mch_private_key_path, 
                $config->mch_platform_cert_path, 
                $notify_url,
            );
            $wechatPayService->createAppOrder('1234567890', 1, '测试订单');
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
        
        return ['status' => true];
    }

}
