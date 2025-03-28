<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    use DefaultDatetimeFormat;
    protected $table = 'apps';

    protected $primaryKey = 'app_key';

    public $incrementing = false;

    protected $fillable = [
        'app_key',
        'name',
        'platform_type',
        'tenant_id',
    ];
} 