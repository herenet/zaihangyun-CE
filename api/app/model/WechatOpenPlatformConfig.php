<?php

namespace app\model;

use support\Cache;
use support\Model;

class WechatOpenPlatformConfig extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wechat_open_platform_config';

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

    const CACHE_KEY_WECHAT_OPEN_PLATFORM_CONFIG = 'wechat_open_platform_config|';

    public function getWechatOpenPlatformConfig($id)
    {
        $cacheKey = self::CACHE_KEY_WECHAT_OPEN_PLATFORM_CONFIG . $id;
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