<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppleReceiptVerification extends Model
{
    protected $table = 'apple_receipt_verifications';

    // 验证状态常量
    const STATUS_SUCCESS = 1;
    const STATUS_FAILED = 2;

    // 状态映射
    public static $statusMap = [
        self::STATUS_SUCCESS => '验证成功',
        self::STATUS_FAILED => '验证失败',
    ];

    protected $fillable = [
        'tenant_id',
        'app_key', 
        'receipt_data_hash',
        'verification_status',
        'apple_status_code',
        'error_message',
        'bundle_id',
        'environment',
        'transaction_id',
        'original_transaction_id',
        'product_id',
        'purchase_date',
        'quantity',
    ];

    protected $casts = [
        'purchase_date' => 'datetime',
        'quantity' => 'integer',
    ];

    /**
     * 关联票据数据
     */
    public function receiptData()
    {
        return $this->hasOne(AppleReceiptData::class, 'verification_id');
    }

    /**
     * 创建或获取验证记录（处理去重）
     */
    public static function createOrGet($tenantId, $appKey, $receiptData, $additionalData = [])
    {
        $hash = hash('sha256', $receiptData);
        
        // 先检查是否已存在
        $existing = self::where([
            'app_key' => $appKey,
            'receipt_data_hash' => $hash
        ])->first();

        if ($existing) {
            return $existing;
        }

        // 创建新记录
        $verification = self::create(array_merge([
            'tenant_id' => $tenantId,
            'app_key' => $appKey,
            'receipt_data_hash' => $hash,
            'verification_status' => self::STATUS_FAILED, // 默认失败状态
        ], $additionalData));

        // 创建关联的数据记录（包含冗余字段）
        $verification->receiptData()->create([
            'tenant_id' => $tenantId,
            'app_key' => $appKey,
            'receipt_data_hash' => $hash,
            'receipt_data' => $receiptData
        ]);

        return $verification;
    }

    /**
     * 更新验证结果和数据
     */
    public function updateVerificationResult($status, $data = [])
    {
        // 更新主记录
        $this->update(array_merge(['verification_status' => $status], $data));

        // 如果有新的票据数据，更新数据表
        if (isset($data['receipt_data'])) {
            $this->receiptData->update([
                'receipt_data' => $data['receipt_data']
            ]);
        }
    }
}