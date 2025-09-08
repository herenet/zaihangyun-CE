<?php

namespace App\Admin\Actions;

use Carbon\Carbon;
use App\Libs\Helpers;
use App\Models\Order;
use App\Models\Product;
use App\Models\AlipayConfig;
use Illuminate\Http\Request;
use App\Services\AlipayService;
use App\Services\WechatPayService;
use App\Models\WechatPaymentConfig;
use Encore\Admin\Actions\RowAction;
use Illuminate\Support\Facades\Log;
use App\Models\OrderInterfaceConfig;
use App\Models\WechatOpenPlatformConfig;

class RefundAction extends RowAction
{
    public $name = '退款';
    
    /**
     * 定义弹出的表单
     */
    public function form()
    {
        $order = Order::find($this->getKey());
        if (!$order) {
            admin_error('订单不存在');
            return;
        }

        $product = Product::withTrashed()->find($order->product_id);
        
        // 显示订单信息
        $this->text('order_info', '订单信息')->readonly()->default("订单号: {$order->oid}，订单金额: ¥" . number_format($order->order_amount / 100, 2));
        
        $this->hidden('order_id')->default($order->oid);
        // 退款金额默认为订单金额
        $this->text('refund_amount', '退款金额')
            ->default(number_format($order->payment_amount / 100, 2))
            ->required()
            ->rules([
                'required',
                'numeric',
                'min:0.01',
                'max:9999999.99',
                'regex:/^\d+(\.\d{1,2})?$/'
            ],[
                'required' => '请输入退款金额',
                'numeric' => '退款金额必须为数字',
                'min' => '退款金额不能小于0.01',
                'max' => '退款金额不能大于9999999.99',
                'regex' => '只允许输入两位小数'
            ])
            ->help('最大可退金额为 ¥' . number_format($order->payment_amount / 100, 2))
            ->attribute(['style' => 'width: 150px;']);
        
        // 退款类型
        if($product){
            $this->radio('refund_type', '退款类型')
            ->options(Order::$refundTypeMap)
            ->default(Order::REFUND_TYPE_ORIGINAL)
            ->rules('required|in:' . implode(',', array_keys(Order::$refundTypeMap)))
            ->required()
            ->help('退款并退功能：退款同时功能也将恢复到本次购买前状态。');
        }else{
            $this->radio('refund_type', '退款类型')
            ->options([Order::REFUND_TYPE_ONLY => Order::$refundTypeMap[Order::REFUND_TYPE_ONLY]])
            ->default(Order::REFUND_TYPE_ONLY)
            ->rules('required|in:' . implode(',', array_keys(Order::$refundTypeMap)))
            ->required()
            ->help('当前订单产品信息已被删除，只允许仅退款操作');
        }
       
        // 退款原因
        $this->textarea('refund_reason', '退款原因')
            ->rows(3)
            ->rules('nullable|string|max:30')
            ->help('请输入退款原因，不超过30个字，当订单退款金额小于等于1元且为部分退款时，退款原因将不会在微信消息中体现');

        $this->modalLarge();
    }
    
    // 处理提交的表单
    public function handle(Order $order, Request $request)
    {
        try {
            // 验证退款金额不超过支付金额
            $refundAmount = $request->get('refund_amount');
            if ($refundAmount * 100 > $order->payment_amount) {
                return $this->response()->error('退款金额不能超过支付金额');
            }

            if($refundAmount <= 0) {
                return $this->response()->error('退款金额不能小于0');
            }

            if($order->status == Order::STATUS_REFUNDED) {
                return $this->response()->success('订单已退款，无需重复退款')->refresh();
            }

            switch($order->pay_channel) {
                case Order::PAY_CHANNEL_WECHAT:
                    $this->refundWechat($order, $refundAmount, $request->get('refund_type'), $request->get('refund_reason'));
                    break;
                case Order::PAY_CHANNEL_ALIPAY:
                    $this->refundAlipay($order, $refundAmount, $request->get('refund_type'), $request->get('refund_reason'));
                    break;
                // case Order::PAY_CHANNEL_APPLE:
                //     // $this->refundApple($order, $refundAmount, $request->get('refund_type'), $request->get('refund_reason'));
                //     break;
                default:
                    return $this->response()->error('不支持的支付方式');
            }
            
            // 返回成功信息
            return $this->response()
                ->success('退款申请已提交，请等待处理')
                ->refresh();
                
        } catch (\Throwable $e) {
            Log::channel('refund')->error('退款申请失败: ' . $e->getMessage(), ['order_info' => $order->toArray(), 'trace' => $e->getTraceAsString()]);
            
            return $this->response()->toastr()->error('退款申请失败: ' . $e->getMessage());
        }
    }

