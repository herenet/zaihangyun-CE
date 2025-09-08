<?php

namespace app\controller\api;

use Exception;
use support\Db;
use support\Log;
use support\Cache;
use app\model\Order;
use support\Request;
use support\Response;
use app\model\IAPConfig;
use app\model\AppleOrder;
use app\model\IAPProduct;
use app\validate\OrderInfo;
use app\validate\OrderList;
use Webman\RedisQueue\Client;
use Webman\RateLimiter\Limiter;
use app\model\AppleDevS2SConfig;
use app\model\AppleNotification;
use app\validate\CreateAppleOrder;
use app\validate\VerifyAppleOrder;
use app\model\OrderInterfaceConfig;
use app\service\AppleReceiptService;
use app\service\OrderBZLogicService;
use support\exception\BusinessException;
use Readdle\AppStoreServerAPI\Environment;
use Readdle\AppStoreServerAPI\ResponseBodyV2;
use Readdle\AppStoreServerAPI\Exception\AppStoreServerNotificationException;

class AppleOrderController
{

    public $noNeedAuth = ['notify', 'getAppleCallbackVerifyStatus'];

    public const APPLE_CALLBACK_VERIFY_CACHE_KEY = 'apple_callback_verify_cache|{uuid}';
    public const APPLE_CALLBACK_VERIFY_CACHE_TTL = 60;
    /**
     * 创建苹果订单
     * @param Request $request
     * @return \support\Response
     */
    public function create(Request $request)
    {
        $token_info = $request->token_info;
        $appkey = $token_info['app_key'];
        $uid = $token_info['uid'];
        
        // 限流：每个用户1秒最多1条订单
        Limiter::check($uid, 1, 1, 'operation too frequent');

        $validate = new CreateAppleOrder();
        if (!$validate->check($request->all())) {
            return json($validate->getErrorInfo());
        }
        
        $pid = $request->input('pid');
        $apple_product_id = $request->input('apple_product_id');
        $environment = $request->input('environment', AppleOrder::ENVIRONMENT_PRODUCTION);

        // 验证产品信息
        $iap_product_model = new IAPProduct();
        $product = $iap_product_model->getProductInfoByPid($pid, $appkey);
        if(empty($product)){
            return json(['code' => 400199, 'msg' => 'product not found']);
        }

        if($product['sale_status'] != IAPProduct::STATUS_ON){
            return json(['code' => 400198, 'msg' => 'product is not on sale']);
        }

        // 验证苹果产品ID是否匹配
        if($product['iap_product_id'] !== $apple_product_id){
            return json(['code' => 400197, 'msg' => 'apple product id mismatch']);
        }

        // 🔥 重复购买检查：只有消耗型产品允许重复购买
        if($product['apple_product_type'] != IAPProduct::PRODUCT_TYPE_CONSUMABLE){
            $duplicate_check = $this->checkDuplicatePurchase($uid, $apple_product_id, $product['apple_product_type'], $appkey);
            if($duplicate_check){
                return $duplicate_check;
            }
        }

        $order_interface_config_model = new OrderInterfaceConfig();
        $order_interface_config = $order_interface_config_model->getOrderInterfaceConfigByAppKey($appkey);
        if(empty($order_interface_config)){
            return json(['code' => 400196, 'msg' => 'order interface config not found']);
        }

        if($order_interface_config['switch'] == 0){
            return json(['code' => 400195, 'msg' => 'order interface not enabled']);
        }

        if($order_interface_config['suport_apple_pay'] == 0){
            return json(['code' => 400194, 'msg' => 'apple iap not enabled']);
        }

        $iap_config_model = new IAPConfig();
        $iap_config = $iap_config_model->getIAPConfig($appkey);
        if(empty($iap_config)){
            return json(['code' => 400193, 'msg' => 'iap config not found']);
        }
        
        if($iap_config['interface_check'] == 0){
            return json(['code' => 400192, 'msg' => 'iap interface check has not passed']);
        }

        try{
            // 生成订单号
            $order_id = generateOrderId(Order::PAY_CHANNEL_APPLE, $order_interface_config['oid_prefix']);

            // 确定产品类型
            $product_type = AppleOrder::getAppleProductType($product);

            $order_crt_data = [
                'oid' => $order_id,
                'app_key' => $appkey,
                'uid' => $uid,
                'product_id' => $product['pid'],
                'apple_product_id' => $apple_product_id,
                'product_type' => $product_type,
                'amount' => $product['sale_price'],
                'payment_status' => AppleOrder::PAYMENT_STATUS_PENDING,
                'environment' => $environment,
                'data_source' => AppleOrder::DATA_SOURCE_RECEIPT,
            ];

            // 如果是订阅类型，设置订阅状态
            if(AppleOrder::isSubscriptionProduct($product_type)){
                $order_crt_data['subscription_status'] = null; // 创建时暂不设置订阅状态
            }

            $apple_order_model = new AppleOrder();
            $order = $apple_order_model->createOrder($order_crt_data);

            return json([
                'code' => config('const.request_success'), 
                'msg' => 'success', 
                'data' => [
                    'oid' => $order['oid'],
                    'apple_product_id' => $apple_product_id,
                    'amount' => $product['sale_price'],
                    'environment' => $environment,
                ]
            ]);

        }catch(BusinessException $e){
            return json(['code' => $e->getCode(), 'msg' => $e->getMessage()]);
        }catch(\Exception $e){
            Log::channel('order')->error('create apple order failed', [
                'error' => $e->getMessage(), 
                'trace' => $e->getTraceAsString(),
                'uid' => $uid,
                'pid' => $pid,
                'apple_product_id' => $apple_product_id
            ]);
            return json(['code' => 400250, 'msg' => 'create apple order failed']);
        }
    }

