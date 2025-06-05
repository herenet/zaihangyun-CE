<?php

namespace App\SaaSAdmin\Controllers\Manager;

use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\SaaSAdmin\AppKey;
use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\AdminController;
use App\Models\AppleReceiptVerification;
use App\Models\AppleOrder;

class AppleReceiptVerificationController extends AdminController
{
    use AppKey;

    public function index(Content $content)
    {
        return $content
            ->title('票据验证记录')
            ->body($this->grid());
    }

    public function grid()
    {
        $grid = new Grid(new AppleReceiptVerification());
        $grid->model()->where('app_key', $this->getAppKey())->orderBy('created_at', 'desc');
        
        $grid->tools(function ($tools) {
            $tools->append('<a href="/docs/1.x/apis/apple_receipt_verify" class="btn btn-sm btn-primary" target="_blank"><i class="fa fa-book"></i> 查看接口文档</a>');
        });
        
        $grid->fixColumns(2, -2);
        
        // 基础信息
        $grid->column('id', 'ID');
        $grid->column('receipt_data_hash', '票据哈希')->limit(16);
        
        // 验证结果
        $grid->column('verification_status', '验证状态')->using(AppleReceiptVerification::$statusMap)->label([
            AppleReceiptVerification::STATUS_SUCCESS => 'success',
            AppleReceiptVerification::STATUS_FAILED => 'danger',
        ]);
        
        $grid->column('apple_status_code', '苹果状态码')->display(function ($value) {
            if ($value === null) return '-';
            return $value == 0 ? '<span class="label label-success">0 (成功)</span>' : '<span class="label label-danger">'.$value.'</span>';
        });
        
        $grid->column('error_message', '错误信息')->limit(30)->help('验证失败时的具体错误信息');
        
        // 解析后的关键信息
        $grid->column('bundle_id', 'Bundle ID')->limit(20);
        $grid->column('environment', '环境')->using(AppleOrder::$environmentMap)->label([
            AppleOrder::ENVIRONMENT_SANDBOX => 'warning',
            AppleOrder::ENVIRONMENT_PRODUCTION => 'success',
        ]);
        
        $grid->column('transaction_id', '交易ID')->limit(20)->copyable();
        $grid->column('original_transaction_id', '原始交易ID')->limit(20)->copyable();
        $grid->column('product_id', '产品ID')->limit(25);
        
        $grid->column('purchase_date', '购买时间')->display(function ($value) {
            if (empty($value)) {
                return '-';
            }
            
            // 如果是时间戳，直接格式化
            return date('Y-m-d H:i:s', strtotime($value));
        });
        
        $grid->column('quantity', '购买数量')->display(function ($value) {
            return $value ?: '-';
        });
        
        // 时间信息
        $grid->column('updated_at', '更新时间');
        $grid->column('created_at', '验证时间');

        // 筛选器
        $grid->filter(function ($filter) {
            $filter->equal('verification_status', '验证状态')->select(AppleReceiptVerification::$statusMap)->config('minimumResultsForSearch', 'Infinity');
            $filter->equal('environment', '环境')->select([
                'sandbox' => '沙盒环境',
                'production' => '生产环境'
            ])->config('minimumResultsForSearch', 'Infinity');
            $filter->equal('apple_status_code', '苹果状态码');
            $filter->equal('transaction_id', '交易ID');
            $filter->equal('original_transaction_id', '原始交易ID');
            $filter->equal('product_id', '产品ID');
            $filter->equal('bundle_id', 'Bundle ID');
            $filter->like('receipt_data_hash', '票据哈希');
            $filter->between('created_at', '验证时间')->datetime();
        });

        // 导出功能
        $grid->export(function ($export) {
            $export->filename('票据验证记录-'.date('Y-m-d H:i:s').'-'.$this->getAppKey().'.csv');
            $export->column('verification_status', function ($value, $original) {
                return AppleReceiptVerification::$statusMap[$original];
            });
            $export->column('environment', function ($value, $original) {
                return AppleOrder::$environmentMap[$original];
            });
            $export->except(['app_key', 'tenant_id']);
        });

        // 禁用操作
        $grid->disableCreateButton();
        $grid->disableBatchActions();
        $grid->actions(function ($actions) {
            $actions->disableEdit();
            $actions->disableDelete();
        });

        return $grid;
    }

    public function title()
    {
        return '票据验证记录';
    }

    public function detail()
    {
        $id = request()->route('verification');
        $verification = AppleReceiptVerification::with('receiptData')->find($id);
        
        if (!$verification) {
            abort(404, '记录不存在');
        }
        
        $show = new Show($verification);
        
        // 基础信息
        $show->field('id', 'ID');
        $show->field('receipt_data_hash', '票据哈希')->copyable();
        
        // 验证结果
        $show->field('verification_status', '验证状态')->using(AppleReceiptVerification::$statusMap);
        $show->field('apple_status_code', '苹果状态码')->as(function ($value) {
            if ($value === null) return '-';
            return $value == 0 ? '0 (验证成功)' : $value . ' (验证失败)';
        });
        $show->field('error_message', '错误信息')->as(function ($value) {
            return $value ?: '-';
        });
        
        // 解析后的信息
        $show->field('bundle_id', 'Bundle ID');
        $show->field('environment', '环境')->using(AppleOrder::$environmentMap);
        $show->field('transaction_id', '交易ID')->copyable();
        $show->field('original_transaction_id', '原始交易ID')->copyable();
        $show->field('product_id', '产品ID');
        $show->field('purchase_date', '购买时间')->as(function ($value) {
            if (empty($value)) {
                return '-';
            }
            
            return date('Y-m-d H:i:s', strtotime($value));
        });
        $show->field('quantity', '购买数量')->as(function ($value) {
            return $value ?: '-';
        });
        
        // 票据数据
        if ($verification->verification_status == AppleReceiptVerification::STATUS_SUCCESS) {
            $show->field('receiptData.receipt_data', '票据数据')->json();
        } else {
            $show->field('receiptData.receipt_data', '票据数据');
        }
        
        // 时间信息
        $show->field('updated_at', '更新时间');
        $show->field('created_at', '验证时间');

        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
                $tools->disableDelete();
            });

        return $show;
    }
} 