<?php

namespace app\controller\api;

use Exception;
use support\Log;
use support\Request;
use app\model\IAPConfig;
use app\model\AppleReceiptData;
use app\model\AppleVerifyConfig;
use app\model\OrderInterfaceConfig;
use app\service\AppleReceiptService;
use app\validate\VerifyAppleReceipt;
use app\model\AppleReceiptVerification;
use support\exception\BusinessException;

class AppleReceiptController
{

    /**
     * 验证Apple支付票据
     * 轻量级验证接口，无需用户登录，租户可直接调用验证IAP票据真实性
     * 
     * @param Request $request
     * @return \support\Response
     */
    public function verify(Request $request)
    {
        $validate = new VerifyAppleReceipt();
        if (!$validate->check($request->all())) {
            return json($validate->getErrorInfo());
        }

        $appkey = $request->input('appkey');
        $receipt_data = $request->input('receipt_data');
        $environment = $request->input('environment', 'Production');
        $transaction_id = $request->input('transaction_id', '');

        // Limiter::check($appkey, 10, 1, 'operation too frequent');//每个appkey1秒最多10条验证请求

        Log::channel('receipt')->info('Apple receipt verification request', [
            'appkey' => $appkey,
            'receipt_data' => $receipt_data,
            'environment' => $environment,
            'transaction_id' => $transaction_id
        ]);

        try {
            // 1. 检查订单接口总开关
            $order_interface_config_model = new OrderInterfaceConfig();
            $order_config = $order_interface_config_model->getOrderInterfaceConfigByAppKey($appkey);
            
            if (empty($order_config)) {
                throw new BusinessException('order interface config not found', 400300);
            }

            if ($order_config['switch'] != 1) {
                throw new BusinessException('order interface is disabled', 400301);
            }

            if ($order_config['suport_apple_verify'] != 1) {
                throw new BusinessException('apple receipt verification is disabled', 400302);
            }

            // 2. 获取Apple验票配置
            $apple_verify_config_model = new AppleVerifyConfig();
            $verify_config = $apple_verify_config_model->getVerifyConfigByAppKey($appkey);
            
            if (empty($verify_config)) {
                throw new BusinessException('apple verify config not found', 400303);
            }

            if (empty($verify_config['bundle_id'])) {
                throw new BusinessException('bundle_id not configured', 400304);
            }

            if($verify_config['subscrip_switch'] == 1 && empty($verify_config['shared_secret'])){
                throw new BusinessException('shared_secret not configured', 400305);
            }

            // 3. 计算票据哈希值
            $receipt_hash = hash('sha256', $receipt_data);

            // 4. 检查是否允许重复验证
            $verification_model = new AppleReceiptVerification();

            if ($verify_config['multiple_verify'] === 0) {
                // 不允许重复验证，检查是否已验证过
                $existing_verification = $verification_model->where([
                    'app_key' => $appkey,
                    'receipt_data_hash' => $receipt_hash
                ])->first();

                if (!empty($existing_verification)) {
                    throw new BusinessException('receipt already verified, duplicate verification not allowed', 400306);
                }
            }

            // 5. 调用Apple验证接口，传递transaction_id
            $apple_result = AppleReceiptService::verifyReceiptLegacyWithTransactionId(
                $receipt_data, 
                strtolower($environment), 
                $verify_config['shared_secret'],
                $transaction_id
            );

            // 6. 解析验证结果
            $verification_status = $apple_result['success'] ? 
                AppleReceiptVerification::VERIFICATION_STATUS_SUCCESS : 
                AppleReceiptVerification::VERIFICATION_STATUS_FAILED;

            $verification_data = [
                'id' => generateUserId(),
                'app_key' => $appkey,
                'receipt_data_hash' => $receipt_hash,
                'verification_status' => $verification_status,
                'environment' => $environment
            ];

            // 7. 验证成功时的额外处理
            if ($apple_result['success']) {
                $receipt_info = $apple_result['data'];
                $raw_data = $apple_result['raw_data'];
                
                // 验证Bundle ID
                if (!empty($receipt_info['bundle_id']) && $receipt_info['bundle_id'] !== $verify_config['bundle_id']) {
                    Log::channel('receipt')->warning('Bundle ID mismatch', [
                        'expected' => $verify_config['bundle_id'],
                        'received' => $receipt_info['bundle_id'],
                        'appkey' => $appkey
                    ]);
                    throw new BusinessException('bundle_id mismatch', 400307);
                }

                // 填充成功验证的数据
                $verification_data = array_merge($verification_data, [
                    'bundle_id' => $receipt_info['bundle_id'] ?? null,
                    'transaction_id' => $receipt_info['transaction_id'] ?? null,
                    'original_transaction_id' => $receipt_info['original_transaction_id'] ?? null,
                    'product_id' => $receipt_info['product_id'] ?? null,
                    'purchase_date' => !empty($receipt_info['purchase_date']) ? 
                        date('Y-m-d H:i:s', strtotime($receipt_info['purchase_date'])) : null,
                    'quantity' => $receipt_info['quantity'] ?? null,
                ]);
            } else {
                // 验证失败时记录错误信息
                $verification_data['apple_status_code'] = $apple_result['status'] ?? null;
                $verification_data['error_message'] = $apple_result['error'] ?? 'Unknown error';
            }

            // 8. 获取租户ID
            $iap_config_model = new IAPConfig();
            $iap_config = $iap_config_model->getIAPConfig($appkey);

            // 9. 保存验证记录
            $verification_record = $verification_model->create($verification_data);

            // 10. 保存票据数据
            $receipt_data_model = new AppleReceiptData();
            $receipt_storage_data = [
                'verification_id' => $verification_record->id,
                'app_key' => $appkey,
                'receipt_data_hash' => $receipt_hash,
                'receipt_data' => $apple_result['success'] ? 
                    json_encode($raw_data, JSON_UNESCAPED_UNICODE) : 
                    $receipt_data
            ];
            $receipt_data_model->create($receipt_storage_data);

            Log::channel('receipt')->info('Apple receipt verification completed', [
                'appkey' => $appkey,
                'verification_id' => $verification_record->id,
                'status' => $verification_status,
                'success' => $apple_result['success']
            ]);

            // 11. 返回验证结果
            return $this->formatVerificationResponse($verification_record, $apple_result);

        } catch (BusinessException $e) {
            Log::channel('receipt')->warning('Apple receipt verification business error', [
                'appkey' => $appkey,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            return json(['code' => $e->getCode(), 'msg' => $e->getMessage()]);
        } catch (\Exception $e) {
            Log::channel('receipt')->error('Apple receipt verification system error', [
                'appkey' => $appkey,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return json(['code' => 400399, 'msg' => 'receipt verification failed']);
        }
    }

    /**
     * 格式化验证响应
     * 
     * @param AppleReceiptVerification $verification
     * @param array|null $apple_result
     * @return \support\Response
     */
    private function formatVerificationResponse($verification, $apple_result = null)
    {
        if ($verification['verification_status'] == AppleReceiptVerification::VERIFICATION_STATUS_SUCCESS) {
            $response_data = [
                'verification_id' => $verification['id'],
                'status' => 'success',
                'bundle_id' => $verification['bundle_id'],
                'environment' => $verification['environment'],
                'transaction_id' => $verification['transaction_id'],
                'original_transaction_id' => $verification['original_transaction_id'],
                'product_id' => $verification['product_id'],
                'purchase_date' => $verification['purchase_date'],
                'quantity' => $verification['quantity']
            ];

            // 如果有实时验证结果，补充更多信息
            if (!empty($apple_result['data'])) {
                $receipt_info = $apple_result['data'];
                if (!empty($receipt_info['expires_date'])) {
                    $response_data['expires_date'] = $receipt_info['expires_date'];
                }
                if (!empty($receipt_info['is_trial_period'])) {
                    $response_data['is_trial_period'] = $receipt_info['is_trial_period'];
                }
                if (!empty($receipt_info['cancellation_date'])) {
                    $response_data['cancellation_date'] = $receipt_info['cancellation_date'];
                }
            }

            return json([
                'code' => 200,
                'msg' => 'success',
                'data' => $response_data
            ]);
        } else {
            return json([
                'code' => 400308,
                'msg' => 'receipt verification failed',
                'data' => [
                    'verification_id' => $verification['id'],
                    'status' => 'failed',
                    'apple_status_code' => $verification['apple_status_code'],
                    'error_message' => $verification['error_message']
                ]
            ]);
        }
    }
} 