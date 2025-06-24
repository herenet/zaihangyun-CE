<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Traits\DefaultDatetimeFormat;

class SubscriptionOrder extends Model
{
    use DefaultDatetimeFormat;

    protected $table = 'subscription_orders';
    protected $primaryKey = 'order_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'order_id',
        'tenant_id',
        'order_type',
        'from_product',
        'to_product',
        'product_name',
        'original_price',
        'final_price',
        'status',
        'pay_channel',
        'wechat_prepay_id',
        'wechat_code_url',
        'third_party_order_id',
        'third_party_transaction_id',
        'paid_at',
        'upgrade_info',
    ];

    protected $casts = [
        'upgrade_info' => 'array',
        'paid_at' => 'datetime',
    ];

    // 订单状态常量
    const STATUS_PENDING = 1;      // 待支付
    const STATUS_PAID = 2;         // 已支付
    const STATUS_CANCELLED = 3;    // 已取消
    const STATUS_FAILED = 4;       // 支付失败

    // 订单类型常量
    const TYPE_NEW_PURCHASE = 'new_purchase';  // 新购
    const TYPE_UPGRADE = 'upgrade';            // 升级
    const TYPE_RENEW = 'renew';               // 续费

    // 支付渠道常量
    const PAY_CHANNEL_WECHAT = 1;  // 微信支付
    const PAY_CHANNEL_ALIPAY = 2;  // 支付宝

    // 状态映射
    public static $statusMap = [
        self::STATUS_PENDING => '待支付',
        self::STATUS_PAID => '已支付',
        self::STATUS_CANCELLED => '已取消',
        self::STATUS_FAILED => '支付失败',
    ];

    // 订单类型映射
    public static $typeMap = [
        self::TYPE_NEW_PURCHASE => '新购',
        self::TYPE_UPGRADE => '升级',
        self::TYPE_RENEW => '续费',
    ];

    // 支付渠道映射
    public static $payChannelMap = [
        self::PAY_CHANNEL_WECHAT => '微信支付',
        self::PAY_CHANNEL_ALIPAY => '支付宝',
    ];

    /**
     * 关联租户
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    /**
     * 获取订单状态文本
     */
    public function getStatusTextAttribute()
    {
        return self::$statusMap[$this->status] ?? '未知状态';
    }

    /**
     * 获取订单类型文本
     */
    public function getTypeTextAttribute()
    {
        return self::$typeMap[$this->order_type] ?? '未知类型';
    }

    /**
     * 获取支付渠道文本
     */
    public function getPayChannelTextAttribute()
    {
        return self::$payChannelMap[$this->pay_channel] ?? '未知渠道';
    }

    /**
     * 获取格式化的原价
     */
    public function getFormattedOriginalPriceAttribute()
    {
        return '¥' . number_format($this->original_price / 100, 2);
    }

    /**
     * 获取格式化的最终价格
     */
    public function getFormattedFinalPriceAttribute()
    {
        return '¥' . number_format($this->final_price / 100, 2);
    }

    /**
     * 检查订单是否可以支付
     */
    public function canPay()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * 检查订单是否已支付
     */
    public function isPaid()
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * 标记订单为已支付
     */
    public function markAsPaid($transactionId = null)
    {
        $this->update([
            'status' => self::STATUS_PAID,
            'third_party_transaction_id' => $transactionId,
            'paid_at' => now(),
        ]);
    }

    /**
     * 标记订单为失败
     */
    public function markAsFailed()
    {
        $this->update([
            'status' => self::STATUS_FAILED,
        ]);
    }
} 