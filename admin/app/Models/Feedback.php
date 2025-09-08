<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Traits\DefaultDatetimeFormat;

class Feedback extends Model
{
    use DefaultDatetimeFormat;

    protected $table = 'feedback';

    public static $type = [
        1 => '功能建议',
        2 => '问题反馈',
        99 => '其他',
    ];

    protected $fillable = [
        'app_key', 
        'uid',
        'type', 
        'content', 
        'contact',
        'reply',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'uid');
    }
}
