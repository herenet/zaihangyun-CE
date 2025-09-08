<?php

namespace app\model;

use support\Model;

class Article extends Model
{
    protected $table = 'articles';

    protected $primaryKey = 'id';

    public $incrementing = false;

    public $timestamps = false;

    public $hidden = ['app_key', 'type'];

    public function getArticleInfoById($id, $appKey)
    {
        $rs = $this->where('id', $id)->where('app_key', $appKey)->first();
        if(empty($rs)){
            return null;
        }
        return $rs->toArray();
    }

    public function getCount($appKey)
    {
        $rs = $this->where('app_key', $appKey)->count();
        return $rs;
    }

    public function getArticleList($appKey, $start, $limit)
    {
        $rs = $this
            ->select('id', 'title', 'order', 'updated_at', 'created_at')
            ->where('app_key', $appKey)->orderBy('order', 'asc')->offset($start)->limit($limit)->get();
        if(empty($rs)){
            return [];
        }
        return $rs->toArray();
    }
}
