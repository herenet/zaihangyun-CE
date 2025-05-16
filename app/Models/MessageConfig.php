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
        'tenant_id',
        'switch',
    ];

    public function getConfig($appKey, $tenantId)
    {
        $config = $this->where(['app_key' => $appKey, 'tenant_id' => $tenantId])->first();
        if(!$config){
            return [];
        }
        return $config->toArray();
    }

    public function saveConfig($appKey, $tenantId, $data)
    {
        return self::updateOrCreate(['tenant_id' => $tenantId, 'app_key' => $appKey], $data);
    }
    
    
}

