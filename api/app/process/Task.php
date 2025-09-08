<?php
namespace app\process;

use support\Db;
use app\model\User;
use Illuminate\Support\Arr;
use Workerman\Crontab\Crontab;
use app\model\LoginInterfaceConfig;

class Task
{
    public function onWorkerStart()
    {

        // // 每秒钟执行一次
        // new Crontab('*/1 * * * * *', function(){
            // dump(date('Y-m-d H:i:s')."\n");
            // $this->clearCanceledUser();
        // });

        // // 每5秒执行一次
        // new Crontab('*/5 * * * * *', function(){
        //     echo date('Y-m-d H:i:s')."\n";
        // });

        // // 每分钟执行一次
        // new Crontab('0 */1 * * * *', function(){
        //     echo date('Y-m-d H:i:s')."\n";
        // });

        // // 每5分钟执行一次
        // new Crontab('0 */5 * * * *', function(){
        //     echo date('Y-m-d H:i:s')."\n";
        // });

        // // 每分钟的第一秒执行
        // new Crontab('1 * * * * *', function(){
        //     echo date('Y-m-d H:i:s')."\n";
        // });

        // 每天的7点50执行，注意这里省略了秒位
        new Crontab('10 0 * * *', function(){
            $this->clearCanceledUser();
        });
    }

    public function clearCanceledUser()
    {
        $login_interface_config = new LoginInterfaceConfig();
        Db::table('users')->orderBy('uid')->whereNotNull('canceled_at')->chunkById(100, function ($users) use ($login_interface_config) {
            foreach ($users as $user) {
                $interface_config = $login_interface_config->getLoginInterfaceConfigByAppKey($user['app_key']);
                $cancel_after_days = Arr::get($interface_config, 'cancel_after_days', 15);
                if(strtotime($user['canceled_at']) < time() - $cancel_after_days * 24 * 60 * 60){
                    $user_model = new User();
                    $user_model->deleteUserByUid($user['uid']);
                }
            }
        }, 'uid');
    }
}