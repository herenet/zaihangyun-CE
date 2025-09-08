<?php

namespace app\model;

use app\lib\ZHYToken;
use support\Cache;
use support\Model;

class UserToken extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_tokens';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    const CACHE_TTL = 24*60*60*365;

    const CACHE_KEY_USER_TOKEN_INFO = 'user_token|';

    public function getTokenInfoByToken($token)
    {
        $cacheKey = self::CACHE_KEY_USER_TOKEN_INFO . $token;
        $tokenInfo = ZHYToken::parseToken($token);
        if(empty($tokenInfo)){
            return null;
        }
        list($appkey, $uid) = array_values($tokenInfo);
        $rs = Cache::get($cacheKey);
        if(empty($rs)){
            $rs = $this->where(['token' => $token, 'app_key' => $appkey, 'uid' => $uid])->first();
            if(empty($rs)){
                return null;
            }
            $rs = $rs->toArray();
            Cache::set($cacheKey, $rs, self::CACHE_TTL);
        }
        return $rs;
    }

    public function deleteUserTokenById($id, $token)
    {
        $cacheKey = self::CACHE_KEY_USER_TOKEN_INFO . $token;
        Cache::delete($cacheKey);
        return $this->where(['id' => $id])->delete();
    }

    public function getTokenListByUid($uid)
    {
        $rs = $this->where(['uid' => $uid])->orderBy('created_at', 'asc')->get();
        if(empty($rs)){
            return [];
        }
        $rs = $rs->toArray();
        return $rs;
    }

    public function addUserToken(array $data)
    {
        return $this->insert([
            'uid' => $data['uid'],
            'token' => $data['token'],
            'app_key' => $data['app_key'],
            'oaid' => $data['oaid'],
            'device_id' => $data['device_id'],
            'ip' => $data['ip'],
            'expired_at' => $data['expired_at'],
        ]);
    }
}
