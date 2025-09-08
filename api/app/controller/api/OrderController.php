<?php

namespace app\controller\api;

use Exception;
use Throwable;
use support\Db;
use support\Log;
use Carbon\Carbon;
use app\model\User;
use app\model\Order;
use support\Request;
use support\Response;
use app\model\Product;
use WeChatPay\Formatter;
use WeChatPay\Crypto\Rsa;
use app\model\AlipayConfig;
use app\validate\OrderInfo;
use app\validate\OrderList;
use Illuminate\Support\Str;
use WeChatPay\Crypto\AesGcm;
use app\validate\CreateOrder;
use app\service\AlipayService;
use Webman\RateLimiter\Limiter;
use app\service\WechatPayService;
use app\model\WechatPaymentConfig;
use app\model\OrderInterfaceConfig;
use app\service\OrderBZLogicService;
use app\model\WechatOpenPlatformConfig;
use support\exception\BusinessException;
use WebmanTech\LaravelFilesystem\Facades\Storage;

class OrderController
{

    public $noNeedAuth = ['wechatCallback', 'alipayCallback', 'wechatRefundCallback'];

    public function info(Request $request)
    {
        $token_info = $request->token_info;
        $uid = $token_info['uid'];

        $validate = new OrderInfo();
        if (!$validate->check($request->all())) {
            dump($validate->getErrorInfo());
            return json($validate->getErrorInfo());
        }

        $oid = $request->input('oid');
        $need_product_info = $request->input('need_product_info', 0);

        $order_model = new Order();
        $order = $order_model->getOrderInfoByOidAndUid($oid, $uid);
        if(empty($order)){
            return json(['code' => 400199, 'msg' => 'order not found']);
        }

        if($need_product_info == 1){
            $product_model = new Product();
            $product = $product_model->getProductInfoByPid($order['product_id'], $order['app_key']);
            $order['product_info'] = $product;
        }

        unset($order['app_key']);

        return json(['code' => config('const.request_success'), 'msg' => 'success', 'data' => $order]);
    }

    public function create(Request $request)
    {
        $token_info = $request->token_info;
        $appkey = $token_info['app_key'];
        $uid = $token_info['uid'];
        Limiter::check($uid, 1, 1, 'operation too frequent');//每个用户1秒最多1条订单

        $validate = new CreateOrder();
        if (!$validate->check($request->all())) {
            return json($validate->getErrorInfo());
        }
        
        $product_model = new Product();
        $product = $product_model->getProductInfoByPid($request->input('pid'), $appkey);
        if(empty($product)){
            return json(['code' => 400199, 'msg' => 'product not found']);
        }

        if($product['sale_status'] != Product::STATUS_ON){
            return json(['code' => 400198, 'msg' => 'product is not on sale']);
        }

        try{
            $price = $product['sale_price'];
            $pay_channel = $request->input('pay_channel');
            $channel = $request->input('channel');

            $order_interface_config_model = new OrderInterfaceConfig();
            $order_interface_config = $order_interface_config_model->getOrderInterfaceConfigByAppKey($appkey);
            $order_id = generateOrderId($pay_channel, $order_interface_config['oid_prefix']);

            $order_crt_data = [
                'oid' => $order_id,
                'app_key' => $appkey,
                'uid' => $uid,
                'product_id' => $product['pid'],
                'product_price' => $price,
                'order_amount' => $price,
                'payment_amount' => $price,
                'pay_channel' => $pay_channel,
                'channel' => $channel ?? Order::DEFAULT_CHANNEL,
                'status' => Order::STATUS_READY,
            ];

            if($price <= 0){
                //for free
                return $this->createFreeOrder($order_crt_data, $product);
            }else{
                switch($pay_channel){
                    case Order::PAY_CHANNEL_WECHAT:
                        return $this->createWechatOrder($order_crt_data, $product, $appkey);
                    case Order::PAY_CHANNEL_ALIPAY:
                        return $this->createAlipayOrder($order_crt_data, $product, $appkey);
                    default:
                        return json(['code' => 400189, 'msg' => 'invalid pay channel']);
                }
            }
        }catch(BusinessException $e){
            return json(['code' => $e->getCode(), 'msg' => $e->getMessage()]);
        }catch(\Exception $e){
            Log::channel('order')->error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return json(['code' => 400250, 'msg' => 'create order failed']);
        }
    }

