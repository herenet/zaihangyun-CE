<?php

namespace App\Admin\Actions;

use Encore\Admin\Actions\RowAction;

class AppConfigCopy extends RowAction
{
    public $name = '复制';

    protected $app_key;

    public function __construct($app_key)
    {
        $this->app_key = $app_key;
    }

    public function href()
    {
        return admin_url('app/manager/'.$this->app_key.'/config/create?copy_from=' . $this->getKey());
    }
}