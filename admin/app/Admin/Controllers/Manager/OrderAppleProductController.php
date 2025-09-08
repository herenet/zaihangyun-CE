<?php

namespace App\Admin\Controllers\Manager;

use App\Libs\Helpers;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Admin\AppKey;
use App\Models\IAPProduct;
use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\AdminController;

class OrderAppleProductController extends AdminController
{
    use AppKey;

    public function index(Content $content)
    {
        
        return $content
        ->title('产品列表')
        ->body($this->grid());
    }

    public function grid()
    {
        $grid = new Grid(new IAPProduct());
        $grid->model()->where('app_key', $this->getAppKey());
        $grid->model()->orderBy('order', 'asc')->orderBy('created_at', 'desc');
        $grid->fixColumns(2, -2);
        $grid->tools(function ($tools) {
            $tools->append('<a href="/docs/1.x/apis/apple_product_list" class="btn btn-sm btn-primary" target="_blank"><i class="fa fa-book"></i> 查看接口文档</a>');
        });
        // 基础信息
        $grid->column('pid', 'PID');
        $grid->column('name', '产品名称');
        $grid->column('sub_name', '子标题');
        $grid->column('iap_product_id', '苹果产品ID');
        $grid->column('type', '功能模型')->using(IAPProduct::$typeMap);
        $grid->column('apple_product_type', '苹果产品类型')->using(IAPProduct::$productTypeMap);
        $grid->column('subscription_duration', '订阅时长')->using(IAPProduct::$subscriptionDurationTypeMap);
        $grid->column('function_value', '产品功能值');
        $grid->column('cross_price', '划线价')->display(function ($value) {
            return '￥'.number_format($value / 100, 2);
        });
        $grid->column('sale_price', '售价')->display(function ($value) {
            return '￥'.number_format($value / 100, 2);
        });
        $grid->column('sale_status', '销售状态')->switch([
            'on' => ['value' => 1, 'text' => '在售', 'color' => 'success'],
            'off' => ['value' => 2, 'text' => '待售', 'color' => 'primary'],
        ]);
        $grid->order('排序')->orderable();
        $grid->column('ext_data', '扩展数据')->limit(30);
       
        $grid->column('updated_at', '更新时间')->sortable([]);
        $grid->column('created_at', '注册时间')->sortable();

        // 筛选器
        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->like('name', '产品名称');
            $filter->like('sub_name', '子标题');
            $filter->equal('apple_product_type', '苹果产品类型')->select(IAPProduct::$productTypeMap);
            $filter->equal('type', '功能模型')->select(IAPProduct::$typeMap);
            $filter->equal('sale_status', '销售状态')->radio(IAPProduct::$saleStatusMap);
        });

        // 配置导出
        $grid->export(function ($export) {
            $export->filename('产品数据-'.date('Y-m-d H:i:s').'-'.$this->getAppKey().'.csv');
             
            $export->except(['order']);
            $export->originalValue(['ext_data']);

            $export->column('type', function ($value, $original) {
                return IAPProduct::$typeMap[$original];
            });
            $export->column('sale_status', function ($value, $original) {
                return IAPProduct::$saleStatusMap[$original];
            });
            $export->column('apple_product_type', function ($value, $original) {
                return IAPProduct::$productTypeMap[$original];
            });
            $export->column('subscription_duration', function ($value, $original) {
                return IAPProduct::$subscriptionDurationTypeMap[$original];
            });
            $export->column('cross_price', function ($value, $original) {
                return '￥'.number_format($original / 100, 2);
            });
            $export->column('sale_price', function ($value, $original) {
                return '￥'.number_format($original / 100, 2);
            });
        });
        // 禁用批量删除
        $grid->disableBatchActions();

        return $grid;
    }

    public function edit($id, Content $content)
    {
        $id = request()->route('product');
        return parent::edit($id, $content)->title('产品信息')->description('编辑');
    }

    public function update($id)
    {
        $id = request()->route('product');
        return parent::update($id);
    }

    public function form()
    {
        $form = new Form(new IAPProduct());

        // 在表单显示之前处理
        $form->editing(function ($form) {
            $form->model()->cross_price = $form->model()->cross_price / 100;
            $form->model()->sale_price = $form->model()->sale_price / 100;
        });

        $form->setWidth(6, 3);
        $form->text('name', '产品名称')->rules(['required', 'string', 'max:64']);
        $form->text('sub_name', '子标题')->rules(['nullable', 'string', 'max:64']);
        $form->text('iap_product_id', '苹果产品ID')
            ->rules(['required', 'string', 'max:128'])
            ->creationRules(['unique:iap_products,iap_product_id,NULL,pid,app_key,'.$this->getAppKey()])
            ->updateRules(['unique:iap_products,iap_product_id,{{id}},pid,app_key,'.$this->getAppKey()])
            ->help('苹果产品ID为苹果后台的商品ID，请在苹果后台查看。');
        $form->select('type', '功能模型')
            ->options(IAPProduct::$typeMap)
            ->config('allowClear', false)
            ->config('minimumResultsForSearch', 'Infinity')
            ->when(1, function (Form $form) {
                if($form->isEditing()) {
                    $form->text('function_value', 'VIP时长')
                    ->rules(['required', 'integer', 'min:1', 'max:9999'])
                    ->append('天')
                    ->help('VIP时长为设置的天数，则用户购买后，VIP有效期从购买当天开始计算，如会员时长未到期，则会员时长将叠加。');
                }else{
                    $form->text('function_value', 'VIP时长')
                    ->default(30)
                    ->rules(['required', 'integer', 'min:1', 'max:9999'])
                    ->append('天')
                    ->help('VIP时长为设置的天数，则用户购买后，VIP有效期从购买当天开始计算，如会员时长未到期，则会员时长将叠加。');
                }
            })
            ->when(2, function (Form $form) {
                $form->html('<span class="text-success"><i class="fa fa-info"></i> 用户购买后，标记为永久会员。</span>');
            })
            // ->when(3, function (Form $form) {
            //     $form->text('function_value', '产品功能值')->rules(['nullable', 'string', 'max:64']);
            // })
            ->rules(['required', 'integer', 'max:64']);
        $form->select('apple_product_type', '苹果产品类型')
            ->config('allowClear', false)
            ->config('minimumResultsForSearch', 'Infinity')
            ->options(IAPProduct::$productTypeMap)
            ->default(IAPProduct::PRODUCT_TYPE_CONSUMABLE)
            ->rules(['required', 'integer', 'max:64'])
            ->when(IAPProduct::PRODUCT_TYPE_AUTO_RENEWABLE, function (Form $form) {
                $form->select('subscription_duration', '订阅时长')
                    ->config('allowClear', false)
                    ->config('minimumResultsForSearch', 'Infinity')
                    ->options(IAPProduct::$subscriptionDurationTypeMap)
                    ->default(IAPProduct::SUBSCRIPTION_DURATION_TYPE_ONE_MONTH)
                    ->rules(['required_if:is_subscription,1', 'integer', 'max:64'])
                    ->help('用于对应苹果订阅时长类型展示，不作功能处理。');
            })
            ->when(IAPProduct::PRODUCT_TYPE_NON_RENEWING, function (Form $form) {
                $form->select('subscription_duration', '订阅时长')
                    ->config('allowClear', false)
                    ->config('minimumResultsForSearch', 'Infinity')
                    ->options(IAPProduct::$subscriptionDurationTypeMap)
                    ->default(IAPProduct::SUBSCRIPTION_DURATION_TYPE_ONE_MONTH)
                    ->rules(['required_if:is_subscription,1', 'integer', 'max:64'])
                    ->help('用于对应苹果订阅时长类型展示，不作功能处理。');
            })
            ->help('系统会根据产品类型来处理产品是否允许重复购买。');
        $form->currency('cross_price', '划线价')->symbol('￥')
            ->default(0)
            ->rules([
                'required', 
                'regex:/^([0-9]{1,6})(\.[0-9]{1,2})?$/', 
                'not_in:null'
            ]);
        $form->currency('sale_price', '售价')->symbol('￥')
            ->default(0)
            ->rules([
                'required', 
                'regex:/^([0-9]{1,6})(\.[0-9]{1,2})?$/', 
                'not_in:null'
            ]);
        $form->radio('sale_status', '销售状态')
            ->options(IAPProduct::$saleStatusMap)
            ->default(1)
            ->rules(['required', 'integer', 'max:64']);
        
        $form->textarea('desc', '商品描述')->rules(['nullable', 'string', 'max:256']);
        $form->json('ext_data', '扩展数据')->rules(['nullable', 'string', 'max:256']);

        $form->saving(function (Form $form) {
            if($form->isCreating()) {
                $form->model()->pid = Helpers::generateProductId();
                $form->model()->app_key = $this->getAppKey();
            }

            $form->cross_price = $form->cross_price * 100;
            $form->sale_price = $form->sale_price * 100;
            if($form->type == IAPProduct::TYPE_VALUE_KEY_FOR_FOREVER_MEMBER) {
                $form->function_value = IAPProduct::FOREVER_VIP_FUNCTION_VALUE;
            }
        });

        $form->saved(function (Form $form) {
            admin_toastr('添加成功', 'success');
        });

        $form->tools(function(Form\Tools $tools){
            $tools->disableView();
            $tools->disableDelete();
        });

        $form->footer(function(Form\Footer $footer){
            $footer->disableViewCheck();
            $footer->disableEditingCheck();
            $footer->disableCreatingCheck();
            $footer->disableReset();
        });
        
        return $form;
    }

    public function title()
    {
        return '产品信息';
    }

    public function detail()
    {
        $pid = request()->route('product');
        $show = new Show(IAPProduct::find($pid));
        $show->field('pid', 'PID');
        $show->field('name', '产品名称');
        $show->field('sub_name', '子标题');
        $show->field('iap_product_id', '苹果产品ID');
        $show->field('type', '功能模型')->using(IAPProduct::$typeMap);
        $show->field('apple_product_type', '苹果产品类型')->using(IAPProduct::$productTypeMap);
        $show->field('subscription_duration', '订阅时长')->using(IAPProduct::$subscriptionDurationTypeMap);
        $show->field('function_value', '产品功能值');
        $show->field('cross_price', '划线价')->as(function ($value) {
            return '￥'.number_format($value / 100, 2);
        });
        $show->field('sale_price', '售价')->as(function ($value) {
            return '￥'.number_format($value / 100, 2);
        });
        $show->field('sale_status', '销售状态')->using(IAPProduct::$saleStatusMap);
        $show->field('desc', '商品描述');
        $show->field('ext_data', '扩展数据')->limit(30);
        // $show->field('order', '排序');
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