<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class ArticleConfig extends Model
{
    protected $table = 'article_config';
    
    protected $primaryKey = 'app_key';

    public $incrementing = false;

    public $timestamps = false;

    const CACHE_KEY_PREFIX = 'article_config|';

    const CACHE_EXPIRE_TIME = 24*60*60;

    const DEFAULT_LIST_THEME = 'light';

    const DEFAULT_CONTENT_THEME = 'light';

    protected $fillable = [
        'app_key',
        'switch',
        'list_theme',
        'content_theme',
    ];

    public static $listTheme = [
        'light' => '浅色主题',
        'dark' => '深色主题',
    ];

    public static $contentTheme = [
        'light' => '浅色主题',
        'dark' => '深色主题',
    ];

    public function getConfig($appKey)
    {
        $cacheKey = self::CACHE_KEY_PREFIX . $appKey;
        $config_info = Cache::get($cacheKey);
        if(!$config_info){
            $config_info = self::where('app_key', $appKey)->first();
            $config_info = $config_info ? $config_info->toArray() : [];
            if($config_info){
                Cache::put($cacheKey, $config_info, self::CACHE_EXPIRE_TIME);
            }
        }
        return $config_info;
    }

    public function getConfigByAppKey($appKey)
    {
        $cacheKey = self::CACHE_KEY_PREFIX . $appKey;
        $config_info = Cache::get($cacheKey);
        if(!$config_info){
            $config_info = self::where('app_key', $appKey)->first();
            $config_info = $config_info ? $config_info->toArray() : [];
            if($config_info){
                Cache::put($cacheKey, $config_info, self::CACHE_EXPIRE_TIME);
            }
        }
        return $config_info;
    }

    protected function clearCache($appKey)
    {
        $cacheKey = self::CACHE_KEY_PREFIX . $appKey;
        Cache::forget($cacheKey);
    }

    public function saveConfig($appKey, $configData)
    {
        $this->clearCache($appKey);
        return self::updateOrCreate(['app_key' => $appKey], $configData);
    }
}

