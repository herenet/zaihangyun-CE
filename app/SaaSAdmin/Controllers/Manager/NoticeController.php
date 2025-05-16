<?php

namespace App\SaaSAdmin\Controllers\Manager;

use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Widgets\InfoBox;

class NoticeController extends AdminController
{
    public function index(Content $content)
    {
        return $content
            ->title('通知列表')
            ->body('功能开发中...');
    }
    
}