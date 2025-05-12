<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Traits\DefaultDatetimeFormat;

class AppUpgradeChannel extends Model
{
    use DefaultDatetimeFormat;
    protected $table = 'app_upgrade_channels';
    
    
}
