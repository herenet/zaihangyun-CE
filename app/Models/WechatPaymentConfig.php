<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Traits\DefaultDatetimeFormat;

class WechatPaymentConfig extends Model
{
    use DefaultDatetimeFormat;

    protected $table = 'wechat_payment_config';
    protected $fillable = [
        'mch_name',
        'tenant_id', 
        'mch_id',
        'mch_cert_serial',
        'mch_api_v3_secret',
        'mch_private_key_path',
        'mch_public_key_path',
        'mch_platform_cert_path',
        'interface_check',
        'callback_check',
        'remark',
    ];
}
