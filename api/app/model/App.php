<?php

namespace app\model;

use support\Cache;
use support\Model;

class App extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'apps';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'app_key';

    /**
     * Undocumented variable
     *
     * @var boolean
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    const CACHE_TTL = 24*60*60;

    const CACHE_KEY_APP_INFO = 'app_info|';

    public function getAppInfoByAppKey($appKey)
    {
        $cacheKey = self::CACHE_KEY_APP_INFO . $appKey;
        $rs = Cache::get($cacheKey);
        if(empty($rs)){
            $rs = $this->where('app_key', $appKey)->first();
            if(empty($rs)){
                return null;
            }
            $rs = $rs->toArray();
            Cache::set($cacheKey, $rs, self::CACHE_TTL);
        }
        return $rs;
    }
}