<?php

namespace App\SaaSAdmin\Controllers\Manager;

use Carbon\Carbon;
use App\Models\User;
use App\Libs\Helpers;
use App\Models\Order;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Models\Product;
use WeChatPay\Formatter;
use App\SaaSAdmin\AppKey;
use WeChatPay\Crypto\Rsa;
use Illuminate\Http\Request;
use WeChatPay\Crypto\AesGcm;
use Encore\Admin\Layout\Content;
use App\Models\WechatPaymentConfig;
use Illuminate\Support\Facades\Log;
use App\SaaSAdmin\Facades\SaaSAdmin;
use Illuminate\Support\Facades\Cache;
use App\SaaSAdmin\Actions\RefundAction;
use Illuminate\Support\Facades\Storage;
use Encore\Admin\Controllers\AdminController;

class OrderController extends AdminController
{
    use AppKey;

    public function index(Content $content)
    {
        return $content
            ->title('订单列表')
            ->body($this->grid());
    }

    protected function grid()
    {
        $grid = new Grid(new Order());
        $grid->model()->where('app_key', $this->getAppKey())->orderBy('created_at', 'desc');
        $grid->tools(function ($tools) {
            $tools->append('<a href="/docs/1.x/apis/order_list" class="btn btn-sm btn-primary" target="_blank"><i class="fa fa-book"></i> 查看接口文档</a>');
        });
        $grid->fixColumns(2, -2);
        $grid->column('oid', '订单ID');
        $grid->column('uid', '用户ID');
        $grid->column('user.nickname', '用户昵称');
        $grid->column('product_id', '产品ID');
        $grid->column('product.name', '产品名称');
        $grid->column('product_price', '产品价格')->display(function ($value) {
            return '￥'.number_format($value / 100, 2);
        });
        $grid->column('discount_amount', '优惠金额')->display(function ($value) {
            return '￥'.number_format($value / 100, 2);
        });
        $grid->column('order_amount', '订单金额')->display(function ($value) {
            return '￥'.number_format($value / 100, 2);
        });
        $grid->column('payment_amount', '实际支付')->display(function ($value) {
            return '￥'.number_format($value / 100, 2);
        });
        $grid->column('platform_order_amount', '三方订单金额')->display(function ($value) {
            if ($value > 0) {
                return '￥'.number_format($value / 100, 2);
            }
            return null;
        });
        $grid->column('status', '订单状态')->using(Order::$statusMap)->label([
            Order::STATUS_PENDING => 'info',
            Order::STATUS_PAID => 'success',
            Order::STATUS_REFUNDING => 'danger',
            Order::STATUS_REFUNDED => 'warning',
            Order::STATUS_PAYMENT_FAILED => 'danger',
            Order::STATUS_REFUND_FAILED => 'danger',
        ]);
        $grid->column('pay_channel', '支付方式')->using(Order::$payChannelMap)->prependIcon('pay');
        $grid->column('tid', '第三方订单号');
        $grid->column('trade_type', '交易类型');
        $grid->column('bank_type', '银行类型');
        $grid->column('open_id', '三方用户标识')->limit(32);
        $grid->column('pay_time', '支付时间');
        $grid->column('refund_id', '退款ID');
        $grid->column('refund_type', '退款类型')->using(Order::$refundTypeMap);
        $grid->column('refund_amount', '退款金额')->display(function ($value) {
            return $value > 0 ? '￥'.number_format($value / 100, 2) : null;
        });
        $grid->column('refund_channel', '退款渠道')->using(Order::$refundChannelMap);
        $grid->column('refund_send_time', '退款发起时间');
        $grid->column('refund_time', '退款时间');
        $grid->column('refund_reason', '退款原因')->limit(30);
        $grid->column('updated_at', '更新时间');
        $grid->column('created_at', '创建时间');

        $grid->filter(function ($filter) {
            $filter->equal('status', '订单状态')->select(Order::$statusMap)->config('minimumResultsForSearch', 'Infinity');
            $filter->equal('pay_channel', '支付方式')->select(Order::$payChannelMap) ->config('minimumResultsForSearch', 'Infinity');
            $filter->equal('tid', '第三方订单号');
            $filter->equal('uid', '用户ID');
            $filter->equal('product_id', '产品ID');
        });

        $grid->export(function ($export) {
            $export->filename('订单列表-'.date('Y-m-d H:i:s').'-'.$this->getAppKey().'.csv');
            $export->column('status', function ($value, $original) {
                return Order::$statusMap[$original];
            });
            $export->column('pay_channel', function ($value, $original) {
                return Order::$payChannelMap[$original];
            });
            $export->except(['app_key', 'tenant_id']);
        });

        $grid->disableCreateButton();
        $grid->disableBatchActions();
        $grid->actions(function ($actions) {
            $actions->disableEdit();
            $actions->disableDelete();
            if (in_array($actions->row->status, [Order::STATUS_PAID, Order::STATUS_REFUNDING])) {
                $actions->add(new RefundAction());
            }
        });

        return $grid;
    }

