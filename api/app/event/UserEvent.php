<?php

namespace app\event;

use Webman\RedisQueue\Client;

class UserEvent
{
    function downloadAvatar($data)
    {
        $queue = 'download-avatar';
        // var_dump($data);
        // var_dump($avatar);

        Client::send($queue, $data);
    }
}