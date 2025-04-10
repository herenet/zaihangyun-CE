<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Traits\DefaultDatetimeFormat;

class WechatOpenPlatformConfig extends Model
{
    use DefaultDatetimeFormat;

    protected $primaryKey = 'app_key';

    public $incrementing = false;

    protected $table = 'wechat_open_platform_config';
    protected $fillable = [
        'app_key',
        'app_name',
        'tenant_id', 
        'wechat_appid', 
        'wechat_appsecret',
        'interface_check',
        'remark',
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
