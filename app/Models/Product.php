<?php

namespace App\Models;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Spatie\EloquentSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\SortableTrait;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model implements Sortable
{
    use DefaultDatetimeFormat, SortableTrait, SoftDeletes;

    protected $table = 'products';

    protected $primaryKey = 'pid';

    protected $autoIncrement = false;

    public $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
        // 'ignore_timestamps' => false,
    ];

    static $fields_map = [
        'pid' => 'PID',
        'app_key' => '应用Key',
        'name' => '产品名称',
        'sub_name' => '子标题',
        'type' => '产品类型',
        'function_value' => '产品功能值',
        'cross_price' => '划线价',
        'sale_price' => '售价',
        'desc' => '商品描述',
        'sale_status' => '销售状态',
        'platform_type' => '适用平台',
        'order' => '排序',
        'created_at' => '创建时间',
        'updated_at' => '更新时间',
    ];

    const TYPE_VALUE_KEY_FOR_DURATION_MEMBER = 1;
    const TYPE_VALUE_KEY_FOR_FOREVER_MEMBER = 2;
    // const TYPE_VALUE_KEY_FOR_CUSTOM_FUNCTION = 99;
    
    public static $typeMap = [
        self::TYPE_VALUE_KEY_FOR_DURATION_MEMBER => '会员时长',
        self::TYPE_VALUE_KEY_FOR_FOREVER_MEMBER => '永久会员',
        // self::TYPE_VALUE_KEY_FOR_CUSTOM_FUNCTION => '自定义',
    ];

    public static $saleStatusMap = [
        1 => '在售',
        2 => '待售'
    ];

    public static $platformTypeMap = [
        1 => '所有平台',
        2 => '安卓',
        3 => '鸿蒙',
    ];

    protected $fillable = [
        'pid',
        'app_key',
        'name',
        'sub_name',
        'type',
        'function_value',
        'cross_price',
        'sale_price',
        'desc',
        'sale_status',
        'platform_type',
        'order',
        'ext_data',
    ];

    const FOREVER_VIP_FUNCTION_VALUE = 'forever_vip';

    // 增加构建排序查询的方法
    public function buildSortQuery()
    {
        // 仅用同一个 app_key 的记录进行排序比较
        return static::where('app_key', $this->app_key); 
    }

    // public function setHighestOrderNumber()
    // {
    //     $this->order = $this->getHighestOrderNumber() + 1;
    // }
}
