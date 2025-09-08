<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageConfig extends Model
{
    protected $table = 'message_config';
    
    protected $primaryKey = 'app_key';

    public $timestamps = false;

    public $incrementing = false;

    protected $fillable = [
        'app_key',
        'switch',
    ];

    public function getConfig($appKey)
    {
        $config = $this->where(['app_key' => $appKey])->first();
        if(!$config){
            return [];
        }
        return $config->toArray();
    }

    public function saveConfig($appKey, $data)
    {
        return self::updateOrCreate(['app_key' => $appKey], $data);
    }
    
    
}