    public function detail()
    {
        $oid = request()->route('list');
        $order = new Show(Order::find($oid));
        $order->field('oid', '订单ID');
        $order->field('uid', '用户ID');
        $order->field('product_id', '产品ID');
        $order->field('product_price', '产品价格')->as(function ($value) {
            return '￥'.number_format($value / 100, 2);
        });
        $order->field('discount_amount', '优惠金额')->as(function ($value) {
            return '￥'.number_format($value / 100, 2);
        });
        $order->field('order_amount', '订单金额')->as(function ($value) {
            return '￥'.number_format($value / 100, 2);
        });
        $order->field('payment_amount', '实际支付')->as(function ($value) {
            return '￥'.number_format($value / 100, 2);
        });
        $order->field('status', '订单状态')->using(Order::$statusMap);
        $order->field('pay_channel', '支付方式')->using(Order::$payChannelMap);
        $order->field('tid', '第三方订单号');
        $order->field('trade_type', '交易类型');
        $order->field('bank_type', '银行类型');
        $order->field('open_id', '三方用户标识');
        $order->field('refund_id', '退款ID');
        $order->field('refund_type', '退款类型')->using(Order::$refundTypeMap);
        $order->field('refund_amount', '退款金额')->as(function ($value) {
            return $value > 0 ? '￥'.number_format($value / 100, 2) : null;
        });
        $order->field('refund_channel', '退款渠道')->using(Order::$refundChannelMap);
        $order->field('refund_send_time', '退款发起时间');
        $order->field('refund_time', '退款时间');
        $order->field('refund_reason', '退款原因');
        $order->field('pay_time', '支付时间');
        $order->field('updated_at', '更新时间');
        $order->field('created_at', '创建时间');

        $order->panel()
        ->tools(function ($tools) {
            $tools->disableEdit();
            $tools->disableDelete();
        });

        return $order;
        
    }

