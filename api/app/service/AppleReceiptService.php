<?php

namespace app\service;

use Exception;
use support\Log;
use GuzzleHttp\Client;
use app\model\AppleOrder;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Readdle\AppStoreServerAPI\Environment;
use Readdle\AppStoreServerAPI\ResponseBodyV2;
use Readdle\AppStoreServerAPI\TransactionInfo;
use Readdle\AppStoreServerAPI\AppStoreServerAPI;
use Readdle\AppStoreServerAPI\Exception\AppStoreServerAPIException;

class AppleReceiptService
{
    // 传统Receipt验证服务器地址
    const PRODUCTION_URL = 'https://buy.itunes.apple.com/verifyReceipt';
    const SANDBOX_URL = 'https://sandbox.itunes.apple.com/verifyReceipt';

    private $apiClient;
    private $environment;

    /**
     * 构造函数（仅用于需要 App Store Server API 的场景）
     * @param array|null $config 配置参数，如果为null则从配置文件读取
     * @throws Exception
     */
    public function __construct(array $config)
    {
        $requiredFields = ['issuer_id', 'key_id', 'bundle_id', 'p8_cert_content'];
        foreach ($requiredFields as $field) {
            if (empty($config[$field])) {
                throw new Exception("Apple App Store Server API configuration '{$field}' is missing");
            }
        }

        // 设置环境
        $this->environment = strtolower($config['environment'] ?? 'production') === 'sandbox' ? Environment::SANDBOX : Environment::PRODUCTION;

        // 初始化API客户端
        $this->apiClient = new AppStoreServerAPI(
            $this->environment,
            $config['issuer_id'],
            $config['bundle_id'],
            $config['key_id'],
            $config['p8_cert_content']
        );
    }