    /**
     * 验证苹果凭证并更新订单
     * @param Request $request
     * @return \support\Response
     */
    public function verify(Request $request)
    {
        $token_info = $request->token_info;
        $uid = $token_info['uid'];
        $appkey = $token_info['app_key'];

        $validate = new VerifyAppleOrder();
        if (!$validate->check($request->all())) {
            return json($validate->getErrorInfo());
        }

        $oid = $request->input('oid');
        $receipt_data = $request->input('receipt_data');
        $transaction_id = $request->input('transaction_id', '');

        Log::channel('order')->info('verify apple order', [
            'oid' => $oid,
            'transaction_id' => $transaction_id,
            'uid' => $uid,
            'appkey' => $appkey,
            'receipt_data' => $receipt_data
        ]);

        try {
            // 验证订单是否存在且属于当前用户
            $apple_order_model = new AppleOrder();
            $order = $apple_order_model->getOrderInfoByOidAndUid($oid, $uid);
            if(empty($order)){
                throw new BusinessException('order not found', 400199);
            }

            $iap_config_model = new IAPConfig();
            $iap_config = $iap_config_model->getIAPConfig($appkey);
            if(empty($iap_config)){
                throw new BusinessException('iap config not found', 400196);
            }

            if($iap_config['apple_dev_s2s_config_id'] == 0){
                throw new BusinessException('apple dev s2s config not found', 400195);
            }

            $apple_dev_s2s_config_model = new AppleDevS2SConfig();
            $apple_dev_s2s_config = $apple_dev_s2s_config_model->getAppleDevS2SConfig($iap_config['apple_dev_s2s_config_id']);
            if(empty($apple_dev_s2s_config)){
                throw new BusinessException('apple dev s2s config not found', 400194);
            }

            $configured_bundle_id = $iap_config['bundle_id'] ?? '';
            if(empty($configured_bundle_id)){
                throw new BusinessException('bundle id not configured', 400191);
            }

            $serverApiConfig = [
                'issuer_id' => $apple_dev_s2s_config['issuer_id'],
                'key_id' => $apple_dev_s2s_config['key_id'],
                'bundle_id' => $iap_config['bundle_id'], // 从IAP配置中获取
                'p8_cert_content' => $apple_dev_s2s_config['p8_cert_content'], // 或创建临时文件
                'environment' => $order['environment']
            ];

            // 验证苹果凭证（添加shared_secret支持）
            $shared_secret = $iap_config['shared_secret'] ?? null;
            $receipt_result = AppleReceiptService::verifyReceipt($receipt_data, $order['environment'], $transaction_id, $serverApiConfig, $shared_secret);
            
            if(!$receipt_result['success']){
                // 只记录日志，不更新订单状态
                Log::channel('order')->warning('receipt verification failed', [
                    'oid' => $oid,
                    'error' => $receipt_result['error'],
                    'transaction_id' => $transaction_id
                ]);
                
                throw new BusinessException($receipt_result['error'], 400193);
            }

            // 验证成功后，首先检查transaction_id是否一致
            $receipt_info = $receipt_result['data'];

            $existing_order = $apple_order_model->getOrderByTransactionId($transaction_id, $appkey);
            if(!empty($existing_order) && $existing_order['oid'] != $oid){
                $apple_order_model->deleteOrder($oid, $appkey);
                $apple_order_model->updateOrderInfoByOid($existing_order['oid'], [
                    'uid' => $uid,
                    'oid' => $oid,
                ]);
                $order = $apple_order_model->getOrderInfoByOidAndUid($oid, $uid);
            }

            // 检查订单状态
            if($order['payment_status'] == AppleOrder::PAYMENT_STATUS_SUCCESS){
                if($order['transaction_id'] == $transaction_id){
                    return json([
                        'code' => config('const.request_success'),
                        'msg' => 'success',
                        'data' => [
                            'oid' => $oid,
                            'payment_status' => AppleOrder::PAYMENT_STATUS_SUCCESS,
                            'transaction_id' => $transaction_id,
                        ]
                    ]);
                }else{                    
                    throw new BusinessException('order already verified, but transaction id mismatch', 400198);
                }
            }

            if($order['payment_status'] == AppleOrder::PAYMENT_STATUS_FAILED){
                throw new BusinessException('order payment status is failed', 400197);
            }
            
            // 从Receipt验证结果中获取bundle_id
            $receipt_bundle_id = $receipt_info['bundle_id'] ?? '';
            if(empty($receipt_bundle_id)){
                throw new BusinessException('bundle id not found in receipt', 400190);
            }
            
            // 比较bundle_id
            if($receipt_bundle_id !== $configured_bundle_id){
                Log::channel('order')->error('Bundle ID mismatch', [
                    'oid' => $oid,
                    'app_key' => $appkey,
                    'receipt_bundle_id' => $receipt_bundle_id,
                    'configured_bundle_id' => $configured_bundle_id
                ]);

                throw new BusinessException('bundle id mismatch', 400189);
            }

            // 验证成功，处理订单
            $this->processVerifiedOrder($order, $receipt_info, $apple_order_model, $receipt_result, $transaction_id);

            return json([
                'code' => config('const.request_success'),
                'msg' => 'success',
                'data' => [
                    'oid' => $oid,
                    'payment_status' => AppleOrder::PAYMENT_STATUS_SUCCESS,
                    'transaction_id' => $receipt_info['transaction_id'] ?? $transaction_id,
                ]
            ]);

        } catch(BusinessException $e){
            return json(['code' => $e->getCode(), 'msg' => $e->getMessage()]);
        } catch(\Exception $e){
            Log::channel('order')->error('verify apple order failed', [
                'error' => $e->getMessage(), 
                'trace' => $e->getTraceAsString(),
                'oid' => $oid,
                'uid' => $uid
            ]);
            return json(['code' => 400250, 'msg' => 'verify apple order failed']);
        }
    }

