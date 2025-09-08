<?php

namespace app\model;

use support\Cache;
use support\Model;


class AppUpgrade extends Model
{
    protected $table = 'app_upgrades';
    protected $primaryKey = 'id';
    public $timestamps = false;

    const CACHE_KEY_APP_UPGRADE = 'app_upgrade|{channelId}';

    const CACHE_TTL = 24*60*60;

    const ENABLED = 1;
    const DISABLED = 0;

    const FORCE_UPGRADE = 1;
    const NO_FORCE_UPGRADE = 0;

    const GRAY_UPGRADE = 1;
    const NO_GRAY_UPGRADE = 0;

    public function getAppUpgradeByChannelId($channelId)
    {
        $cacheKey = str_replace('{channelId}', $channelId, self::CACHE_KEY_APP_UPGRADE);
        $rs = Cache::get($cacheKey);
        if(empty($rs)){
            $rs = $this->where(['channel_id' => $channelId, 'enabled' => self::ENABLED])->first();
            if(empty($rs)){
                return [];
            }
            $rs = $rs->toArray();
            Cache::set($cacheKey, $rs, self::CACHE_TTL);
        }
        return $rs;
    }
}
