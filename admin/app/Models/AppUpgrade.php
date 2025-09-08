<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Traits\DefaultDatetimeFormat;

class AppUpgrade extends Model
{
    use DefaultDatetimeFormat;

    protected $table = 'app_upgrades';

    public static $enabledMap = [
        0 => '未开启',
        1 => '已开启',
    ];

    public static $upgradeFromMap = [
        1 => '应用市场',
        2 => '官网下载',
    ];
}