    public function wechatRefundCallback($encodeNotifyParams)
    {
        try{
            $notify_params = Helpers::simpleDecode($encodeNotifyParams);
            list($wechat_payment_config_id, $tenant_id) = explode('-', $notify_params);

            Log::channel('refund')->info('微信退款回调', [
                'tenant_id' => $tenant_id, 
                'wechat_payment_config_id' => $wechat_payment_config_id, 
                'headers' => request()->header(), 
                'body' => request()->getContent()
            ]);

            $wechat_payment_config = WechatPaymentConfig::where('id', $wechat_payment_config_id)->where('tenant_id', $tenant_id)->first();
            if (!$wechat_payment_config) {
                throw new \Exception('商户配置错误:'.$wechat_payment_config_id.'-'.$tenant_id);
            }

            $inWechatpaySignature = request()->header('Wechatpay-Signature');
            $inWechatpayTimestamp = request()->header('Wechatpay-Timestamp');
            $inWechatpayNonce = request()->header('Wechatpay-Nonce');
            // $inWechatpaySerial = request()->header('Wechatpay-Serial');
            $inBody = request()->getContent();
            
            $api_v3_key = $wechat_payment_config['mch_api_v3_secret'];
            $platform_public_key_file = Storage::disk('SaaSAdmin-mch')->path($wechat_payment_config['mch_platform_cert_path']);
            $platform_pubic_key_instance = Rsa::from("file://".$platform_public_key_file, Rsa::KEY_TYPE_PUBLIC);

            $time_offset_status = 1800 >= abs(Formatter::timestamp() - (int)$inWechatpayTimestamp);
            $verified_status = Rsa::verify(
                Formatter::joinedByLineFeed($inWechatpayTimestamp, $inWechatpayNonce, $inBody),
                $inWechatpaySignature,
                $platform_pubic_key_instance
            );
            
            if ($time_offset_status && $verified_status) {
            
                $in_body_array = (array) json_decode($inBody, true);
                ['resource' => [
                    'ciphertext'      => $ciphertext,
                    'nonce'           => $nonce,
                    'associated_data' => $aad
                ]] = $in_body_array;

                $in_body_resource = AesGcm::decrypt($ciphertext, $api_v3_key, $nonce, $aad);
                $in_body_resource_array = (array) json_decode($in_body_resource, true);

                Log::channel('refund')->info('微信退款回调', [
                    'in_body_resource_array' => $in_body_resource_array
                ]);

                $oid = $in_body_resource_array['out_trade_no'];
                
                // 查找并更新订单
                $order = Order::where('oid', $oid)->first();
                if (!$order) {
                    throw new \Exception('订单不存在:'.$oid);
                }

                if($order->status == Order::STATUS_REFUNDED) {
                    return response('SUCCESS');
                }
                
                // 更新订单状态
                if ($in_body_resource_array['refund_status'] == 'SUCCESS') {
                    $order->status = Order::STATUS_REFUNDED;
                    $order->refund_time = isset($in_body_resource_array['success_time']) 
                        ? Carbon::parse($in_body_resource_array['success_time'])->format('Y-m-d H:i:s') 
                        : null;
                    $order->save();
                    $this->refundLogic($order);
                } else {
                    $order->status = Order::STATUS_REFUND_FAILED;
                    $order->save();
                    
                    // 记录日志
                    Log::channel('refund')->error('微信退款失败', [
                        'order_id' => $order->oid,
                        'refund_id' => $order->refund_id,
                        'result' => $in_body_resource_array
                    ]);
                }
                
                return response('SUCCESS');
            }else{
                throw new \Exception('回调验证未通过');
            }
        }catch(\Throwable $e){
            Log::channel('refund')->error('微信退款回调失败', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'code' => 'FAIL',
                'message' => '微信退款回调失败'
            ], 500);
        }
    }

    public function aliRefundCallback()
    {
        $data = request()->all();
        dd($data);
    }

    public function appleRefundCallback()
    {
        $data = request()->all();
        dd($data);
    }

    public function sendRefundCode(Request $request)
    {
        $mobile = SaaSAdmin::user()->phone_number;
        $orderId = $request->input('order_id');
        
        // 检查订单是否存在
        $order = Order::find($orderId);
        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => '订单不存在'
            ]);
        }

        if(!in_array($order->status, [Order::STATUS_PAID, Order::STATUS_REFUNDING])) {
            return response()->json([
                'status' => false,
                'message' => '订单状态错误'
            ]);
        }
        
        // 检查发送频率限制
        $throttleKey = 'refund_throttle_' . $mobile;
        if (Cache::has($throttleKey)) {
            return response()->json([
                'status' => false,
                'message' => '发送过于频繁，请稍后再试'
            ]);
        }
        
        // 生成验证码
        $code = mt_rand(100000, 999999);
        $cacheKey = RefundAction::REFUNED_VERIFY_CODE_CACHE_KEY;
        $cacheKey = str_replace(['{mobile}', '{order_id}'], [$mobile, $orderId], $cacheKey);
        
        try {
            // 发送验证码
            app(\App\Services\SmsService::class)->sendVerifyCode($mobile, $code);
            
            // 缓存验证码，有效期 5 分钟
            Cache::put($cacheKey, $code, RefundAction::REFUNED_VERIFY_CODE_EXPIRE_TIME);
            
            Cache::put($throttleKey, 1, 10);
            
            return response()->json([
                'status' => true,
                'message' => '验证码已发送'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => '发送失败，请稍后再试：' . $e->getMessage()
            ]);
        }
    }

    protected function refundLogic(Order $order)
    {
        if($order->refund_type == Order::REFUND_TYPE_ONLY) {
            return;
        }
        $user = User::find($order->uid);
        if(!$user) {
            throw new \Exception('用户不存在');
        }

        $product = Product::find($order->product_id);
        if(!$product) {
            throw new \Exception('产品不存在');
        }

        switch($product->type) {
            case Product::TYPE_VALUE_KEY_FOR_DURATION_MEMBER:
                $vip_expired_at = null;
                if($user->vip_expired_at) {
                    $vip_time_left = strtotime($user->vip_expired_at) - $product->function_value * 24 * 60 * 60;
                    if($vip_time_left > time()) {
                        $vip_expired_at = date('Y-m-d H:i:s', $vip_time_left);
                    }
                }
                $user->vip_expired_at = $vip_expired_at;
                $user->save();
                break;
            case Product::TYPE_VALUE_KEY_FOR_FOREVER_MEMBER:
                $user->is_forever_vip = 0;
                $user->save();
                break;
            default:
                throw new \Exception('产品类型错误');
        }    
    }
}
