<?php

namespace app\model;

use support\Cache;
use support\Model;

class AliyunAccessConfig extends Model
{
    protected $table = 'aliyun_access_config';

    protected $primaryKey = 'id';

    public $timestamps = false;

    const CACHE_TTL = 24*60*60;

    const CACHE_KEY_ALIYUN_ACCESS_CONFIG = 'aliyun_access_config|';

    public function getAliyunAccessConfigById($id)
    {
        $cacheKey = self::CACHE_KEY_ALIYUN_ACCESS_CONFIG . $id;
        $rs = Cache::get($cacheKey);
        if(empty($rs)){
            $rs = $this->where('id', $id)->first();
            if(empty($rs)){
                return null;
            }
            $rs = $rs->toArray();
            Cache::set($cacheKey, $rs, self::CACHE_TTL);
        }
        return $rs;
    }
    
}