<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlipayConfig extends Model
{
    protected $table = 'alipay_config';

    protected $primaryKey = 'app_key';

    public $incrementing = false;

    protected $fillable = [
        'app_key', 
        'tenant_id', 
        'alipay_app_id',
        'alipay_public_cert',
        'app_private_cert',
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
