<?php

namespace app\model;

use support\Model;

class Order extends Model
{
    protected $table = 'orders';
    
    protected $primaryKey = 'oid';

    public $incrementing = false;

    public $timestamps = false;

    const STATUS_READY = 1;
    const STATUS_SUCCESS = 2;
    const STATUS_REFUNDING = 3;
    const STATUS_REFUNDED = 4;
    const STATUS_PAYMENT_FAILED = 5;
    const STATUS_REFUND_FAILED = 6;
    
    const PAY_CHANNEL_WECHAT = 1;
    const PAY_CHANNEL_ALIPAY = 2;
    const PAY_CHANNEL_APPLE = 3;

    const DEFAULT_CHANNEL = 'official'; 

    const REFUND_TYPE_ORIGINAL = 1;
    const REFUND_TYPE_ONLY = 2;

    public static $refundTypeMap = [
        self::REFUND_TYPE_ORIGINAL => '退款并退功能',
        self::REFUND_TYPE_ONLY => '仅退款',
    ];

    protected $fillable = [
        'oid',
        'app_key',
        'uid',
        'product_id',
        'product_price',
        'order_amount',
        'payment_amount',
        'pay_channel',
        'channel',
        'status',
        'refund_id',
        'refund_send_time',
        'refund_time',
        'refund_amount',
        'refund_reason',
        'refund_type',
        'refund_channel',
    ];

    public function createOrder(array $data)
    {
        $params = [
            'oid' => $data['oid'],
            'app_key' => $data['app_key'],
            'uid' => $data['uid'],
            'product_id' => $data['product_id'],
            'product_price' => $data['product_price'],
            'order_amount' => $data['order_amount'],
            'payment_amount' => $data['payment_amount'],
            'pay_channel' => $data['pay_channel'],
            'channel' => $data['channel'],
            'status' => $data['status'],
        ];
        $order = $this->create($params);
        return $order;
    }

    public function getOrderInfoByOid(string $oid)
    {
        $rs = $this->where('oid', $oid)->first();
        if(empty($rs)){
            return [];
        }
        return $rs->toArray();
    }

    public function getOrderInfoByOidAndUid(string $oid, string $uid)
    {
        $rs = $this->where(['oid' => $oid, 'uid' => $uid])->first();
        if(empty($rs)){
            return [];
        }
        return $rs->toArray();
    }

    public function updateOrderInfoByOid(string $oid, array $data)
    {
        return $this->where('oid', $oid)->update($data);
    }

    public function getOrdersByUid(string $uid, $pay_channel = null, $status = null, $limit = 10)
    {
        $query = $this->where(['uid' => $uid]);
        if(!empty($pay_channel)){
            $query->where('pay_channel', $pay_channel);
        }
        if(!empty($status)){
            $query->where('status', $status);
        }
        $rs = $query->orderBy('created_at', 'desc')->limit($limit)->get();
        if(empty($rs)){
            return [];
        }
        return $rs->toArray();
    }
}
