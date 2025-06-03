<?php

namespace App\SaaSAdmin\Controllers\Manager;

use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\SaaSAdmin\AppKey;
use App\Models\AppleOrder;
use App\Models\AppleNotification;
use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\AdminController;

class AppleNotificationController extends AdminController
{
    use AppKey;

    public function index(Content $content)
    {
        return $content
            ->title('苹果通知列表')
            ->body($this->grid());
    }

    public function grid()
    {
        $grid = new Grid(new AppleNotification());
        $grid->model()->where('app_key', $this->getAppKey())->orderBy('created_at', 'desc');
        
        $grid->column('id', 'ID');
        $grid->column('notification_type', '通知类型');
        $grid->column('subtype', '子类型');
        $grid->column('transaction_id', '交易ID');
        $grid->column('original_transaction_id', '原始交易ID');
        $grid->column('environment', '环境')->display(function ($value) {
            return strtolower($value) === strtolower(AppleOrder::ENVIRONMENT_SANDBOX) ? 
                AppleOrder::$environmentMap[AppleOrder::ENVIRONMENT_SANDBOX] : 
                AppleOrder::$environmentMap[AppleOrder::ENVIRONMENT_PRODUCTION];
        })->label([
            'sandbox' => 'warning',
            'production' => 'success',
        ]);
        $grid->column('processed', '处理状态')->display(function ($value) {
            return $value ? '已处理' : '待处理';
        })->label([
            0 => 'warning',
            1 => 'success',
        ]);
        $grid->column('process_result', '处理结果')->limit(50);
        $grid->column('created_at', '创建时间');
        
        $grid->filter(function ($filter) {
            $filter->equal('notification_type', '通知类型');
            $filter->equal('environment', '环境')->select(['sandbox' => '沙盒', 'production' => '生产']);
            $filter->equal('processed', '处理状态')->select([0 => '待处理', 1 => '已处理']);
            $filter->equal('transaction_id', '交易ID');
            $filter->equal('original_transaction_id', '原始交易ID');
        });
        
        $grid->disableCreateButton();
        $grid->disableBatchActions();
        $grid->actions(function ($actions) {
            $actions->disableEdit();
            $actions->disableDelete();
        });
        
        return $grid;
    }

    public function detail()
    {
        $id = request()->route('notifications');
        $show = new Show(AppleNotification::find($id));
        
        $show->field('id', 'ID');
        $show->field('notification_uuid', '通知UUID');
        $show->field('notification_type', '通知类型');
        $show->field('subtype', '子类型');
        $show->field('transaction_id', '交易ID');
        $show->field('original_transaction_id', '原始交易ID');
        $show->field('environment', '环境')->using(AppleOrder::$environmentMap);
        $show->field('processed', '处理状态')->using(AppleNotification::$processedMap);
        $show->field('process_result', '处理结果');
        // $show->field('notification_data', '通知数据')->json();
        $show->field('created_at', '创建时间');
        $show->field('updated_at', '更新时间');

        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
                $tools->disableDelete();
            });

        return $show;
    }
} 