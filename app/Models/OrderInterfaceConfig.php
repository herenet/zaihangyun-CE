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
        'switch', 
        'oid_prefix',
        'suport_wechat_pay', 
        'wechat_platform_config_id',
        'wechat_payment_config_id',
        'suport_alipay', 
        'suport_apple_pay', 
        'suport_apple_verify',
    ];

    public function getConfig($appKey)
    {
        $config = $this->where(['app_key' => $appKey])->first();
        return $config;
    }

    public function saveConfig($appKey, $data)
    {
        return self::updateOrCreate(['app_key' => $appKey], $data);
    }
}
