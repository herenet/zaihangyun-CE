<?php

namespace App\SaaSAdmin\Actions;

use Encore\Admin\Actions\RowAction;

class FeedbackReply extends RowAction
{
    public $name = '回复';

    protected $app_key;

    public function __construct($app_key)
    {
        $this->app_key = $app_key;
    }

    public function href()
    {
        return admin_url('app/manager/'.$this->app_key.'/feedback/'.$this->getKey().'/reply');
    }
}