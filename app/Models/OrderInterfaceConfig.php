<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderInterfaceConfig extends Model
{
    protected $table = 'order_interface_config';

    protected $primaryKey = 'app_key';

    public $incrementing = false;

    protected $fillable = [
        'app_key', 
        'tenant_id', 
        'switch', 
        'suport_wechat_pay', 
        'wechat_platform_config_id',
        'wechat_payment_config_id',
        'suport_alipay', 
        'suport_apple_pay', 
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
