<?php

namespace app\model;

use support\Model;

class Feedback extends Model
{
    protected $table = 'feedback';

    protected $fillable = ['app_key', 'uid', 'content', 'type', 'contact'];

    public $timestamps = false;

    public static $typeMap = [
        1 => '功能建议',
        2 => '问题反馈',
        99 => '其他',
    ];

    public function createFeedback(array $data)
    {
       if($this->insert($data)){
            return true;
       }
       return false;
    }

    public function getCount($appKey, $uid = null)
    {
        if($uid){
            $rs = $this->where('app_key', $appKey)->where('uid', $uid)->count();
        }else{
            $rs = $this->where('app_key', $appKey)->count();
        }
        return $rs;
    }

    public function getFeedbackListByUid($appKey, $uid, $start, $limit)
    {
        $rs = $this
            ->select('id', 'content', 'type', 'contact', 'reply', 'updated_at', 'created_at')
            ->where('app_key', $appKey)->where('uid', $uid)->orderBy('created_at', 'desc')->offset($start)->limit($limit)->get();
        if(empty($rs)){
            return [];
        }
        return $rs->toArray();
    }

    public function getFeedbackList($appKey, $start, $limit, $fields = [])
    {
        if(empty($fields)){
            $fields = ['id', 'content', 'type', 'contact', 'reply', 'updated_at', 'created_at'];
        }
        $rs = $this
            ->select($fields)
            ->where('app_key', $appKey)->orderBy('created_at', 'desc')->offset($start)->limit($limit)->get();
        if(empty($rs)){
            return [];
        }
        return $rs->toArray();
    }
}