    protected function createAlipayOrder($order_crt_data, $product, $appkey)
    {
        $order_interface_config_model = new OrderInterfaceConfig();
        $order_interface_config = $order_interface_config_model->getOrderInterfaceConfigByAppKey($order_crt_data['app_key']);
        if(empty($order_interface_config)){
            throw new BusinessException('order interface config not found', 400197);
        }
        if(empty($order_interface_config['switch'] ) || $order_interface_config['switch'] == 0){
            throw new BusinessException('order interface is not open', 400196);
        }
        if(empty($order_interface_config['suport_alipay']) || $order_interface_config['suport_alipay'] == 0){
            throw new BusinessException('alipay is not enabled', 400195);
        }
        
        $alipay_config_model = new AlipayConfig();
        $alipay_config = $alipay_config_model->getAlipayConfig($appkey);
        if(empty($alipay_config)){
            throw new BusinessException('alipay config not found', 400194);
        }

        if($alipay_config['interface_check'] != 1){
            throw new BusinessException('alipay interface check not passed', 400193);
        }

        try{
            $alipay_config_params = [
                'alipay_app_id' => $alipay_config['alipay_app_id'],
                'app_private_cert' => $alipay_config['app_private_cert'],
                'alipay_public_cert' => $alipay_config['alipay_public_cert'],
            ];
            $alipay_service = new AlipayService($alipay_config_params);
            $ret = $alipay_service->createAppOrder(
                $product['name'], 
                $order_crt_data['oid'], 
                $order_crt_data['payment_amount'], 
                $order_crt_data['app_key'], 
            );
            $this->createOrder($order_crt_data);
            return json([
                'code' => config('const.request_success'), 
                'msg' => 'success', 
                'data' => [
                    'oid' => $order_crt_data['oid'],
                    'order_str' => $ret,
                ]
            ]);
        }catch(\Exception $e){
            Log::channel('order')->error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw new BusinessException('create alipay order failed', 400250);
        }
    }

    public function alipayCallback(Request $request, $params)
    {
        Log::channel('order')->info('params:'.$params);
        Log::channel('order')->info('Callback enter:', ['headers' => $request->header(),'all' => $request->all(), 'body' => $request->rawBody()]);

        try{
            $appkey = simpleDecode($params);

            $alipay_config_model = new AlipayConfig();
            $alipay_config = $alipay_config_model->getAlipayConfig($appkey);
            $alipay_config_params = [
                'alipay_app_id' => $alipay_config['alipay_app_id'],
                'app_private_cert' => $alipay_config['app_private_cert'],
                'alipay_public_cert' => $alipay_config['alipay_public_cert'],
            ];
            $alipay_service = new AlipayService($alipay_config_params);
            $result = $alipay_service->verifyNotify($request->all());
            if($result){
                $oid = $request->input('out_trade_no');

                //是否是退款通知
                $refund_fee = $request->input('refund_fee') ? (int) ($request->input('refund_fee') * 100) : false;
                if($refund_fee === false){
                    if($request->input('trade_status') == 'TRADE_SUCCESS'){
                        $trade_status = Order::STATUS_SUCCESS;
                    }else{
                        $trade_status = Order::STATUS_PAYMENT_FAILED;
                    }
    
                    $data = [
                        'platform_order_amount' => ceil($request->input('total_amount', 0) * 100),
                        'payment_amount' => ceil($request->input('buyer_pay_amount', 0) * 100),
                        'open_id' => $request->input('buyer_id') ?? $request->input('buyer_open_id') ?? '',
                        'status' => $trade_status,
                        'tid' => $request->input('trade_no'),
                        'trade_type' => 'APP',
                        'bank_type' => '',
                        'pay_time' => $request->input('gmt_payment'),
                    ];
    
                    $this->rsyncOrder($oid, $data);
                }else{
                    if(in_array($request->input('trade_status'), ['TRADE_SUCCESS', 'TRADE_CLOSED'])){
                        $refund_time = date('Y-m-d H:i:s', strtotime($request->input('gmt_refund')));
                        $this->handleAlipayRefundOrder($oid, $refund_time, $refund_fee);
                    }
                }

                return new Response(200, [], 'success');
            }

            throw new Exception('回调验证未通过');
            
        }catch(Exception $e){
            Log::channel('order')->error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return new Response(200, [], 'failed');
        }
    }

