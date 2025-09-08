<?php

namespace app\model;

use support\Cache;
use support\Model;

/**
 * CREATE TABLE `users` (
 *  `uid` bigint unsigned NOT NULL,
 *  `app_key` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '应用app_key',
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
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'uid';

    /**
     * Undocumented variable
     *
     * @var boolean
     */
    public $incrementing = false;

    // protected $hidden = ['app_key', 'updated_at', 'deleted_at', 'canceled_at'];

    const REG_FROM_PHONE = 1;
    const REG_FROM_WECHAT = 2;
    const REG_FROM_APPLE = 3;
    const REG_FROM_HUAWEI = 4;
    const REG_FROM_ADMIN = 99;

    public static $regFromMap = [
        self::REG_FROM_PHONE => '手机号',
        self::REG_FROM_WECHAT => '微信',
        self::REG_FROM_APPLE => '苹果',
        self::REG_FROM_HUAWEI => '华为',
        self::REG_FROM_ADMIN => '后台'
    ];

    public static $genderMap = [
        0 => '未知',
        1 => '男',
        2 => '女'
    ];

    const DEFAULT_CHANNEL = 'official';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    const CACHE_TTL = 24*60*60;

    const CACHE_KEY_USER_INFO_UID = 'user_info_uid|';

    public function getUserInfoByUid($uid)
    {
        $cacheKey = self::CACHE_KEY_USER_INFO_UID . $uid;
        $rs = Cache::get($cacheKey);
        if(empty($rs)){
            $rs = $this->where('uid', $uid)->first();
            if(empty($rs)){
                return null;
            }
            $rs = $rs->toArray();
            Cache::set($cacheKey, $rs, self::CACHE_TTL);
        }
        return $rs;
    }

    public function createUser($data, $uid = null)
    {
        if(empty($uid)){
            $data['uid'] = generateUserId();
        }else{
            $data['uid'] = $uid;
        }
        if($this->insert($data)){
            return $data['uid'];
        }
        return false;
    }

    public function getUserInfoByAppleUserId($appleUserId, $appkey)
    {
        $rs = $this->where(['apple_userid' => $appleUserId, 'app_key' => $appkey])->first();
        if(empty($rs)){
            return null;
        }
        $rs = $rs->toArray();
        return $rs;
    }

    public function getUserInfoByOpenId($openId, $appkey)
    {
        $rs = $this->where(['wechat_openid' => $openId, 'app_key' => $appkey])->first();
        if(empty($rs)){
            return null;
        }
        $rs = $rs->toArray();
            
        return $rs;
    }

    public function updateUserInfoByUid($uid, $data)
    {
        $rs = $this->where('uid', $uid)->update($data);
        if($rs){
            Cache::delete(self::CACHE_KEY_USER_INFO_UID . $uid);
            return true;
        }
        return false;
    }

    public function getUserInfoByPhoneNumber($mcode, $mobile, $appkey)
    {
        $rs = $this->where(['mcode' => $mcode, 'mobile' => $mobile, 'app_key' => $appkey])->first();
        if(empty($rs)){
                return null;
        }
        $rs = $rs->toArray();
        return $rs;
    }

    public function deleteUserByUid($uid)
    {
        Cache::delete(self::CACHE_KEY_USER_INFO_UID . $uid);
        return $this->where('uid', $uid)->delete();
    }

    public function cancelUserByUid($uid)
    {
        return $this->where('uid', $uid)->update(['canceled_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * 根据华为OpenID获取用户信息
     */
    public function getUserInfoByHuaweiOpenId($openId, $appkey)
    {
        $rs = $this->where(['huawei_openid' => $openId, 'app_key' => $appkey])->first();
        if(empty($rs)){
            return null;
        }
        $rs = $rs->toArray();
        return $rs;
    }
}