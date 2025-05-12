<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Traits\DefaultDatetimeFormat;

class AppUpgradeChannel extends Model
{
    use DefaultDatetimeFormat;
    protected $table = 'app_upgrade_channels';

    protected $fillable = [
        'app_key',
        'channel_name',
        'is_default',
        'tenant_id'
    ];
    
    public const IS_DEFAULT = 1;
    public const IS_NOT_DEFAULT = 2;
}