    /**
     * 获取订单信息
     * @param Request $request
     * @return \support\Response
     */
    public function info(Request $request)
    {
        $token_info = $request->token_info;
        $uid = $token_info['uid'];

        $validate = new OrderInfo();
        if (!$validate->check($request->all())) {
            return json($validate->getErrorInfo());
        }

        $oid = $request->input('oid');
        $need_product_info = $request->input('need_product_info', 0);

        $apple_order_model = new AppleOrder();
        $order = $apple_order_model->getOrderInfoByOidAndUid($oid, $uid);
        if(empty($order)){
            return json(['code' => 400199, 'msg' => 'order not found']);
        }

        if($need_product_info == 1){
            $iap_product_model = new IAPProduct();
            $product = $iap_product_model->getProductInfoByPid($order['product_id'], $order['app_key']);
            $order['product_info'] = $product;
        }

        // 移除敏感信息
        unset($order['app_key']);

        return json(['code' => config('const.request_success'), 'msg' => 'success', 'data' => $order]);
    }

    /**
     * 获取用户订单列表
     * @param Request $request
     * @return \support\Response
     */
    public function myOrder(Request $request)
    {
        $token_info = $request->token_info;
        $uid = $token_info['uid'];
        $need_product_info = $request->input('need_product_info', 0);

        $validate = new OrderList();
        if (!$validate->check($request->all())) {
            return json($validate->getErrorInfo());
        }

        $payment_status = empty($request->input('payment_status')) ? null : $request->input('payment_status');
        $subscription_status = empty($request->input('subscription_status')) ? null : $request->input('subscription_status');
        $limit = $request->input('limit', 10);
        
        $apple_order_model = new AppleOrder();
        $orders = $apple_order_model->getOrdersByUid($uid, $payment_status, $subscription_status, $limit);

        $pids = [];
        foreach($orders as &$order){
            unset($order['app_key']);
            $pids[] = $order['product_id'];
        }
        $pids = array_unique($pids);

        if($need_product_info == 1){
            $iap_product_model = new IAPProduct();
            $products = $iap_product_model->getProductsByPids($pids);
            $products_map = collect($products)->keyBy('pid')->toArray();
            foreach($orders as &$order){
                $order['product_info'] = $products_map[$order['product_id']] ?? null;
            }
        }
        
        return json(['code' => config('const.request_success'), 'msg' => 'success', 'data' => $orders]);
    }