    public function wechatCallback(Request $request, $params)
    {
        Log::channel('order')->info('Callback enter:', ['headers' => $request->header(), 'body' => $request->rawBody()]);
        $in_wechatpay_signature = $request->header('Wechatpay-Signature');
        $in_wechatpay_timestamp = $request->header('Wechatpay-Timestamp');
        // $in_wechatpay_serial = $request->header('Wechatpay-Serial');
        $in_wechatpay_nonce = $request->header('Wechatpay-Nonce');
        $in_body = $request->rawBody();

        try {
            $wechat_payment_config_id = simpleDecode($params);
            $wechat_payment_config_model = new WechatPaymentConfig();
            $wechat_payment_config = $wechat_payment_config_model->getWechatPaymentConfig(
                $wechat_payment_config_id, 
            );

            $api_v3_key = $wechat_payment_config['mch_api_v3_secret'];
            $platform_public_key_file = Storage::disk('local_mch')->path($wechat_payment_config['mch_platform_cert_path']);
            $platform_pubic_key_instance = Rsa::from("file://".$platform_public_key_file, Rsa::KEY_TYPE_PUBLIC);

            $time_offset_status = 1800 >= abs(Formatter::timestamp() - (int)$in_wechatpay_timestamp);
            $verified_status = Rsa::verify(
                Formatter::joinedByLineFeed($in_wechatpay_timestamp, $in_wechatpay_nonce, $in_body),
                $in_wechatpay_signature,
                $platform_pubic_key_instance
            );

            if ($time_offset_status && $verified_status) {
                $in_body_array = (array) json_decode($in_body, true);
                ['resource' => [
                    'ciphertext'      => $ciphertext,
                    'nonce'           => $nonce,
                    'associated_data' => $aad
                ]] = $in_body_array;

                $in_body_resource = AesGcm::decrypt($ciphertext, $api_v3_key, $nonce, $aad);
                $in_body_resource_array = (array) json_decode($in_body_resource, true);
                Log::channel('order')->info('Callback return:', $in_body_resource_array);

                $oid = $in_body_resource_array['out_trade_no'];
                
                if($in_body_resource_array['trade_state'] == 'SUCCESS'){
                    $trade_status = Order::STATUS_SUCCESS;
                }else{
                    $trade_status = Order::STATUS_PAYMENT_FAILED;
                }

                $data = [
                    'platform_order_amount' => $in_body_resource_array['amount']['total'],
                    'payment_amount' => $in_body_resource_array['amount']['payer_total'],
                    'open_id' => $in_body_resource_array['payer']['openid'],
                    'status' => $trade_status,
                    'tid' => $in_body_resource_array['transaction_id'],
                    'trade_type' => $in_body_resource_array['trade_type'],
                    'bank_type' => $in_body_resource_array['bank_type'],
                    'pay_time' => date('Y-m-d H:i:s', strtotime($in_body_resource_array['success_time'])),
                ];

                $this->rsyncOrder($oid, $data);
                return new Response(200);
            }else{
                throw new Exception('回调验证未通过');
            }
        } catch (Throwable $e) {
            Log::channel('order')->error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return new Response(500, [], json_encode([
                'code' => 'FAIL',
                'message' => $e->getMessage(),
            ]));
        }
        return;
    }

    protected function handleAlipayRefundOrder(string $oid, string $refund_time, int $refund_fee) : void
    {
        $order_model = new Order();
        $order_info = $order_model->getOrderInfoByOid($oid);
        if(empty($order_info)){
            throw new Exception('退款订单不存在，OID:'.$oid);
        }

        if($order_info['status'] == Order::STATUS_REFUNDED){
            return;
        }

        if($order_model->updateOrderInfoByOid($oid, [
            'refund_time' => $refund_time, 
            'refund_amount' => $refund_fee, 
            'status' => Order::STATUS_REFUNDED
        ]) === false){
            throw new Exception('更新退款订单信息失败，OID:'.$oid);
        }

        self::refundOrderBZLogic($order_info);
    }

