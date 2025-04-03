<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginInterfaceConfig extends Model
{
    protected $table = 'login_interface_config';
    protected $fillable = [
        'tenant_id', 
        'app_key', 
        'switch', 
        'token_effective_duration',
        'jwt_payload_fields', 
        'suport_wechat_login', 
        'selected_wechat_open_platform_id', 
        'suport_mobile_login', 
        'mobile_sms_interface_check', 
        'suport_apple_login', 
        'apple_login_interface_check'
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
