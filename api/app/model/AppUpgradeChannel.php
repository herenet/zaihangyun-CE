<?php

namespace app\model;

use support\Model;

class AppUpgradeChannel extends Model
{
    protected $table = 'app_upgrade_channels';
    protected $primaryKey = 'id';
    public $timestamps = false;

    const DEFAULT_CHANNEL_NAME = 'default';

    public function getAppUpgradeChannelByChannelName($appKey, $channelName)
    {
        $rs = $this->where(['app_key' => $appKey, 'channel_name' => $channelName])->first();
        if(empty($rs)){
            return [];
        }
        return $rs->toArray();
    }
}