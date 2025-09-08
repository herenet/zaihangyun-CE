<?php

namespace app\controller\api;

use support\Request;
use app\model\AppConfig;
use app\model\AppUpgrade;
use app\validate\Upgrade;
use app\model\AppUpgradeChannel;

class AppController
{
    public function config(Request $request)
    {
        $app_key = $request->input('appkey');
        $config_name = $request->input('name');

        if(empty($config_name)){
            return json(['code' => 400101, 'msg' => 'name is required']);
        }

        $app_config_model = new AppConfig();
        $config = $app_config_model->getAppConfigByConfigName($config_name, $app_key);

        if(empty($config)){
            return json(['code' => 400199, 'msg' => 'config not found']);
        }

        return json(['code' => config('const.request_success'), 'msg' => 'success', 'data' => json_decode($config['params'], true)]);
    }

    public function upgrade(Request $request)
    {
        $app_key = $request->input('appkey');

        $validate = new Upgrade();
        if (!$validate->check($request->all())) {
            return json($validate->getErrorInfo());
        }

        $device_uuid = $request->input('device_uuid');
        $channel_name = $request->input('channel_name', AppUpgradeChannel::DEFAULT_CHANNEL_NAME);
        $version_number = $request->input('version_number');

        if(empty($channel_name)){
            $channel_name = AppUpgradeChannel::DEFAULT_CHANNEL_NAME;
        }

        $app_upgrade_channel_model = new AppUpgradeChannel();
        $app_upgrade_channel = $app_upgrade_channel_model->getAppUpgradeChannelByChannelName($app_key, $channel_name);

        if(empty($app_upgrade_channel)){
            return json(['code' => 400198, 'msg' => 'upgrade channel not found']);
        }

        $app_upgrade_model = new AppUpgrade();
        $app_upgrade = $app_upgrade_model->getAppUpgradeByChannelId($app_upgrade_channel['id']);

        if(empty($app_upgrade)){
            return json(['code' => config('const.request_success'), 'msg' => 'success', 'data' => null]);
        }

        $ret = [
            'upgrade' => false,
            'force_upgrade' => false,
            'last_version' => $app_upgrade['version_str'],
            'last_version_number' => $app_upgrade['version_num'],
            'upgrade_from' => $app_upgrade['upgrade_from'],
            'package_download_url' => $app_upgrade['package_download_url'],
            'package_size' => $app_upgrade['package_size'],
            'package_md5' => $app_upgrade['package_md5'],
            'upgrade_note' => $app_upgrade['upgrade_note'],
        ];

        if(!empty($app_upgrade) && $app_upgrade['version_num'] > $version_number){
            if($app_upgrade['gray_upgrade'] == AppUpgrade::GRAY_UPGRADE){
                if(!isInGray($device_uuid, $app_upgrade['gray_percent'])){
                    return json(['code' => config('const.request_success'), 'msg' => 'success', 'data' => $ret]);
                }
            }

            if($app_upgrade['force_upgrade'] == AppUpgrade::FORCE_UPGRADE || $app_upgrade['min_version_num'] >= $version_number){
                $ret['upgrade'] = true;
                $ret['force_upgrade'] = true;
            }else{
                $ret['upgrade'] = true;
                $ret['force_upgrade'] = false;
            }
        }
        return json(['code' => config('const.request_success'), 'msg' => 'success', 'data' => $ret]);
    }
}