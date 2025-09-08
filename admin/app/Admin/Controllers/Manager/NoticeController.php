<?php

namespace App\Admin\Controllers\Manager;

use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\AdminController;

class NoticeController extends AdminController
{
    public function index(Content $content)
    {
        return $content
            ->title('通知列表')
            ->body('功能开发中...');
    }
    
}