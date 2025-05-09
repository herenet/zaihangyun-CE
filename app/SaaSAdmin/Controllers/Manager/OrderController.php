<?php

namespace App\SaaSAdmin\Controllers\Manager;

use App\Models\Order;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\SaaSAdmin\AppKey;
use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\AdminController;

class OrderController extends AdminController
{
    use AppKey;

    public function index(Content $content)
    {
        return $content
            ->title('订单列表')
            ->body($this->grid());
    }

    protected function grid()
    {
        $grid = new Grid(new Order());
        $grid->model()->where('app_key', $this->getAppKey())->orderBy('created_at', 'desc');
        $grid->fixColumns(2, -2);
        $grid->column('oid', '订单ID');
        $grid->column('uid', '用户ID');
        $grid->column('user.nickname', '用户昵称');
        $grid->column('product_id', '产品ID');
        $grid->column('product.name', '产品名称');
        $grid->column('product_price', '产品价格')->display(function ($value) {
            return '￥'.number_format($value / 100, 2);
        });
        $grid->column('discount_amount', '优惠金额')->display(function ($value) {
            return '￥'.number_format($value / 100, 2);
        });
        $grid->column('order_amount', '订单金额')->display(function ($value) {
            return '￥'.number_format($value / 100, 2);
        });
        $grid->column('payment_amount', '实际支付')->display(function ($value) {
            return '￥'.number_format($value / 100, 2);
        });
        $grid->column('platform_order_amount', '三方订单金额')->display(function ($value) {
            if ($value > 0) {
                return '￥'.number_format($value / 100, 2);
            }
            return null;
        });
        $grid->column('status', '订单状态')->using(Order::$statusMap)->label([
            1 => 'info',
            2 => 'success',
            3 => 'danger',
            4 => 'warning',
        ]);
        $grid->column('pay_channel', '支付方式')->using(Order::$payChannelMap)->prependIcon('pay');
        $grid->column('tid', '第三方订单号');
        $grid->column('trade_type', '交易类型');
        $grid->column('bank_type', '银行类型');
        $grid->column('open_id', '三方用户标识')->limit(32);
        $grid->column('pay_time', '支付时间');
        $grid->column('updated_at', '更新时间');
        $grid->column('created_at', '创建时间');

        $grid->filter(function ($filter) {
            $filter->equal('status', '订单状态')->select(Order::$statusMap)->config('minimumResultsForSearch', 'Infinity');
            $filter->equal('pay_channel', '支付方式')->select(Order::$payChannelMap) ->config('minimumResultsForSearch', 'Infinity');
            $filter->equal('tid', '第三方订单号');
            $filter->equal('uid', '用户ID');
            $filter->equal('product_id', '产品ID');
        });

        $grid->export(function ($export) {
            $export->filename('订单列表-'.date('Y-m-d H:i:s').'-'.$this->getAppKey().'.csv');
            $export->column('status', function ($value, $original) {
                return Order::$statusMap[$original];
            });
            $export->column('pay_channel', function ($value, $original) {
                return Order::$payChannelMap[$original];
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
        $order = new Show(Order::find($oid));
        $order->field('oid', '订单ID');
        $order->field('uid', '用户ID');
        $order->field('product_id', '产品ID');
        $order->field('product_price', '产品价格')->as(function ($value) {
            return '￥'.number_format($value / 100, 2);
        });
        $order->field('discount_amount', '优惠金额')->as(function ($value) {
            return '￥'.number_format($value / 100, 2);
        });
        $order->field('order_amount', '订单金额')->as(function ($value) {
            return '￥'.number_format($value / 100, 2);
        });
        $order->field('payment_amount', '实际支付')->as(function ($value) {
            return '￥'.number_format($value / 100, 2);
        });
        $order->field('status', '订单状态')->using(Order::$statusMap);
        $order->field('pay_channel', '支付方式')->using(Order::$payChannelMap);
        $order->field('tid', '第三方订单号');
        $order->field('trade_type', '交易类型');
        $order->field('bank_type', '银行类型');
        $order->field('open_id', '三方用户标识');
        $order->field('pay_time', '支付时间');
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