    private function refundAlipay(Order $order, $refundAmount, $refundType, $refundReason)
    {
        $order_interface_config = OrderInterfaceConfig::where('app_key', $order->app_key)->first();
        if(!$order_interface_config) {
            throw new \Exception('订单接口配置不存在');
        }

        if($order_interface_config->switch == 0) {
            throw new \Exception('订单接口配置未开启');
        }

        if($order_interface_config->suport_alipay == 0){
            throw new \Exception('支付宝功能未开启');
        }

        $refund_amount_int  = (int)($refundAmount * 100);

        if($refund_amount_int > $order->payment_amount) {
            throw new \Exception('退款金额不能超过支付金额');
        }

        if($refund_amount_int <= 0) {
            throw new \Exception('退款金额不能小于0');
        }

        $alipay_config = AlipayConfig::where('app_key', $order->app_key)->first();
        if(!$alipay_config) {
            throw new \Exception('支付宝配置不存在');
        }

        try{
            $alipay_config_params = [
                'alipay_app_id' => $alipay_config['alipay_app_id'],
                'app_private_cert' => $alipay_config['app_private_cert'],
                'alipay_public_cert' => $alipay_config['alipay_public_cert'],
            ];

            $alipay_service = new AlipayService($alipay_config_params);
            $ret = $alipay_service->applyRefund(
                $order->oid, 
                $refundAmount, 
                $refundReason
            );
            Log::channel('refund')->info('支付宝退款申请', $ret);
            if($ret['code'] == '10000') {
                $order->refund_id = $ret['trade_no'];
                $order->status = Order::STATUS_REFUNDING;
                $order->refund_send_time = now();
                $order->refund_amount = (int)($ret['refund_fee']*100);
                $order->refund_reason = $refundReason;
                $order->refund_type = $refundType;
                $order->refund_channel = 'ORIGINAL';
                $order->save();
                return true;
            } else {
                Log::channel('refund')->error('支付宝退款申请失败', $ret);
                throw new \Exception('退款申请失败: ' . $ret['msg']);
            }

        }catch(\Throwable $e){
            throw $e;
        }
    }

    private function refundWechat(Order $order, $refundAmount, $refundType, $refundReason)
    {
        $order_interface_config = OrderInterfaceConfig::where('app_key', $order->app_key)->first();
        if(!$order_interface_config) {
            throw new \Exception('订单接口配置不存在');
        }

        if($order_interface_config->switch == 0) {
            throw new \Exception('订单接口配置未开启');
        }

        if($order_interface_config->suport_wechat_pay == 0){
            throw new \Exception('微信支付功能未开启');
        }

        $refundAmount  = (int)($refundAmount * 100);

        if($refundAmount > $order->payment_amount) {
            throw new \Exception('退款金额不能超过支付金额');
        }

        if($refundAmount <= 0) {
            throw new \Exception('退款金额不能小于0');
        }

        $wechatPaymentConfig = WechatPaymentConfig::where('id', $order_interface_config->wechat_payment_config_id)->first();
        if(!$wechatPaymentConfig) {
            throw new \Exception('微信支付配置不存在');
        }
        $wechatOpenPlatformConfig = WechatOpenPlatformConfig::where('id', $order_interface_config->wechat_platform_config_id)->first();
        if(!$wechatOpenPlatformConfig) {
            throw new \Exception('微信开放平台配置不存在');
        }

        $notify_params = $order_interface_config['wechat_platform_config_id'];
        $notify_params_encode = Helpers::simpleEncode($notify_params);
        $notify_url = config('app.api_url').'/v1/order/callback/wechat/refund/'.$notify_params_encode;

        try {
            $wechatPayService = new WechatPayService(
                $wechatOpenPlatformConfig->appid, 
                $wechatPaymentConfig->mch_id, 
                $wechatPaymentConfig->mch_cert_serial, 
                $wechatPaymentConfig->mch_private_key_path, 
                $wechatPaymentConfig->mch_platform_cert_path, 
                $notify_url
            );
            $result = $wechatPayService->applyRefund(
                $order->tid, 
                $order->payment_amount, 
                $refundAmount, 
                $order->oid, 
                $refundReason
            );
            Log::channel('refund')->info('微信退款申请', $result);
            if (isset($result['status']) && ($result['status'] == 'SUCCESS' || $result['status'] == 'PROCESSING')) {
                // 退款成功，更新订单状态
                if($result['status'] == 'SUCCESS') {
                    $status = Order::STATUS_REFUNDED;
                    $order->refund_time = isset($result['success_time']) ? Carbon::parse($result['success_time'])->format('Y-m-d H:i:s') : null;
                } else {
                    $status = Order::STATUS_REFUNDING;
                }

                $order->refund_id = $result['refund_id'];
                $order->status = $status;
                $order->refund_type = $refundType;
                $order->refund_reason = $refundReason;
                $order->refund_amount = $refundAmount;
                $order->refund_send_time = now();
                $order->refund_channel = $result['channel'] ?? 'ORIGINAL';
                $order->save();
                return true;
            } else {
                Log::channel('refund')->error('微信退款申请失败', $result);
                throw new \Exception('退款申请失败: ' . $result['message']);
            }
        } catch (\Throwable $e) {
            throw $e;
        }
    }
}