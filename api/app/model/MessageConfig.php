<?php

namespace app\model;

use support\Cache;
use support\Model;

class MessageConfig extends Model
{
    protected $table = 'message_config';
    protected $primaryKey = 'app_key';

    protected $autoIncrement = false;

    public $timestamps = false;

    const CACHE_TTL = 24*60*60;

    const CACHE_KEY_MESSAGE_CONFIG = 'message_config|';

    public function getMessageConfigByAppKey($appKey)
    {
        $cacheKey = self::CACHE_KEY_MESSAGE_CONFIG . $appKey;
        $rs = Cache::get($cacheKey);
        if(empty($rs)){
            $rs = $this->where('app_key', $appKey)->first();
            if(empty($rs)){
                return [];
            }
            $rs = $rs->toArray();
            Cache::set($cacheKey, $rs, self::CACHE_TTL);
        }
        return $rs;
    }
}