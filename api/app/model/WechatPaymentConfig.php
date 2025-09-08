<?php

namespace app\model;

use support\Cache;
use support\Model;

class WechatPaymentConfig extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wechat_payment_config';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    const CACHE_TTL = 24*60*60;

    const CACHE_KEY_WECHAT_PAYMENT_CONFIG = 'wechat_payment_config|';

    public function getWechatPaymentConfig($id)
    {
        $cacheKey = self::CACHE_KEY_WECHAT_PAYMENT_CONFIG . $id;
        $rs = Cache::get($cacheKey);
        if(empty($rs)){
            $rs = $this->where(['id' => $id])->first();
            if(empty($rs)){
                return null;
            }
            $rs = $rs->toArray();
            Cache::set($cacheKey, $rs, self::CACHE_TTL);
        }
        return $rs;
    }
}