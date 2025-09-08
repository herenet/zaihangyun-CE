<?php

namespace app\controller\api;

use support\Request;
use app\model\Product;
use app\model\IAPProduct;
use app\validate\IAPList;
use app\validate\ProductInfo;
use app\validate\ProductList;

class ProductController
{
    public function list(Request $request)
    {
        $validate = new ProductList();
        if (!$validate->check($request->all())) {
            return json($validate->getErrorInfo());
        }

        $appkey = $request->input('appkey');
        $status = $request->input('status', Product::STATUS_ON);
        $type = $request->input('type', null);

        $product_model = new Product();
        $product_list = $product_model->getProductListByAppKey($appkey, $status, $type);
        return json(['code' => config('const.request_success'), 'msg' => 'success', 'data' => $product_list]);
    }

    public function info(Request $request)
    {
        $validate = new ProductInfo();
        if (!$validate->check($request->all())) {
            return json($validate->getErrorInfo());
        }

        $pid = $request->input('pid');
        $appkey = $request->input('appkey');

        $product_model = new Product();
        $product_info = $product_model->getProductInfoByPid($pid, $appkey);
        return json(['code' => config('const.request_success'), 'msg' => 'success', 'data' => $product_info]);
    }

    public function iapInfo(Request $request)
    {
        $validate = new ProductInfo();
        if (!$validate->check($request->all())) {
            return json($validate->getErrorInfo());
        }

        $pid = $request->input('pid');
        $appkey = $request->input('appkey');

        $iap_model = new IAPProduct();
        $iap_info = $iap_model->getProductInfoByPid($pid, $appkey);
        return json(['code' => config('const.request_success'), 'msg' => 'success', 'data' => $iap_info]);
    }

    public function iapList(Request $request)
    {
        $validate = new IAPList();
        if (!$validate->check($request->all())) {
            return json($validate->getErrorInfo());
        }

        $appkey = $request->input('appkey');
        $status = $request->input('status', IAPProduct::STATUS_ON);
        $type = $request->input('type', null);
        $apple_product_type = $request->input('apple_product_type', null);

        $iap_model = new IAPProduct();
        $iap_list = $iap_model->getProductListByAppKey($appkey, $status, $type, $apple_product_type);
        return json(['code' => config('const.request_success'), 'msg' => 'success', 'data' => $iap_list]);
    }
}