<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Traits\DefaultDatetimeFormat;

class AppConfig extends Model
{
    use DefaultDatetimeFormat;

    protected $table = 'app_configs';

    protected $casts = [
        'params' => 'json',
    ];
}