    protected function rsyncOrder(string $oid, array $data) : void
    {
        $order_model = new Order();
        $order_info = $order_model->getOrderInfoByOid($oid);
        if(empty($order_info)){
            throw new Exception('订单不存在，OID:'.$oid);
        }

        if($order_info['status'] == Order::STATUS_SUCCESS){
            Log::channel('order')->info('订单已处理，跳过重复回调', ['oid' => $oid]);
            return; // 订单已经是支付成功状态，直接返回成功，不再处理
        }

        $product_model = new Product();
        $product_info = $product_model->getProductInfoByPid($order_info['product_id'], $order_info['app_key']);

        Db::beginTransaction();
        try {
            $update_rs = $order_model->updateOrderInfoByOid($oid, $data);
            if($update_rs === false){
                Db::rollBack();
                throw new \Exception('update order info failed oid:'.$oid);
            }
            $rs = $this->orderBZLogic($order_info, $product_info);
            if($rs === false){
                Db::rollBack();
                throw new \Exception('order bz logic failed oid:'.$oid);
            }
            Db::commit();
        } catch (Exception $e) {
            Db::rollBack();
            throw new Exception($e->getMessage().$e->getTraceAsString());
        }
    }

    private function createFreeOrder($order_crt_data, $product)
    {
        $order_model = new Order();
        Db::beginTransaction();
        try{
            $order_crt_data['status'] = Order::STATUS_SUCCESS;
            $order = $order_model->createOrder($order_crt_data);
            $rs = $this->orderBZLogic($order, $product);
            if($rs === false){
                Db::rollBack();
                throw new \Exception('order bz logic failed');
            }
            Db::commit();
            return json(['code' => config('const.request_success'), 'msg' => 'success', 'data' => ['oid' => $order['oid']]]);
        }catch(\Throwable $e){
            Db::rollBack();
            Log::channel('order')->error('create free order failed', ['error' => $e->getMessage(), 'order_crt_data' => $order_crt_data]);
            throw new BusinessException('create free order failed', 400250);
        }
    }

    private function createOrder($order_crt_data)
    {
        $order_model = new Order();
        try{
            $order = $order_model->createOrder($order_crt_data);
        }catch(\Throwable $e){
            Log::channel('order')->error('create order failed', ['error' => $e->getMessage(), 'order_crt_data' => $order_crt_data]);
            throw new BusinessException('create order failed', 400250);
        }
    }

    private function createWechatOrder($order_crt_data, $product, $appkey)
    {
        $order_interface_config_model = new OrderInterfaceConfig();
        $order_interface_config = $order_interface_config_model->getOrderInterfaceConfigByAppKey($order_crt_data['app_key']);
        
        if(empty($order_interface_config)){
            throw new BusinessException('order interface config not found', 400197);
        }
        if(empty($order_interface_config['switch'] ) || $order_interface_config['switch'] == 0){
            throw new BusinessException('order interface is not open', 400196);
        }
        if(empty($order_interface_config['suport_wechat_pay']) || $order_interface_config['suport_wechat_pay'] == 0){
            throw new BusinessException('wechat pay is not enabled', 400195);
        }
        if(empty($order_interface_config['wechat_payment_config_id'])){
            throw new BusinessException('wechat merchant config not set', 400194);
        }
        if(empty($order_interface_config['wechat_platform_config_id'])){
            throw new BusinessException('wechat platform config not set', 400193);
        }


        $wechat_payment_config_model = new WechatPaymentConfig();
        $wechat_payment_config = $wechat_payment_config_model->getWechatPaymentConfig(
            $order_interface_config['wechat_payment_config_id'], 
        );
        if(empty($wechat_payment_config)){
            throw new BusinessException('wechat merchant config not found', 400192);
        }

        $wechat_open_platform_config_model = new WechatOpenPlatformConfig();
        $wechat_open_platform_config = $wechat_open_platform_config_model->getWechatOpenPlatformConfig(
            $order_interface_config['wechat_platform_config_id'], 
        );
        if(empty($wechat_open_platform_config)){
            throw new BusinessException('wechat platform config not found', 400191);
        }

        if($wechat_payment_config['interface_check'] != 1){
            throw new BusinessException('wechat interface check not passed', 400190);
        }
        
        try {
            $wechat_appid = $wechat_open_platform_config['wechat_appid'];
            $mch_id = $wechat_payment_config['mch_id'];
            $mch_cert_serial = $wechat_payment_config['mch_cert_serial'];
            $mch_private_key = Storage::disk('local_mch')->path($wechat_payment_config['mch_private_key_path']);
            $platform_cert = Storage::disk('local_mch')->path($wechat_payment_config['mch_platform_cert_path']);
            $notify_params = $order_interface_config['wechat_platform_config_id'];
            $notify_params_encode = simpleEncode($notify_params);
            $notify_url = getenv("APP_URL")."/v1/order/callback/wechat/".$notify_params_encode;

            $wechat_pay_service = new WechatPayService(
                $wechat_appid, 
                $mch_id, 
                $mch_cert_serial, 
                $mch_private_key, 
                $platform_cert,
                $notify_url
            );
            
            $ret = $wechat_pay_service->createAppOrder($order_crt_data['oid'], $order_crt_data['payment_amount'], $product['name']);
            $prepay_id = $ret['prepay_id'];
            $this->createOrder($order_crt_data);
            return $this->buildWechatPayReturn($prepay_id, $wechat_appid, $mch_id, $mch_private_key, $order_crt_data['oid']);
        } catch (\Exception $e) {
            Log::channel('order')->error($e->getMessage(), ['http_code' => $e->getCode(), 'trace' => $e->getTraceAsString()]);
            throw new BusinessException('create wechat order failed', 400250);
        }
    }

