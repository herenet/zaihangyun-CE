<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use DefaultDatetimeFormat;

    protected $table = 'users';

    protected $primaryKey = 'uid';
    public $incrementing = false;


    public static $regFromMap = [
        1 => '手机号',
        2 => '微信',
        3 => '苹果',
        99 => '后台'
    ];

    const DEFAULT_CHANNEL = 'official';

    public static $isForeverVipMap = [
        0 => '否',
        1 => '是'
    ];
}