    /**
     * 处理验证成功的订单
     * @param array $order
     * @param array $receipt_info
     * @param AppleOrder $apple_order_model
     * @param array $receipt_result 完整的验证结果，包含environment_used等信息
     * @param string $client_transaction_id 客户端传递的transaction_id
     * @throws Exception
     */
    private function processVerifiedOrder(array $order, array $receipt_info, AppleOrder $apple_order_model, array $receipt_result, string $client_transaction_id = '')
    {
        try {
            Db::beginTransaction();
            
            // 检查是否发生了环境切换（21007自动切换）
            $environment_used = $receipt_result['environment_used'] ?? $order['environment'];
            $environment_switched = false;
            if (strtolower($environment_used) !== strtolower($order['environment'])) {
                $environment_switched = true;
                Log::channel('order')->info('Environment switched during verification, updating order environment', [
                    'oid' => $order['oid'],
                    'original_environment' => $order['environment'],
                    'verified_environment' => $environment_used,
                    'transaction_id' => $receipt_info['transaction_id'] ?? ''
                ]);
            }
            
            // 使用客户端传递的transaction_id，如果没有则使用receipt中的
            $transaction_id_for_db = !empty($client_transaction_id) ? $client_transaction_id : ($receipt_info['transaction_id'] ?? '');
            
            // 更新订单信息
            $update_data = [
                'payment_status' => AppleOrder::PAYMENT_STATUS_SUCCESS,
                'transaction_id' => $transaction_id_for_db,  // 使用用户上报的
                'original_transaction_id' => $receipt_info['original_transaction_id'] ?? '',
                'purchase_date' => $receipt_info['purchase_date'] ?? null,
                'original_purchase_date' => $receipt_info['original_purchase_date'] ?? null,
            ];
            
            // 如果发生了环境切换，同步更新订单的environment字段
            if ($environment_switched) {
                $update_data['environment'] = strtolower($environment_used) == 'sandbox' ? Environment::SANDBOX : Environment::PRODUCTION;
            }

            // 处理订阅相关字段
            if(AppleOrder::isSubscriptionProduct($order['product_type'])){
                $update_data['subscription_status'] = AppleOrder::SUBSCRIPTION_STATUS_ACTIVE;
                $update_data['expires_date'] = $receipt_info['expires_date'] ?? null;
                $update_data['is_trial_period'] = $receipt_info['is_trial_period'] ?? 0;
                $update_data['is_in_intro_offer_period'] = $receipt_info['is_in_intro_offer_period'] ?? 0;
                $update_data['auto_renew_status'] = $receipt_info['auto_renew_status'] ?? null;
                $update_data['auto_renew_product_id'] = $receipt_info['auto_renew_product_id'] ?? null;
            }

            // 处理取消日期
            if(isset($receipt_info['cancellation_date'])){
                $update_data['cancellation_date'] = $receipt_info['cancellation_date'];
                if(AppleOrder::isSubscriptionProduct($order['product_type'])){
                    $update_data['subscription_status'] = AppleOrder::SUBSCRIPTION_STATUS_CANCELED;
                }
            }

            $update_rs = $apple_order_model->updateOrderInfoByOid($order['oid'], $update_data);
            if($update_rs === false){
                throw new Exception('update order info failed oid:'.$order['oid']);
            }

            // 执行业务逻辑 - 使用合并后的订单数据，包含Apple的过期时间
            $iap_product_model = new IAPProduct();
            $product_info = $iap_product_model->getProductInfoByPid($order['product_id'], $order['app_key']);
            if(empty($product_info)){
                throw new Exception('product not found pid:'.$order['product_id']);
            }

            // 合并订单数据和更新数据，确保业务逻辑能获取到Apple的过期时间
            $orderForBZ = array_merge($order, $update_data);
            $orderBZLogicService = new OrderBZLogicService($orderForBZ, $product_info);
            $rs = $orderBZLogicService->orderBZLogic();
            if($rs === false){
                throw new Exception('order bz logic failed oid:'.$order['oid']);
            }
            Db::commit();
        } catch (Exception $e) {
            Db::rollBack();
            throw $e;
        }
    } 