    protected function buildWechatPayReturn(string $prepayId, $wechat_appid, $mch_id, $mch_private_key, $oid)
    {
        $timestamp = time();
        $nonce_str = Str::random(10);
        $sign = WechatPayService::generateSign($wechat_appid, $timestamp, $nonce_str, $prepayId, $mch_private_key);

        return json([
            'code' => config('const.request_success'),
            'msg' => 'success',
            'data' => [
                'oid' => $oid,
                'appid' => $wechat_appid,
                'partnerid' => $mch_id,
                'prepay_id' => $prepayId,
                'package' => 'Sign=WXPay',
                'noncestr' => $nonce_str,
                'timestamp' => $timestamp,
                'sign' => $sign,
            ]
        ]);
    }
    
    private function orderBZLogic($order, $product)
    {
        $order_bz_service = new OrderBZLogicService($order, $product);
        $order_bz_service->orderBZLogic();
    }

    public static function refundOrderBZLogic($order)
    {
        if($order['refund_type'] == Order::REFUND_TYPE_ONLY) {
            return;
        }
        $user_model = new User();
        $user = $user_model->getUserInfoByUid($order['uid']);
        if(empty($user)){
            throw new \Exception('用户不存在');
        }

        $product_model = new Product();
        $product = $product_model->getProductInfoByPid($order['product_id'], $order['app_key']);
        if(empty($product)){
            throw new \Exception('产品不存在');
        }

        switch($product['type']) {
            case Product::TYPE_MEMBER_DURATION:
                $vip_expired_at = null;
                if($user['vip_expired_at']) {
                    $vip_time_left = strtotime($user['vip_expired_at']) - $product['function_value'] * 24 * 60 * 60;
                    if($vip_time_left > time()) {
                        $vip_expired_at = date('Y-m-d H:i:s', $vip_time_left);
                    }
                }
                $user_model->updateUserInfoByUid($order['uid'], ['vip_expired_at' => $vip_expired_at]);
                break;
            case Product::TYPE_MEMBER_FOREVER:
                $user_model->updateUserInfoByUid($order['uid'], ['is_forever_vip' => 0]);
                break;
            default:
                throw new \Exception('产品类型错误');
        }
    }

    private function memberDurationOrderBZLogic($order, $product)
    {
        $vip_duration = $product['function_value']*24*60*60;
        $user_model = new User();
        $user = $user_model->getUserInfoByUid($order['uid']);
        if(empty($user)){
            return false;
        }
        if(strtotime($user['vip_expired_at']) > time()){
            $user_model->updateUserInfoByUid($order['uid'], ['vip_expired_at' => date('Y-m-d H:i:s', strtotime($user['vip_expired_at']) + $vip_duration)]);
        }else{
            $user_model->updateUserInfoByUid($order['uid'], ['vip_expired_at' => date('Y-m-d H:i:s', time() + $vip_duration)]);
        }
        return true;
    }
    
    private function memberForeverOrderBZLogic($order)
    {
        $user_model = new User();
        $user = $user_model->getUserInfoByUid($order['uid']);
        if(empty($user)){
            return false;
        }
        $user_model->updateUserInfoByUid($order['uid'], ['is_forever_vip' => 1]);
        return true;
    }

