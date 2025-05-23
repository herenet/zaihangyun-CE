<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Traits\DefaultDatetimeFormat;

class App extends Model
{
    use DefaultDatetimeFormat;
    protected $table = 'apps';

    protected $primaryKey = 'app_key';

    public $incrementing = false;

    const PLATFORM_TYPE_ANDROID = 1;
    const PLATFORM_TYPE_IOS = 2;
    const PLATFORM_TYPE_HARMONYOS = 3;

    public static $platformType = [
        self::PLATFORM_TYPE_ANDROID => 'Android',
        self::PLATFORM_TYPE_IOS => 'iOS',
        self::PLATFORM_TYPE_HARMONYOS => 'HarmonyOS',
    ];

    const APP_INFO_CACHE_KEY = 'app_info_';
    const APP_INFO_CACHE_EXPIRE_TIME = 60 * 60 * 24;

    protected $fillable = [
        'app_key',
        'app_secret',
        'name',
        'platform_type',
        'tenant_id',
    ];

    public function getAppInfo($app_key) : array
    {
        $cache_key = self::APP_INFO_CACHE_KEY . $app_key;
        $app_info = Cache::get($cache_key);
        if($app_info) {
            return $app_info;
        }

        $app = self::where('app_key', $app_key)->first();
        if(!$app) {
            return [];
        }

        $app_info = $app->toArray();
        Cache::put($cache_key, $app_info, self::APP_INFO_CACHE_EXPIRE_TIME);

        return $app_info;
    }

    public function clearAppInfoCache($app_key)
    {
        $cache_key = self::APP_INFO_CACHE_KEY . $app_key;
        Cache::forget($cache_key);
    }
} 