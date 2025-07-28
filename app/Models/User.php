<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Model;


/**
 * CREATE TABLE `users` (
 *  `uid` bigint unsigned NOT NULL,
 *  `app_key` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '应用app_key',
 *  `tenant_id` bigint NOT NULL COMMENT '租户ID',
 *  `huawei_openid` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *  `huawei_unionid` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *  `apple_openid` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *  `apple_unionid` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *  `wechat_openid` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *  `wechat_unionid` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *  `apple_userid` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *  `oaid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *  `device_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *  `username` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *  `nickname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *  `mcode` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '+86' COMMENT '手机国家区号',
 *  `mobile` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *  `password` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '登录密码',
 *  `email` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *  `avatar` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *  `gender` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *  `birthday` date DEFAULT NULL,
 *  `country` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *  `province` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *  `city` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *  `reg_ip` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *  `is_forever_vip` bit(1) DEFAULT b'0' COMMENT '永久会员',
 *  `vip_expired_at` datetime DEFAULT NULL,
 *  `enter_pass` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '应用启动密码',
 *  `version_number` int unsigned NOT NULL DEFAULT '1' COMMENT 'APP版本数值',
 *  `channel` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'official' COMMENT '来源渠道',
 *  `reg_from` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '注册来源平台：1为手机号，2为微信，3为苹果',
 *  `ext_data` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '自定义字段',
 *  `canceled_at` timestamp NULL DEFAULT NULL COMMENT '注销时间',
 *  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
 *  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 *  PRIMARY KEY (`uid`),
 *  UNIQUE KEY `idx_openid` (`wechat_openid`,`app_key`) USING BTREE,
 *  UNIQUE KEY `idx_appleuserid` (`apple_userid`,`app_key`) USING BTREE,
 *  UNIQUE KEY `idx_mcode_mobile` (`mcode`,`mobile`,`app_key`) USING BTREE
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 */
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
