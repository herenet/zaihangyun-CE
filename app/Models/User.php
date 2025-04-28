<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use DefaultDatetimeFormat;

    protected $table = 'users';

    protected $primaryKey = 'uid';
    public $incrementing = false;

    static $fields_map = [
        'uid' => '用户ID',
        'email' => '邮箱',
        'mcode' => '国家区号',
        'mobile' => '手机号',
        'username' => '用户名',
        'password' => '密码',
        'nickname' => '昵称',
        'avatar' => '头像',
        'gender' => '性别',
        'birthday' => '生日',
        'address' => '地址',
        'city' => '城市',
        'province' => '省份',
        'country' => '国家',
        'reg_ip' => '注册IP',
        'reg_from' => '注册来源',
        'enter_pass' => '进入密码',
        'vip_expired_at' => 'VIP到期时间',
        'is_forever_vip' => '是否永久VIP',
        'version_number' => '版本数值',
        'channel' => '注册来源渠道',
        'ext_data' => '自定义扩展数据',
        'canceled_at' => '申请注销时间',
        'created_at' => '创建时间',
        'updated_at' => '更新时间',
    ];


    public static $regFromMap = [
        1 => '手机号',
        2 => '微信',
        3 => '苹果',
        99 => '后台'
    ];

    public static $genderMap = [
        0 => '未知',
        1 => '男',
        2 => '女'
    ];

    const DEFAULT_CHANNEL = 'official';

    public static $isForeverVipMap = [
        0 => '否',
        1 => '是'
    ];

    public function fields() 
    {
        $columns = $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
        $ret = array_diff($columns, [
            'tenant_id',
            'wechat_openid', 
            'wechat_unionid', 
            'apple_userid', 
            'app_key', 
            'reg_ip', 
            'enter_pass', 
            'created_at', 
            'updated_at', 
            'deleted_at'
        ]);
        $rs = [];
        foreach ($ret as $key => $value) {
            $rs[$value] = static::$fields_map[$value].'（'.$value.'）';
        }
        return $rs;
    }
}
