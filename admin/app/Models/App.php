<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Traits\DefaultDatetimeFormat;


/**
 * CREATE TABLE `apps` (
 *  `app_key` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
 *  `name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
 *  `platform_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1为安卓，2为iphone',
 *  `launcher_icon` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *  `app_secret` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
 *  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
 *  PRIMARY KEY (`app_key`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 */
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

    public static $platformIcons = [
        self::PLATFORM_TYPE_ANDROID => '<i class="fa fa-lg fa-android text-green"></i>',
        self::PLATFORM_TYPE_IOS => '<i class="fa fa-lg fa-apple text-black"></i>',
        self::PLATFORM_TYPE_HARMONYOS => '<i class="fa fa-lg fa-circle-o text-blue"></i>',
    ];

    const APP_INFO_CACHE_KEY = 'app_info_';
    const APP_INFO_CACHE_EXPIRE_TIME = 60 * 60 * 24;

    protected $fillable = [
        'app_key',
        'app_secret',
        'name',
        'platform_type',
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