    /**
     * Apple App Store Server Notifications V2 回调处理
     * 处理来自Apple的服务器到服务器通知，包括购买、续费、退款等事件
     * 
     * @param Request $request HTTP请求对象
     * @param string $encodeNotifyParams 编码的通知参数，包含appkey
     * @return Response JSON响应
     */
    public function notify(Request $request, $params)
    {
        $rawNotification = $request->rawBody();

        Log::channel('order')->info('apple notify', [
            'headers' => $request->header(), 
            'params' => $params, 
            'raw_data' => $rawNotification,
            'server_time' => date('Y-m-d H:i:s'),
            'server_timestamp' => time(),
            'timezone' => date_default_timezone_get(),
            'php_time' => date('c'), // ISO 8601格式，包含时区信息
        ]);

        try{
            // 解码通知参数，获取appkey
            $appkey = simpleDecode($params);

            // 使用官方库解析通知
            try {
                $responseBodyV2 = ResponseBodyV2::createFromRawNotification($rawNotification);
            } catch (AppStoreServerNotificationException $e) {
                Log::channel('order')->error('apple notify parse failed', ['error' => $e->getMessage()]);
                // 格式错误，返回200避免无意义重试
                return json(['code' => config('const.request_success'), 'msg' => 'success']);
            }

            // 处理通知 - 这里的错误应该让Apple重试
            $this->processAppleNotificationV2($responseBodyV2, $appkey);

            // 成功处理，返回200
            return json(['code' => config('const.request_success'), 'msg' => 'success']);

        }catch(\Exception $e){
            Log::channel('order')->error('apple order notify failed', [
                'error' => $e->getMessage(),
                'encode_params' => $params
            ]);
            
            // 系统错误，返回500让Apple重试
            return response('Internal Server Error', 500);
        }
    }

