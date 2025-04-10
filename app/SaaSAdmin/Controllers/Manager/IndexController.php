<?php

namespace App\SaaSAdmin\Controllers\Manager;

use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Callout;
use Encore\Admin\Widgets\Carousel;
use Encore\Admin\Widgets\Collapse;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Controllers\AdminController;

class IndexController extends AdminController
{
    public function index(Content $content)
    {
        return $content
        ->title('应用概况')
        ->body(view('manager.partials.stats'));
    }
}