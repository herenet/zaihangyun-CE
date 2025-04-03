<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Traits\DefaultDatetimeFormat;

class WechatOpenPlatformConfig extends Model
{
    use DefaultDatetimeFormat;

    protected $table = 'wechat_open_platform_config';
    protected $fillable = [
        'laucher_icon_url',
        'app_name',
        'tenant_id', 
        'wechat_appid', 
        'wechat_appsecret',
        'interface_check',
        'remark',
    ];
}
