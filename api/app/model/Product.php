<?php

namespace app\model;

use support\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'pid';
    public $incrementing = false;
    public $timestamps = false;

    const STATUS_ON = 1;
    const STATUS_OFF = 2;

    const PLATFORM_ALL = 1;
    const PLATFORM_IOS = 2;
    const PLATFORM_ANDROID = 3;

    const TYPE_MEMBER_DURATION = 1;
    const TYPE_MEMBER_FOREVER = 2;
    const TYPE_MEMBER_CUSTOM = 99;

    public $hidden = ['app_key', 'updated_at', 'created_at', 'order', 'deleted_at', 'platform_type'];

    public function getProductListByAppKey($appkey, $status = null, $type = null, $limit = 100, $offset = 0)
    {
        $query = $this->where('app_key', $appkey)->orderBy('order', 'asc');
        if(!empty($status)){
            $query->where('sale_status', $status);
        }
        if(!empty($type)){
            $query->where('type', $type);
        }
        $query->where('deleted_at', null);
        $product_list = $query->limit($limit)->offset($offset)->get();
        if(empty($product_list)){
            return [];
        }
        $product_list = $product_list->toArray();
        return $product_list;
    }

    public function getProductInfoByPid($pid, $appkey)
    {
        $product = $this->where('pid', $pid)->where('app_key', $appkey)->first();
        if(empty($product)){
            return [];
        }
        $product = $product->toArray();
        return $product;
    }

    public function getProductsByPids(array $pids)
    {
        $products = $this->whereIn('pid', $pids)->get();
        if(empty($products)){
            return [];
        }
        $products = $products->toArray();
        return $products;
    }
}