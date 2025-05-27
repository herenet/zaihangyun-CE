<?php

namespace App\SaaSAdmin\Controllers\Manager;

use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\SaaSAdmin\AppKey;
use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\AdminController;
use App\Models\AppleOrder;

class AppleOrderController extends AdminController
{
    use AppKey;

    public function index(Content $content)
    {
        return $content
            ->title('订单列表')
            ->body($this->grid());
    }

    public function grid()
    {
        $grid = new Grid(new AppleOrder());
        $grid->model()->where('app_key', $this->getAppKey())->orderBy('created_at', 'desc');
        $grid->tools(function ($tools) {
            $tools->append('<a href="/docs/1.x/apis/apple_order_list" class="btn btn-sm btn-primary" target="_blank"><i class="fa fa-book"></i> 查看接口文档</a>');
        });
        $grid->fixColumns(2, -2);
        $grid->column('oid', '订单ID');
        $grid->column('uid', '用户ID');
        $grid->column('user.nickname', '用户昵称');
        $grid->column('product_id', '产品ID');
        $grid->column('apple_product_id', '苹果产品ID');
        $grid->column('product.name', '产品名称');
        $grid->column('product.is_subscription', '是否订阅')->using([0 => '否', 1 => '是'])->label([
            0 => 'info',
            1 => 'success'
        ]);
        $grid->column('product.sale_price', '产品价格')->display(function ($value) {
            return '￥'.number_format($value / 100, 2);
        });
        $grid->column('amount', '订单金额')->display(function ($value) {
            return '￥'.number_format($value / 100, 2);
        })->help('此为内部产品售价金额，并非实际支付金额');
        $grid->column('platform_order_amount', '三方订单金额')->display(function ($value) {
            if ($value > 0) {
                return '￥'.number_format($value / 100, 2);
            }
            return null;
        });
        $grid->column('payment_status', '支付状态')->using(AppleOrder::$paymentStatusMap)->label([
            AppleOrder::PAYMENT_STATUS_PENDING => 'info',
            AppleOrder::PAYMENT_STATUS_SUCCESS => 'success',
            AppleOrder::PAYMENT_STATUS_FAILED => 'danger',
            AppleOrder::PAYMENT_STATUS_REFUNDED => 'warning',
        ]);
        $grid->column('subscription_status', '订阅状态')->using(AppleOrder::$subscriptionStatusMap)->prependIcon('pay');
        $grid->column('transaction_id', '苹果交易ID');
        $grid->column('original_transaction_id', '原始交易ID');
        $grid->column('environment', '环境')->using(AppleOrder::$environmentMap)->label([
            AppleOrder::ENVIRONMENT_SANDBOX => 'info',
            AppleOrder::ENVIRONMENT_PRODUCTION => 'success',
        ]);
        $grid->column('is_trial_period', '是否试用期')->using([0 => '否', 1 => '是']);
        $grid->column('is_in_intro_offer_period', '是否促销期')->using([0 => '否', 1 => '是']);
        $grid->column('expires_date', '订阅过期时间');
        $grid->column('grace_period_expires_date', '宽限期过期时间');
        $grid->column('auto_renew_status', '自动续订状态')->using([0 => '关闭', 1 => '开启']);
        $grid->column('auto_renew_product_id', '下一周期续订的产品ID');
        $grid->column('purchase_date', '购买时间');
        $grid->column('original_purchase_date', '原始购买时间');
        $grid->column('cancellation_date', '取消时间');
        $grid->column('data_source', '数据来源')->using(AppleOrder::$dataSourceMap)->dot([
            AppleOrder::DATA_SOURCE_RECEIPT => 'info',
            AppleOrder::DATA_SOURCE_S2S => 'success',
        ]);
        $grid->column('updated_at', '更新时间');
        $grid->column('created_at', '创建时间');

        $grid->filter(function ($filter) {
            $filter->equal('payment_status', '支付状态')->select(AppleOrder::$paymentStatusMap)->config('minimumResultsForSearch', 'Infinity');
            $filter->equal('subscription_status', '订阅状态')->select(AppleOrder::$subscriptionStatusMap)->config('minimumResultsForSearch', 'Infinity');
            $filter->equal('transaction_id', '苹果交易ID');
            $filter->equal('uid', '用户ID');
            $filter->equal('product_id', '产品ID');
        });

        $grid->export(function ($export) {
            $export->filename('订单列表-'.date('Y-m-d H:i:s').'-'.$this->getAppKey().'.csv');
            $export->column('payment_status', function ($value, $original) {
                return AppleOrder::$paymentStatusMap[$original];
            });
            $export->column('subscription_status', function ($value, $original) {
                return AppleOrder::$subscriptionStatusMap[$original];
            });
            $export->except(['app_key', 'tenant_id']);
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
        $oid = request()->route('list');
        $order = new Show(AppleOrder::find($oid));
        $order->field('oid', '订单ID');
        $order->field('uid', '用户ID');
        $order->field('user.nickname', '用户昵称');
        $order->field('product_id', '产品ID');
        $order->field('product.name', '产品名称');
        $order->field('product.sale_price', '产品价格')->as(function ($value) {
            return '￥'.number_format($value / 100, 2);
        });
        $order->field('amount', '订单金额')->as(function ($value) {
            return '￥'.number_format($value / 100, 2);
        });
        $order->field('payment_status', '支付状态')->using(AppleOrder::$paymentStatusMap);
        $order->field('subscription_status', '订阅状态')->using(AppleOrder::$subscriptionStatusMap);
        $order->field('transaction_id', '苹果交易ID');
        $order->field('original_transaction_id', '原始交易ID');
        $order->field('environment', '环境')->using(AppleOrder::$environmentMap);
        $order->field('is_trial_period', '是否试用期')->using([0 => '否', 1 => '是']);
        $order->field('is_in_intro_offer_period', '是否促销期')->using([0 => '否', 1 => '是']);
        $order->field('expires_date', '订阅过期时间');
        $order->field('grace_period_expires_date', '宽限期过期时间');
        $order->field('auto_renew_status', '自动续订状态')->using([0 => '关闭', 1 => '开启']);
        $order->field('auto_renew_product_id', '下一周期续订的产品ID');
        $order->field('purchase_date', '购买时间');
        $order->field('original_purchase_date', '原始购买时间');
        $order->field('cancellation_date', '取消时间');
        $order->field('data_source', '数据来源')->using(AppleOrder::$dataSourceMap);
        $order->field('updated_at', '更新时间');
        $order->field('created_at', '创建时间');

        $order->panel()
        ->tools(function ($tools) {
            $tools->disableEdit();
            $tools->disableDelete();
        });

        return $order;
        
    }
}
