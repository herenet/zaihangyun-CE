<?php

namespace app\model;

use support\Cache;
use support\Model;

class ArticleConfig extends Model
{
    protected $table = 'article_config';

    protected $primaryKey = 'app_key';

    public $incrementing = false;

    public $timestamps = false;
    
    const CACHE_TTL = 24*60*60;

    const CACHE_KEY_ARTICLE_CONFIG = 'article_config|';

    public function getArticleConfigByAppKey($appKey)
    {
        $cacheKey = self::CACHE_KEY_ARTICLE_CONFIG . $appKey;
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