    /**
     * 验证苹果凭证（静态方法，优先使用传统验证）
     * @param string $receiptData base64编码的凭证数据
     * @param string $environment 环境：sandbox 或 production
     * @param string|null $transactionId 可选的交易ID，用于新API验证
     * @return array
     */
    public static function verifyReceipt(
        string $receiptData, 
        string $environment = 'production', 
        ?string $transactionId = null,
        ?array $serverApiConfig = null,
        ?string $sharedSecret = null
    ): array
    {
        try {
            // 首先尝试传统Receipt验证（已包含21007自动切换逻辑）
            $legacyResult = self::verifyReceiptLegacy($receiptData, $environment, $sharedSecret);
            
            if ($legacyResult['success']) {
                return $legacyResult;
            }

            // 只有在传统验证失败且提供了transaction_id和有效配置时，才使用Server API作为最后手段
            if (!empty($transactionId) && !empty($serverApiConfig)) {
                // 检查配置完整性
                $requiredFields = ['issuer_id', 'key_id', 'bundle_id', 'p8_cert_content'];
                $configComplete = true;
                foreach ($requiredFields as $field) {
                    if (empty($serverApiConfig[$field])) {
                        $configComplete = false;
                        break;
                    }
                }
                
                if ($configComplete) {
                    Log::channel('order')->info('Legacy receipt verification failed, trying App Store Server API as last resort', [
                        'transaction_id' => $transactionId,
                        'legacy_error' => $legacyResult['error'] ?? 'Unknown error',
                        'legacy_status' => $legacyResult['status'] ?? null,
                        'environment' => $environment,
                        'shared_secret_provided' => !empty($sharedSecret)
                    ]);
                    
                    try {
                        $service = new self($serverApiConfig);
                        $serverApiResult = $service->verifyTransactionWithServerAPI($transactionId);
                        
                        if ($serverApiResult['success']) {
                            Log::channel('order')->info('Server API verification succeeded after legacy verification failed');
                            return $serverApiResult;
                        } else {
                            Log::channel('order')->warning('Server API verification also failed', [
                                'transaction_id' => $transactionId,
                                'error' => $serverApiResult['error'] ?? 'Unknown error'
                            ]);
                        }
                    } catch (Exception $e) {
                        Log::channel('order')->warning('Server API verification exception', [
                            'transaction_id' => $transactionId,
                            'error' => $e->getMessage()
                        ]);
                    }
                } else {
                    Log::channel('order')->info('Server API configuration incomplete, skipping Server API verification', [
                        'transaction_id' => $transactionId
                    ]);
                }
            }

            // 返回传统验证的结果
            return $legacyResult;

        } catch (Exception $e) {
            Log::channel('order')->error('Apple receipt verification exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => 'Receipt verification failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * 使用App Store Server API验证交易（实例方法）
     * @param string $transactionId
     * @return array
     */
    public function verifyTransactionWithServerAPI(string $transactionId): array
    {
        try {
            // 获取交易信息
            $response = $this->apiClient->getTransactionInfo($transactionId);
            
            if (!$response) {
                return [
                    'success' => false,
                    'error' => 'Failed to get transaction info from App Store Server API',
                ];
            }

            // 解析交易信息
            $transactionInfo = $response->getTransactionInfo();
            $receiptInfo = $this->parseTransactionInfo($transactionInfo);

            return [
                'success' => true,
                'data' => $receiptInfo,
            ];

        } catch (AppStoreServerAPIException $e) {
            Log::channel('order')->error('App Store Server API error', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'transaction_id' => $transactionId
            ]);

            return [
                'success' => false,
                'error' => 'App Store Server API error: ' . $e->getMessage(),
                'code' => $e->getCode(),
            ];
        } catch (Exception $e) {
            Log::channel('order')->error('Transaction verification exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'transaction_id' => $transactionId
            ]);

            return [
                'success' => false,
                'error' => 'Transaction verification failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * 获取订阅状态（使用App Store Server API）
     * @param string $originalTransactionId
     * @return array
     */
    public function getSubscriptionStatus(string $originalTransactionId): array
    {
        try {
            $response = $this->apiClient->getAllSubscriptionStatuses($originalTransactionId);
            
            if (!$response) {
                return [
                    'success' => false,
                    'error' => 'Failed to get subscription status',
                ];
            }

            $subscriptionInfo = [
                'environment' => $response->getEnvironment(),
                'app_apple_id' => $response->getAppAppleId(),
                'bundle_id' => $response->getBundleId(),
                'data' => $response->getData(),
            ];

            return [
                'success' => true,
                'data' => $subscriptionInfo,
            ];

        } catch (AppStoreServerAPIException $e) {
            Log::channel('order')->error('Get subscription status error', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'original_transaction_id' => $originalTransactionId
            ]);

            return [
                'success' => false,
                'error' => 'Get subscription status error: ' . $e->getMessage(),
                'code' => $e->getCode(),
            ];
        }
    }

    /**
     * 传统Receipt验证（静态方法）
     * @param string $receiptData
     * @param string $environment
     * @return array
     */
    public static function verifyReceiptLegacy(string $receiptData, string $environment = 'production', ?string $sharedSecret = null): array
    {
        try {
            // 首先尝试指定环境验证
            $url = (strtolower($environment) === 'sandbox') ? self::SANDBOX_URL : self::PRODUCTION_URL;
            $result = self::sendVerifyRequest($receiptData, $url, $sharedSecret);
            
            // 如果生产环境返回21007错误（沙盒凭证），则自动尝试沙盒环境
            if ($result['status'] === 21007 && strtolower($environment) === 'production') {
                Log::channel('order')->info('Production receipt verification returned 21007 (sandbox receipt), automatically trying sandbox', [
                    'original_status' => $result['status'],
                    'original_environment' => $environment,
                    'shared_secret_provided' => !empty($sharedSecret)
                ]);
                $result = self::sendVerifyRequest($receiptData, self::SANDBOX_URL, $sharedSecret);
                $url = self::SANDBOX_URL; // 更新URL变量以便正确记录日志
                
                // 标记这是一个自动切换的结果
                if ($result['status'] === 0) {
                    $result['auto_switched_to_sandbox'] = true;
                    Log::channel('order')->info('Auto-switched to sandbox verification succeeded');
                }
            }
            
            if ($result['status'] === 0) {
                // 验证成功，解析凭证信息
                $receiptInfo = self::parseReceiptInfo($result);
                return [
                    'success' => true,
                    'data' => $receiptInfo,
                    'raw_data' => $result,
                    'environment_used' => isset($result['auto_switched_to_sandbox']) ? 'sandbox' : $environment,
                ];
            } else {
                // 验证失败
                $errorMsg = self::getErrorMessage($result['status']);
                Log::channel('order')->warning('Legacy receipt verification failed', [
                    'status' => $result['status'],
                    'error' => $errorMsg,
                    'environment' => $environment,
                    'url_used' => $url,
                    'auto_switched' => isset($result['auto_switched_to_sandbox']) ? 'yes' : 'no',
                    'shared_secret_provided' => !empty($sharedSecret)
                ]);
                
                return [
                    'success' => false,
                    'error' => $errorMsg,
                    'status' => $result['status'],
                    'environment' => $environment,
                ];
            }
            
        } catch (Exception $e) {
            Log::channel('order')->error('Legacy receipt verification exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'environment' => $environment
            ]);
            
            return [
                'success' => false,
                'error' => 'Legacy receipt verification failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * 验证苹果凭证（支持指定transaction_id）
     * @param string $receiptData base64编码的凭证数据
     * @param string $environment 环境：sandbox 或 production
     * @param string|null $sharedSecret 共享密钥
     * @param string|null $transactionId 指定的交易ID
     * @return array
     */
    public static function verifyReceiptLegacyWithTransactionId(
        string $receiptData, 
        string $environment = 'production', 
        ?string $sharedSecret = null,
        ?string $transactionId = null
    ): array
    {
        try {
            // 首先尝试指定环境验证
            $url = (strtolower($environment) === 'sandbox') ? self::SANDBOX_URL : self::PRODUCTION_URL;
            $result = self::sendVerifyRequest($receiptData, $url, $sharedSecret);

            Log::channel('order')->info('Legacy receipt with environment verification result', [
                'transaction_id' => $transactionId,
                'environment' => $environment,
                'shared_secret_provided' => !empty($sharedSecret),
                'result' => $result,
            ]);
            
            // 如果生产环境返回21007错误（沙盒凭证），则自动尝试沙盒环境
            if ($result['status'] === 21007 && strtolower($environment) === 'production') {
                Log::channel('order')->info('Production receipt verification returned 21007 (sandbox receipt), automatically trying sandbox', [
                    'original_status' => $result['status'],
                    'transaction_id' => $transactionId,
                    'original_environment' => $environment,
                    'shared_secret_provided' => !empty($sharedSecret)
                ]);
                $result = self::sendVerifyRequest($receiptData, self::SANDBOX_URL, $sharedSecret);
                $url = self::SANDBOX_URL; // 更新URL变量以便正确记录日志
                
                // 标记这是一个自动切换的结果
                if ($result['status'] === 0) {
                    $result['auto_switched_to_sandbox'] = true;
                    Log::channel('order')->info('Auto-switched to sandbox verification succeeded', [
                        'transaction_id' => $transactionId
                    ]);
                }
            }
            
            if ($result['status'] === 0) {
                // 验证成功，解析凭证信息，传递transaction_id
                $receiptInfo = self::parseReceiptInfoWithTransactionId($result, $transactionId);
                return [
                    'success' => true,
                    'data' => $receiptInfo,
                    'raw_data' => $result,
                    'environment_used' => isset($result['auto_switched_to_sandbox']) ? 'sandbox' : $environment,
                ];
            } else {
                // 验证失败
                $errorMsg = self::getErrorMessage($result['status']);
                Log::channel('order')->warning('Legacy receipt verification failed', [
                    'status' => $result['status'],
                    'error' => $errorMsg,
                    'environment' => $environment,
                    'transaction_id' => $transactionId,
                    'url_used' => $url,
                    'auto_switched' => isset($result['auto_switched_to_sandbox']) ? 'yes' : 'no',
                    'shared_secret_provided' => !empty($sharedSecret)
                ]);
                
                return [
                    'success' => false,
                    'error' => $errorMsg,
                    'status' => $result['status'],
                    'environment' => $environment,
                ];
            }
            
        } catch (Exception $e) {
            Log::channel('order')->error('Legacy receipt verification exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'transaction_id' => $transactionId,
                'environment' => $environment
            ]);
            
            return [
                'success' => false,
                'error' => 'Legacy receipt verification failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * 解析凭证信息（支持指定transaction_id）
     * @param array $verifyResult
     * @param string|null $transactionId 指定的交易ID
     * @return array
     */
    private static function parseReceiptInfoWithTransactionId(array $verifyResult, ?string $transactionId = null): array
    {
        $receipt = $verifyResult['receipt'] ?? [];
        $latestReceiptInfo = $verifyResult['latest_receipt_info'] ?? [];
        $pendingRenewalInfo = $verifyResult['pending_renewal_info'] ?? [];

        $currentTransaction = null;
        
        // 如果指定了transaction_id，优先查找指定的交易
        if (!empty($transactionId)) {
            // 先在latest_receipt_info中查找（包含所有交易）
            foreach ($latestReceiptInfo as $transaction) {
                if (isset($transaction['transaction_id']) && 
                    $transaction['transaction_id'] === $transactionId) {
                    $currentTransaction = $transaction;
                    Log::channel('receipt')->info('Found specified transaction in latest_receipt_info', [
                        'transaction_id' => $transactionId,
                        'product_id' => $transaction['product_id'] ?? null
                    ]);
                    break;
                }
            }
            
            // 如果在latest_receipt_info中没找到，再在in_app中查找
            if (empty($currentTransaction) && !empty($receipt['in_app'])) {
                foreach ($receipt['in_app'] as $transaction) {
                    if (isset($transaction['transaction_id']) && 
                        $transaction['transaction_id'] === $transactionId) {
                        $currentTransaction = $transaction;
                        Log::channel('receipt')->info('Found specified transaction in in_app', [
                            'transaction_id' => $transactionId,
                            'product_id' => $transaction['product_id'] ?? null
                        ]);
                        break;
                    }
                }
            }
            
            // 如果指定的transaction_id没找到，抛出错误
            if (empty($currentTransaction)) {
                Log::channel('receipt')->warning('Specified transaction_id not found in receipt', [
                    'transaction_id' => $transactionId,
                    'available_transactions' => array_column($latestReceiptInfo, 'transaction_id')
                ]);
                // throw new Exception("Transaction ID '{$transactionId}' not found in receipt");
            }
        } else {
            // 如果没有指定transaction_id，使用原来的逻辑（向后兼容）
            if (!empty($receipt['in_app'])) {
                $currentTransaction = $receipt['in_app'][0];
                
                if (!empty($latestReceiptInfo) && !empty($currentTransaction['transaction_id'])) {
                    $currentTransactionId = $currentTransaction['transaction_id'];
                    
                    foreach ($latestReceiptInfo as $transaction) {
                        if (isset($transaction['transaction_id']) && 
                            $transaction['transaction_id'] === $currentTransactionId) {
                            $currentTransaction = $transaction;
                            break;
                        }
                    }
                }
            } else if (!empty($latestReceiptInfo)) {
                $currentTransaction = $latestReceiptInfo[0];
            }
            
            if (empty($currentTransaction)) {
                throw new Exception('No transaction found in receipt');
            }
        }

        // 返回完整的票据数据，包含原始数据和解析后的关键字段
        $receiptInfo = [
            // 原始完整数据
            'raw_receipt_data' => $verifyResult,
            
            // 解析后的关键字段（基于找到的目标交易）
            'transaction_id' => $currentTransaction['transaction_id'] ?? '',
            'original_transaction_id' => $currentTransaction['original_transaction_id'] ?? '',
            'product_id' => $currentTransaction['product_id'] ?? '',
            'bundle_id' => $receipt['bundle_id'] ?? '',
            'purchase_date' => self::formatAppleDate($currentTransaction['purchase_date'] ?? ''),
            'original_purchase_date' => self::formatAppleDate($currentTransaction['original_purchase_date'] ?? ''),
            'quantity' => intval($currentTransaction['quantity'] ?? 1),
            
            // 当前交易信息（指定的或匹配到的交易）
            'current_transaction' => $currentTransaction,
            
            // 完整的票据信息
            'receipt' => $receipt,
            
            // 完整的最新票据信息列表
            'latest_receipt_info' => $latestReceiptInfo,
            
            // 完整的待续费信息
            'pending_renewal_info' => $pendingRenewalInfo,
        ];

        // 订阅相关字段
        if (isset($currentTransaction['expires_date'])) {
            $receiptInfo['expires_date'] = self::formatAppleDate($currentTransaction['expires_date']);
            $receiptInfo['is_trial_period'] = ($currentTransaction['is_trial_period'] ?? 'false') === 'true' ? 1 : 0;
            $receiptInfo['is_in_intro_offer_period'] = ($currentTransaction['is_in_intro_offer_period'] ?? 'false') === 'true' ? 1 : 0;
        }

        // 取消日期
        if (isset($currentTransaction['cancellation_date'])) {
            $receiptInfo['cancellation_date'] = self::formatAppleDate($currentTransaction['cancellation_date']);
        }

        // 自动续费信息（仅当找到的是订阅交易时）
        if (!empty($pendingRenewalInfo) && !empty($currentTransaction['original_transaction_id'])) {
            // 找到对应的续费信息
            foreach ($pendingRenewalInfo as $renewalInfo) {
                if (isset($renewalInfo['original_transaction_id']) && 
                    $renewalInfo['original_transaction_id'] === $currentTransaction['original_transaction_id']) {
                    $receiptInfo['auto_renew_status'] = intval($renewalInfo['auto_renew_status'] ?? 1);
                    $receiptInfo['auto_renew_product_id'] = $renewalInfo['auto_renew_product_id'] ?? '';
                    break;
                }
            }
        }

        return $receiptInfo;
    }

    /**
     * 发送验证请求到苹果服务器（静态方法）
     * @param string $receiptData
     * @param string $url
     * @return array
     * @throws Exception
     */
    private static function sendVerifyRequest(string $receiptData, string $url, ?string $sharedSecret = null): array
    {
        try {
            $client = new Client([
                'timeout' => 30,
                'verify' => true, // 启用SSL验证
            ]);

            $response = $client->post($url, [
                'json' => [
                    'receipt-data' => $receiptData,
                    'password' => $sharedSecret ?? config('apple.shared_secret', ''), // 共享密钥，用于验证订阅
                    'exclude-old-transactions' => false
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ]
            ]);

            $body = $response->getBody()->getContents();
            $result = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('JSON decode error: ' . json_last_error_msg());
            }

            return $result;

        } catch (RequestException $e) {
            // HTTP 请求异常（4xx, 5xx 状态码）
            $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 0;
            $errorMessage = $e->getMessage();
            
            Log::channel('order')->error('Apple receipt verification HTTP error', [
                'url' => $url,
                'status_code' => $statusCode,
                'error' => $errorMessage
            ]);
            
            throw new Exception("HTTP error ({$statusCode}): {$errorMessage}");
            
        } catch (GuzzleException $e) {
            // 其他 Guzzle 异常（连接超时、DNS解析失败等）
            Log::channel('order')->error('Apple receipt verification Guzzle error', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            
            throw new Exception('Request error: ' . $e->getMessage());
            
        } catch (Exception $e) {
            // 其他异常
            Log::channel('order')->error('Apple receipt verification unexpected error', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * 解析交易信息
     * @param TransactionInfo $transactionInfo
     * @return array
     */
    private function parseTransactionInfo(TransactionInfo $transactionInfo): array
    {
        $receiptInfo = [
            'transaction_id' => $transactionInfo->getTransactionId(),
            'original_transaction_id' => $transactionInfo->getOriginalTransactionId(),
            'product_id' => $transactionInfo->getProductId(),
            'bundle_id' => $transactionInfo->getBundleId(),
            'purchase_date' => $this->formatTimestamp($transactionInfo->getPurchaseDate()),
            'original_purchase_date' => $this->formatTimestamp($transactionInfo->getOriginalPurchaseDate()),
            'quantity' => $transactionInfo->getQuantity(),
        ];

        // 订阅相关字段
        if ($transactionInfo->getExpiresDate()) {
            $receiptInfo['expires_date'] = $this->formatTimestamp($transactionInfo->getExpiresDate());
            $receiptInfo['is_trial_period'] = $transactionInfo->getOfferType() === TransactionInfo::OFFER_TYPE__INTRODUCTORY ? 1 : 0;
            $receiptInfo['is_in_intro_offer_period'] = $transactionInfo->getOfferType() === TransactionInfo::OFFER_TYPE__PROMOTIONAL ? 1 : 0;
        }

        // 取消日期
        if ($transactionInfo->getRevocationDate()) {
            $receiptInfo['cancellation_date'] = $this->formatTimestamp($transactionInfo->getRevocationDate());
        }

        return $receiptInfo;
    }

    /**
     * 解析凭证信息（传统方式，静态方法）
     * @param array $verifyResult
     * @return array
     */
    private static function parseReceiptInfo(array $verifyResult): array
    {
        $receipt = $verifyResult['receipt'] ?? [];
        $latestReceiptInfo = $verifyResult['latest_receipt_info'] ?? [];
        $pendingRenewalInfo = $verifyResult['pending_renewal_info'] ?? [];

        // 获取当前票据对应的交易信息
        $currentTransaction = null;
        
        // 优先从receipt的in_app中获取当前票据的交易信息
        if (!empty($receipt['in_app'])) {
            // in_app中可能包含多个交易，选择最新的一个
            $currentTransaction = $receipt['in_app'][0];
            
            // 如果有latest_receipt_info，尝试用transaction_id匹配找到对应的详细信息
            if (!empty($latestReceiptInfo) && !empty($currentTransaction['transaction_id'])) {
                $currentTransactionId = $currentTransaction['transaction_id'];
                
                // 在latest_receipt_info中查找匹配的交易
                foreach ($latestReceiptInfo as $transaction) {
                    if (isset($transaction['transaction_id']) && 
                        $transaction['transaction_id'] === $currentTransactionId) {
                        // 找到匹配的交易，使用更详细的信息
                        $currentTransaction = $transaction;
                        break;
                    }
                }
            }
        } else if (!empty($latestReceiptInfo)) {
            // 如果receipt中没有in_app，则使用latest_receipt_info的第一个
            // 这种情况通常发生在订阅产品的验证中
            $currentTransaction = $latestReceiptInfo[0];
        }

        if (empty($currentTransaction)) {
            throw new Exception('No transaction found in receipt');
        }

        // 返回完整的票据数据，包含原始数据和解析后的关键字段
        $receiptInfo = [
            // 原始完整数据
            'raw_receipt_data' => $verifyResult,
            
            // 解析后的关键字段（保持向后兼容）
            'transaction_id' => $currentTransaction['transaction_id'] ?? '',
            'original_transaction_id' => $currentTransaction['original_transaction_id'] ?? '',
            'product_id' => $currentTransaction['product_id'] ?? '',
            'bundle_id' => $receipt['bundle_id'] ?? '',
            'purchase_date' => self::formatAppleDate($currentTransaction['purchase_date'] ?? ''),
            'original_purchase_date' => self::formatAppleDate($currentTransaction['original_purchase_date'] ?? ''),
            'quantity' => intval($currentTransaction['quantity'] ?? 1),
            
            // 当前交易信息（替代latest_transaction）
            'current_transaction' => $currentTransaction,
            
            // 完整的票据信息
            'receipt' => $receipt,
            
            // 完整的最新票据信息列表
            'latest_receipt_info' => $latestReceiptInfo,
            
            // 完整的待续费信息
            'pending_renewal_info' => $pendingRenewalInfo,
        ];

        // 订阅相关字段
        if (isset($currentTransaction['expires_date'])) {
            $receiptInfo['expires_date'] = self::formatAppleDate($currentTransaction['expires_date']);
            $receiptInfo['is_trial_period'] = ($currentTransaction['is_trial_period'] ?? 'false') === 'true' ? 1 : 0;
            $receiptInfo['is_in_intro_offer_period'] = ($currentTransaction['is_in_intro_offer_period'] ?? 'false') === 'true' ? 1 : 0;
        }

        // 取消日期
        if (isset($currentTransaction['cancellation_date'])) {
            $receiptInfo['cancellation_date'] = self::formatAppleDate($currentTransaction['cancellation_date']);
        }

        // 自动续费信息
        if (!empty($pendingRenewalInfo)) {
            $renewalInfo = $pendingRenewalInfo[0];
            $receiptInfo['auto_renew_status'] = intval($renewalInfo['auto_renew_status'] ?? 1);
            $receiptInfo['auto_renew_product_id'] = $renewalInfo['auto_renew_product_id'] ?? '';
        }

        return $receiptInfo;
    }

    /**
     * 格式化时间戳（实例方法）
     * @param int|null $timestamp
     * @return string|null
     */
    private function formatTimestamp(?int $timestamp): ?string
    {
        if (empty($timestamp)) {
            return null;
        }

        try {
            // App Store Server API返回的是毫秒时间戳
            $seconds = intval($timestamp / 1000);
            return date('Y-m-d H:i:s', $seconds);
        } catch (Exception $e) {
            Log::channel('order')->warning('Failed to format timestamp', [
                'timestamp' => $timestamp,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * 格式化苹果日期（传统方式，静态方法）
     * @param string $appleDate
     * @return string|null
     */
    private static function formatAppleDate(string $appleDate): ?string
    {
        if (empty($appleDate)) {
            return null;
        }

        try {
            // 苹果日期格式：2023-01-01 12:00:00 Etc/GMT
            $timestamp = strtotime($appleDate);
            if ($timestamp === false) {
                return null;
            }
            return date('Y-m-d H:i:s', $timestamp);
        } catch (Exception $e) {
            Log::channel('order')->warning('Failed to format apple date', [
                'date' => $appleDate,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * 获取错误信息（静态方法）
     * @param int $status
     * @return string
     */
    private static function getErrorMessage(int $status): string
    {
        $errorMessages = [
            21000 => 'The App Store could not read the JSON object you provided.',
            21002 => 'The data in the receipt-data property was malformed or missing.',
            21003 => 'The receipt could not be authenticated.',
            21004 => 'The shared secret you provided does not match the shared secret on file for your account.',
            21005 => 'The receipt server is not currently available.',
            21006 => 'This receipt is valid but the subscription has expired.',
            21007 => 'This receipt is from the test environment, but it was sent to the production environment for verification.',
            21008 => 'This receipt is from the production environment, but it was sent to the test environment for verification.',
            21009 => 'Internal data access error.',
            21010 => 'The user account cannot be found or has been deleted.',
        ];

        return $errorMessages[$status] ?? 'Unknown error (status: ' . $status . ')';
    }
}