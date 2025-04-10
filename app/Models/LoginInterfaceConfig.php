<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginInterfaceConfig extends Model
{
    protected $table = 'login_interface_config';

    protected $primaryKey = 'app_key';

    public $incrementing = false;

    protected $fillable = [
        'app_key', 
        'tenant_id', 
        'switch', 
        'token_effective_duration',
        'suport_wechat_login', 
        'suport_mobile_login', 
        'suport_apple_login', 
        'endpoint_allow_count',
    ];

    public function getConfig($tenantId, $appKey)
    {
        $config = $this->where(['tenant_id' => $tenantId, 'app_key' => $appKey])->first();
        return $config;
    }

    public function saveConfig($tenantId, $appKey, $data)
    {
        return self::updateOrCreate(['tenant_id' => $tenantId, 'app_key' => $appKey], $data);
    }
}
