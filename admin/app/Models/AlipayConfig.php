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
        'alipay_app_id',
        'alipay_public_cert',
        'app_private_cert',
        'interface_check',
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