    /**
     * 处理Apple通知的主要逻辑 - 使用官方库
     * 根据通知类型分发到对应的处理方法
     * 
     * @param ResponseBodyV2 $responseBodyV2 通知对象
     * @param string $appkey 应用标识
     * @throws \Exception 处理失败时抛出异常
     */
    private function processAppleNotificationV2(ResponseBodyV2 $responseBodyV2, string $appkey)
    {
        $notificationType = $responseBodyV2->getNotificationType();
        $subtype = $responseBodyV2->getSubtype();

        if($notificationType === ResponseBodyV2::NOTIFICATION_TYPE__TEST){
            $this->handleTestV2($responseBodyV2, $appkey);
            return;
        }

        // 获取交易信息
        $appMetadata = $responseBodyV2->getAppMetadata();
        $transactionInfo = $appMetadata->getTransactionInfo();
        $renewalInfo = $appMetadata->getRenewalInfo();

        if (empty($transactionInfo)) {
            Log::channel('order')->error('apple notify missing transaction info');
            return;
        }

        $transactionId = $transactionInfo->getTransactionId();
        $originalTransactionId = $transactionInfo->getOriginalTransactionId();
        $notificationUUID = $responseBodyV2->getNotificationUUID();

        // 保存通知记录
        $notificationId = $this->saveNotificationRecordV2($responseBodyV2, $transactionId, $originalTransactionId, $appkey, $notificationUUID);
        if (empty($notificationId)) {
            Log::channel('order')->error('apple notify save notification record failed');
            return;
        }

        Client::send('verify-apple-notify', [
            'transactionInfo' => $transactionInfo ? $transactionInfo->jsonSerialize() : null,
            'renewalInfo' => $renewalInfo ? $renewalInfo->jsonSerialize() : null,
            'appkey' => $appkey,
            'notification_type' => $notificationType,
            'subtype' => $subtype,
            'notification_id' => $notificationId,
            'transaction_id' => $transactionId
        ], 30);
    }

    public function getAppleCallbackVerifyStatus(Request $request)
    {
        if(empty($request->input('uuid'))){
            return json(['status' => false, 'message' => 'uuid is required']);
        }

        $uuid = $request->input('uuid');
        $cache_key = str_replace('{uuid}', $uuid, self::APPLE_CALLBACK_VERIFY_CACHE_KEY);
        $call_back_verify_status = Cache::get($cache_key);
        Log::channel('order')->info('苹果IAP回调验证状态', ['cache_key' => $cache_key, 'call_back_verify_status' => $call_back_verify_status]);
        if(!$call_back_verify_status) {
            return json([
                'status' => false,
                'waiting' => false,
                'message' => '验证失败: 验证超时',
            ]);
        }
        return json($call_back_verify_status);
    }

    private function handleTestV2(ResponseBodyV2 $responseBodyV2, string $appkey)
    { 
        try {
            // TEST通知直接从notificationData中获取信息，不需要解析JWT
            $notification_uuid = $responseBodyV2->getNotificationUUID();
            $bundle_id = $responseBodyV2->getAppMetadata()->getBundleId();
            $environment = $responseBodyV2->getAppMetadata()->getEnvironment();

            if (empty($notification_uuid) || empty($bundle_id)) {
                Log::channel('order')->error('TEST通知缺少必要字段', [
                    'notificationData' => $responseBodyV2
                ]);
                return;
            }

            $cache_key = str_replace('{uuid}', $notification_uuid, self::APPLE_CALLBACK_VERIFY_CACHE_KEY);
            $call_back_verify_status = Cache::get($cache_key);
            Log::channel('order')->info('苹果IAP TEST通知处理', [
                'cache_key' => $cache_key,
                'call_back_verify_status' => $call_back_verify_status
            ]);
            if ($call_back_verify_status) {
                if ($call_back_verify_status['bundle_id'] == $bundle_id) {
                    $call_back_verify_status['status'] = true;
                    $call_back_verify_status['waiting'] = false;
                    $call_back_verify_status['message'] = '回调验证成功';
                } else {
                    $call_back_verify_status['status'] = false;
                    $call_back_verify_status['waiting'] = false;
                    $call_back_verify_status['message'] = '回调验证失败，Bundle ID不匹配';
                }

                Log::channel('order')->info($call_back_verify_status['message'], [
                    'notification_uuid' => $notification_uuid,
                    'bundle_id' => $bundle_id,
                    'environment' => $environment
                ]);

                Cache::set($cache_key, $call_back_verify_status, self::APPLE_CALLBACK_VERIFY_CACHE_TTL);
            }
            
        } catch (\Exception $e) {
            Log::channel('order')->error('苹果IAP TEST通知处理失败', [
                'error' => $e->getMessage(),
                'notificationData' => $responseBodyV2
            ]);
        }
    }

