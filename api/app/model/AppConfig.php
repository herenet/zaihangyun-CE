<?php

namespace app\model;

use support\Cache;
use support\Model;

class AppConfig extends Model
{
    protected $table = 'app_configs';
    protected $primaryKey = 'id';
    public $timestamps = false;

    const CACHE_TTL = 24*60*60;

    const CACHE_KEY_APP_CONFIG = 'app_config|{configName}|{appKey}';

    public function getAppConfigByConfigName($configName, $appKey)
    {
        $cacheKey = str_replace(['{configName}', '{appKey}'], [$configName, $appKey], self::CACHE_KEY_APP_CONFIG);
        $rs = Cache::get($cacheKey);
        if(empty($rs)){
            $rs = $this->where(['name' => $configName, 'app_key' => $appKey])->first();
            if(empty($rs)){
                return [];
            }
            $rs = $rs->toArray();
            Cache::set($cacheKey, $rs, self::CACHE_TTL);
        }
        return $rs;
    }
}
