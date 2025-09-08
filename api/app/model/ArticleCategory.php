<?php

namespace app\model;

use support\Model;

class ArticleCategory extends Model
{
    protected $table = 'article_category';

    protected $primaryKey = 'id';

    public $incrementing = false;

    public $timestamps = false;

    public $hidden = ['app_key'];

    public function getCount($appKey)
    {
        $rs = $this->where('app_key', $appKey)->count();
        return $rs;
    }

    public function getArticleCategoryList($appKey, $start, $limit)
    {
        $rs = $this
            ->select('id', 'name', 'desc', 'updated_at', 'created_at')
            ->where('app_key', $appKey)->orderBy('created_at', 'desc')->offset($start)->limit($limit)->get();
        if(empty($rs)){
            return [];
        }
        return $rs->toArray();
    }
}