    /**
     * 保存Apple通知记录到数据库
     * 用于审计和重复通知检测
     * 
     * @param ResponseBodyV2 $responseBodyV2 通知对象
     * @param string $transactionId 交易ID
     * @param string $originalTransactionId 原始交易ID
     * @param string $appkey 应用标识
     * @param string|null $notificationUUID 通知UUID（可选）
     * @throws \Exception 保存失败时抛出异常，触发Apple重试
     */
    private function saveNotificationRecordV2(ResponseBodyV2 $responseBodyV2, string $transactionId, string $originalTransactionId, string $appkey, ?string $notificationUUID = null) : ?int
    {
        try {
            $apple_notification_model = new AppleNotification();
            $notification_record = [
                'app_key' => $appkey,
                'notification_uuid' => $notificationUUID,
                'notification_type' => $responseBodyV2->getNotificationType(),
                'subtype' => $responseBodyV2->getSubtype(),
                'transaction_id' => $transactionId,
                'original_transaction_id' => $originalTransactionId,
                'environment' => $responseBodyV2->getAppMetadata()->getEnvironment(),
                'notification_data' => json_encode($responseBodyV2),
                'processed' => AppleNotification::PROCESSED_NO,
            ];

            $result = $apple_notification_model->createNotification($notification_record);
            return $result ? $result->id : null;

        } catch (\Exception $e) {
            Log::channel('order')->error('save notification record failed', [
                'error' => $e->getMessage(),
                'type' => $responseBodyV2->getNotificationType(),
                'transactionId' => $transactionId
            ]);
            // 数据库错误应该让Apple重试
            throw new \Exception('Failed to save notification record: ' . $e->getMessage());
        }
    }

    /**
     * 检查重复购买
     * 根据苹果官方产品特性进行重复购买检查
     * 
     * @param int $uid 用户ID
     * @param string $apple_product_id 苹果产品ID
     * @param int $product_type 产品类型
     * @param string $appkey 应用标识
     * @return array|null 如果有重复购买返回错误响应，否则返回null
     */
    private function checkDuplicatePurchase(int $uid, string $apple_product_id, int $product_type, string $appkey)
    {
        $apple_order_model = new AppleOrder();
        
        switch ($product_type) {
            case AppleOrder::PRODUCT_TYPE_CONSUMABLE:
                // 消耗型产品：允许重复购买，无需检查
                return null;
                
            case AppleOrder::PRODUCT_TYPE_NON_CONSUMABLE:
                // 非消耗型产品：一次性购买，不允许重复购买
                return $this->checkNonConsumableDuplicate($uid, $apple_product_id, $appkey, $apple_order_model);
                
            case AppleOrder::PRODUCT_TYPE_AUTO_RENEWABLE:
                // 自动续期订阅：检查是否有活跃订阅
                return $this->checkAutoRenewableSubscriptionDuplicate($uid, $apple_product_id, $appkey, $apple_order_model);
                
            case AppleOrder::PRODUCT_TYPE_NON_RENEWING:
                // 非续期订阅：检查是否有未过期的订阅
                return $this->checkNonRenewingSubscriptionDuplicate($uid, $apple_product_id, $appkey, $apple_order_model);
                
            default:
                // 未知产品类型，为安全起见禁止重复购买
                Log::channel('order')->warning('unknown product type for duplicate check', [
                    'uid' => $uid,
                    'apple_product_id' => $apple_product_id,
                    'product_type' => $product_type,
                    'appkey' => $appkey
                ]);
                return json(['code' => 400190, 'msg' => 'unknown product type']);
        }
    }

    /**
     * 检查非消耗型产品重复购买
     * 非消耗型产品一旦购买成功就永久拥有，不允许重复购买
     * 
     * @param int $uid 用户ID
     * @param string $apple_product_id 苹果产品ID
     * @param string $appkey 应用标识
     * @param AppleOrder $apple_order_model 订单模型
     * @return array|null
     */
    private function checkNonConsumableDuplicate(int $uid, string $apple_product_id, string $appkey, AppleOrder $apple_order_model)
    {
        // 查找该用户是否已经成功购买过此非消耗型产品
        $existing_order = $apple_order_model->where([
            'uid' => $uid,
            'app_key' => $appkey,
            'apple_product_id' => $apple_product_id,
            'product_type' => AppleOrder::PRODUCT_TYPE_NON_CONSUMABLE,
            'payment_status' => AppleOrder::PAYMENT_STATUS_SUCCESS
        ])->first();
        
        if (!empty($existing_order)) {
            Log::channel('order')->info('non-consumable product already purchased', [
                'uid' => $uid,
                'apple_product_id' => $apple_product_id,
                'existing_oid' => $existing_order['oid']
            ]);
            
            return json([
                'code' => 400180, 
                'msg' => 'non-consumable product already purchased',
                'data' => [
                    'existing_oid' => $existing_order['oid'],
                    'purchase_date' => $existing_order['purchase_date']
                ]
            ]);
        }
        
        return null;
    }

