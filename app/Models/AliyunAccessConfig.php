<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Traits\DefaultDatetimeFormat;

class AliyunAccessConfig extends Model
{
    use DefaultDatetimeFormat;

    protected $table = 'aliyun_access_config';
    protected $fillable = [
        'name',
        'access_key_id',
        'access_key_secret',
        'interface_check',
        'remark',
    ];
}
