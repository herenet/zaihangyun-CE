<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    /**
     * 首页
     */
    public function index()
    {
        return view('welcome');
    }

    /**
     * 价格页面
     */
    public function pricing()
    {
        $products = config('product');
        
        // 处理价格显示（从分转换为元）
        foreach ($products as $key => &$product) {
            $product['price_yuan'] = $product['price'] / 100;
            $product['key'] = $key;
        }
        
        return view('pricing', compact('products'));
    }

    /**
     * 关于我们页面
     */
    public function about()
    {
        return view('about');
    }
} 