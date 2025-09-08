<?php

namespace App\Admin\Actions;

use Encore\Admin\Actions\RowAction;

class VersionConfigCopy extends RowAction
{
    public $name = '复制';

    protected $app_key;
    protected $channel_id;

    public function __construct($app_key, $channel_id)
    {
        $this->app_key = $app_key;
        $this->channel_id = $channel_id;
    }

    public function href()
    {
        return admin_url('app/manager/'.$this->app_key.'/version/'.$this->channel_id.'/item/create?copy_from=' . $this->getKey());
    }
}