    /**
     * 检查自动续期订阅重复购买
     * 自动续期订阅同一时间只能有一个活跃订阅
     * 
     * @param int $uid 用户ID
     * @param string $apple_product_id 苹果产品ID
     * @param string $appkey 应用标识
     * @param AppleOrder $apple_order_model 订单模型
     * @return array|null
     */
    private function checkAutoRenewableSubscriptionDuplicate(int $uid, string $apple_product_id, string $appkey, AppleOrder $apple_order_model)
    {
        // 查找该用户是否有活跃的自动续期订阅
        $active_subscription = $apple_order_model->where([
            'uid' => $uid,
            'app_key' => $appkey,
            'apple_product_id' => $apple_product_id,
            'product_type' => AppleOrder::PRODUCT_TYPE_AUTO_RENEWABLE,
            'payment_status' => AppleOrder::PAYMENT_STATUS_SUCCESS,
            'subscription_status' => AppleOrder::SUBSCRIPTION_STATUS_ACTIVE
        ])->where('expires_date', '>', date('Y-m-d H:i:s'))
          ->first();

        Log::channel('order')->info('active auto-renewable subscription', [
            'uid' => $uid,
            'apple_product_id' => $apple_product_id,
            'active_subscription' => $active_subscription
        ]);
        
        if (!empty($active_subscription)) {
            Log::channel('order')->info('active auto-renewable subscription exists', [
                'uid' => $uid,
                'apple_product_id' => $apple_product_id,
                'existing_oid' => $active_subscription['oid'],
                'expires_date' => $active_subscription['expires_date']
            ]);
            
            return json([
                'code' => 400181, 
                'msg' => 'active subscription already exists',
                'data' => [
                    'existing_oid' => $active_subscription['oid'],
                    'expires_date' => $active_subscription['expires_date'],
                    'auto_renew_status' => $active_subscription['auto_renew_status']
                ]
            ]);
        }
        
        return null;
    }

    /**
     * 检查非续期订阅重复购买
     * 非续期订阅在有效期内不允许重复购买同一产品
     * 
     * @param int $uid 用户ID
     * @param string $apple_product_id 苹果产品ID
     * @param string $appkey 应用标识
     * @param AppleOrder $apple_order_model 订单模型
     * @return array|null
     */
    private function checkNonRenewingSubscriptionDuplicate(int $uid, string $apple_product_id, string $appkey, AppleOrder $apple_order_model)
    {
        // 查找该用户是否有未过期的非续期订阅
        $active_subscription = $apple_order_model->where([
            'uid' => $uid,
            'app_key' => $appkey,
            'apple_product_id' => $apple_product_id,
            'product_type' => AppleOrder::PRODUCT_TYPE_NON_RENEWING,
            'payment_status' => AppleOrder::PAYMENT_STATUS_SUCCESS
        ])->where('expires_date', '>', date('Y-m-d H:i:s'))
          ->first();
        
        if (!empty($active_subscription)) {
            Log::channel('order')->info('active non-renewing subscription exists', [
                'uid' => $uid,
                'apple_product_id' => $apple_product_id,
                'existing_oid' => $active_subscription['oid'],
                'expires_date' => $active_subscription['expires_date']
            ]);
            
            return json([
                'code' => 400183, 
                'msg' => 'active non-renewing subscription exists',
                'data' => [
                    'existing_oid' => $active_subscription['oid'],
                    'expires_date' => $active_subscription['expires_date']
                ]
            ]);
        }
        
        return null;
    }
}