    private function memberCustomOrderBZLogic($order, $product)
    {
        //todo
    }

    public function myOrder(Request $request)
    {
        $token_info = $request->token_info;
        $uid = $token_info['uid'];
        $need_product_info = $request->input('need_product_info', 0);

        $validate = new OrderList();
        if (!$validate->check($request->all())) {
            return json($validate->getErrorInfo());
        }

        $pay_channel = empty($request->input('pay_channel')) ? null : $request->input('pay_channel');
        $status = empty($request->input('status')) ? null : $request->input('status');
        $limit = $request->input('limit', 10);
        
        $order_model = new Order();
        $orders = $order_model->getOrdersByUid($uid, $pay_channel, $status, $limit);

        $pids = [];
        foreach($orders as &$order){
            unset($order['app_key']);
            $pids[] = $order['product_id'];
        }
        $pids = array_unique($pids);

        if($need_product_info == 1){
            $product_model = new Product();
            $products = $product_model->getProductsByPids($pids);
            $products_map = collect($products)->keyBy('pid')->toArray();
            foreach($orders as &$order){
                $order['product_info'] = $products_map[$order['product_id']];
            }
        }
        
        return json(['code' => config('const.request_success'), 'msg' => 'success', 'data' => $orders]);
    }

    public function wechatRefundCallback(Request $request, $encodeNotifyParams)
    {
        try{
            $wechat_payment_config_id = simpleDecode($encodeNotifyParams);

            Log::channel('order')->info('微信退款回调', [
                'wechat_payment_config_id' => $wechat_payment_config_id, 
                'headers' => $request->header(), 
                'body' => $request->rawBody()
            ]);

            $wechat_payment_config_model = new WechatPaymentConfig();
            $wechat_payment_config = $wechat_payment_config_model->getWechatPaymentConfig(
                $wechat_payment_config_id, 
            );
            if(empty($wechat_payment_config)){
                throw new \Exception('商户配置错误:'.$wechat_payment_config_id);
            }

            $inWechatpaySignature = $request->header('Wechatpay-Signature');
            $inWechatpayTimestamp = $request->header('Wechatpay-Timestamp');
            $inWechatpayNonce = $request->header('Wechatpay-Nonce');
            // $inWechatpaySerial = request()->header('Wechatpay-Serial');
            $inBody = $request->rawBody();
            
            $api_v3_key = $wechat_payment_config['mch_api_v3_secret'];
            $platform_public_key_file = Storage::disk('local_mch')->path($wechat_payment_config['mch_platform_cert_path']);
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

                Log::channel('order')->info('微信退款回调', [
                    'in_body_resource_array' => $in_body_resource_array
                ]);

                $oid = $in_body_resource_array['out_trade_no'];
                
                // 查找并更新订单
                $order_model = new Order();
                $order = $order_model->getOrderInfoByOid($oid);
                if(empty($order)){
                    throw new \Exception('订单不存在:'.$oid);
                }

                if($order['status'] == Order::STATUS_REFUNDED) {
                    return response('SUCCESS');
                }
                
                // 更新订单状态
                if ($in_body_resource_array['refund_status'] == 'SUCCESS') {
                    $order_model->updateOrderInfoByOid($oid, [
                        'status' => Order::STATUS_REFUNDED,
                        'refund_time' => isset($in_body_resource_array['success_time']) 
                            ? Carbon::parse($in_body_resource_array['success_time'])->format('Y-m-d H:i:s') 
                            : null
                    ]);
                    self::refundOrderBZLogic($order);
                } else {
                    $order_model->updateOrderInfoByOid($oid, [
                        'status' => Order::STATUS_REFUND_FAILED
                    ]);
                    
                    // 记录日志
                    Log::channel('refund')->error('微信退款失败', [
                        'order_id' => $order['oid'],
                        'refund_id' => $order['refund_id'],
                        'result' => $in_body_resource_array
                    ]);
                }
                
                return response('SUCCESS');
            }else{
                throw new \Exception('回调验证未通过');
            }
        }catch(\Throwable $e){
            Log::channel('order')->error('微信退款回调失败', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return new Response(500, [], json_encode([
                'code' => 'FAIL',
                'message' => $e->getMessage(),
            ]));
        }
    }
}