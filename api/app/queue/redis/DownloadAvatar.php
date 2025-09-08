<?php

namespace app\queue\redis;

use support\Log;
use app\model\User;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Webman\RedisQueue\Consumer;
use WebmanTech\LaravelFilesystem\Facades\Storage;


class DownloadAvatar implements Consumer
{
    public $queue = 'download-avatar';

    public $connection = 'default';

    public function consume($data)
    {
        $this->localStoreAvatar($data);
    }

    public function onConsumerFailure(\Throwable $e, $package)
    {
        Log::channel('queue')->error('avatar download consumer failure:'.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
    }

    protected function localStoreAvatar($data)
    {
        try{
            $uid = $data['uid'];

            $user_model = new User();
            $user = $user_model->getUserInfoByUid($uid);
            if(empty($user)){
                Log::channel('queue')->error('user not found:'.$uid);
                return;
            }
            

            $url = $data['avatar_url'];
            $uid_hash = md5($uid);
            $base_path = $user['app_key'].'/'.$uid;
        
            $client = new Client();
            $response = $client->get($url);
            $image = $response->getBody();
            $mime_type = $response->getHeader('Content-Type');
            $file_ext = Arr::get(config('custom.mime_type'), $mime_type, '.jpg');
            $file_path = $base_path.'/'.$uid_hash.$file_ext;
            $avatar_storage = Storage::disk('local_avatar');
            $avatar_storage->deleteDirectory($base_path);
            $avatar_storage->makeDirectory($base_path);
            $avatar_storage->put($file_path, $image);
            $avatar_path = $file_path;

            $user_model = new User();
            $user_model->updateUserInfoByUid($uid, ['avatar' => $avatar_path]);
        }catch(\Exception $e){
            Log::channel('queue')->error('download avatar error:'.$e->getMessage(), ['user' => $user, 'trace' => $e->getTraceAsString()]);
        }
    }
}