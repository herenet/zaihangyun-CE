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
        $cases = $this->getCases();
        
        return view('welcome', compact('cases'));
    }

    /**
     * 价格页面
     */
    public function pricing()
    {
        $products = config('product');
        
        // 处理价格显示（从分转换为元）
        foreach ($products as $key => &$product) {
            if ($product['price'] === 'contact') {
                $product['price_yuan'] = 'contact';
            } else {
                $product['price_yuan'] = $product['price'] / 100;
            }
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

    /**
     * 获取成功案例数据
     */
    private function getCases()
    {
        return [
            // 第一行案例（向右滚动）
            'row1' => [
                [
                    'name' => '时间大师',
                    'description' => '自律养成，时间管理神器',
                    'icon' => 'sjds.png',
                    'gradient' => 'linear-gradient(135deg, #4086F5 0%, #1AE2D6 100%)',
                    'dot_color' => '#1AE2D6',
                    'dot_color_small' => '#4086F5'
                ],
                [
                    'name' => '家长守护',
                    'description' => '管控孩子手机，防沉迷工具',
                    'icon' => 'jzsh.png',
                    'gradient' => 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
                    'dot_color' => '#10b981',
                    'dot_color_small' => '#059669'
                ],
                [
                    'name' => '钢笔字帖',
                    'description' => '练字神器，手机练字',
                    'icon' => 'gbzt.png',
                    'gradient' => 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
                    'dot_color' => '#f59e0b',
                    'dot_color_small' => '#d97706'
                ],
                [
                    'name' => '解压缩全能王',
                    'description' => '解压缩全能王，各平台Top10应用',
                    'icon' => 'jysqnw.png',
                    'gradient' => 'linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%)',
                    'dot_color' => '#8b5cf6',
                    'dot_color_small' => '#7c3aed'
                ],
                [
                    'name' => '草稿纸',
                    'description' => '学习工作打草稿，灵感记录工具',
                    'icon' => 'cgz.png',
                    'gradient' => 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)',
                    'dot_color' => '#ef4444',
                    'dot_color_small' => '#dc2626'
                ],
                [
                    'name' => 'Scratch图形编程',
                    'description' => '少儿Scratch图形编程工具',
                    'icon' => 'scratch.png',
                    'gradient' => 'linear-gradient(135deg, #06b6d4 0%, #0891b2 100%)',
                    'dot_color' => '#06b6d4',
                    'dot_color_small' => '#0891b2'
                ],
                [
                    'name' => '滑动相册清理',
                    'description' => '像刷视频一样整理回忆，清理相册空间',
                    'icon' => 'hdxcql.png',
                    'gradient' => 'linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)',
                    'dot_color' => '#6366f1',
                    'dot_color_small' => '#4f46e5'
                ],
                [
                    'name' => 'FM手机调频收音机',
                    'description' => '频道多内容丰富清晰无需耳机的收音机',
                    'icon' => 'fmsyj.png',
                    'gradient' => 'linear-gradient(135deg, #66f163 0%, #4f16e5 100%)',
                    'dot_color' => '#66f163',
                    'dot_color_small' => '#4f16e5'
                ],
                [
                    'name' => '极简白板',
                    'description' => '简洁高效的移动白板',
                    'icon' => 'jjbb.png',
                    'gradient' => 'linear-gradient(135deg, #f16366 0%, #e54f16 100%)',
                    'dot_color' => '#f16366',
                    'dot_color_small' => '#e54f16'
                ]
            ],
            // 第二行案例（向左滚动）
            'row2' => [
                [
                    'name' => '猿爸爸守护',
                    'description' => '孩子手机防沉迷，家长守护好助手',
                    'icon' => 'ybb.png',
                    'gradient' => 'linear-gradient(135deg, #ec4899 0%, #be185d 100%)',
                    'dot_color' => '#ec4899',
                    'dot_color_small' => '#be185d'
                ],
                [
                    'name' => 'C语言编译器IDE',
                    'description' => 'C语言编译器IDE学习C语言好帮手',
                    'icon' => 'cyy.png',
                    'gradient' => 'linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%)',
                    'dot_color' => '#0ea5e9',
                    'dot_color_small' => '#0284c7'
                ],
                [
                    'name' => '手机远程协助控制',
                    'description' => '无需root超低延迟，远程协助',
                    'icon' => 'sjyckz.png',
                    'gradient' => 'linear-gradient(135deg, #a855f7 0%, #9333ea 100%)',
                    'dot_color' => '#a855f7',
                    'dot_color_small' => '#9333ea'
                ],
                [
                    'name' => 'USB摄像头',
                    'description' => 'OTG免驱高清直连',
                    'icon' => 'usbsxt.png',
                    'gradient' => 'linear-gradient(135deg, #f97316 0%, #ea580c 100%)',
                    'dot_color' => '#f97316',
                    'dot_color_small' => '#ea580c'
                ],
                [
                    'name' => '时间印记',
                    'description' => '重要日子提醒，记录美好瞬间',
                    'icon' => 'sjyj.png',
                    'gradient' => 'linear-gradient(135deg, #84cc16 0%, #65a30d 100%)',
                    'dot_color' => '#84cc16',
                    'dot_color_small' => '#65a30d'
                ],
                [
                    'name' => '小谷地球',
                    'description' => '高清卫星地图，卫星地图看家乡',
                    'icon' => 'xgdq.png',
                    'gradient' => 'linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)',
                    'dot_color' => '#6366f1',
                    'dot_color_small' => '#4f46e5'
                ],
                [
                    'name' => '视频格式转换工厂',
                    'description' => '全能格式转换，快速便捷',
                    'icon' => 'spgszhgc.png',
                    'gradient' => 'linear-gradient(135deg, #6316f1 0%, #4f16e5 100%)',
                    'dot_color' => '#6316f1',
                    'dot_color_small' => '#4f16e5'
                ],
                [
                    'name' => '电池容量检测管理',
                    'description' => '电池寿命电池容量检测专家',
                    'icon' => 'dcyljcgl.png',
                    'gradient' => 'linear-gradient(135deg, #16f163 0%, #16e54f 100%)',
                    'dot_color' => '#16f163',
                    'dot_color_small' => '#16e54f'
                ],
                [
                    'name' => 'NFC百宝箱',
                    'description' => '专业NFC工具箱',
                    'icon' => 'nfcbbx.png',
                    'gradient' => 'linear-gradient(135deg, #f16366 0%, #e54f16 100%)',
                    'dot_color' => '#f16366',
                    'dot_color_small' => '#e54f16'
                ]
            ]
        ];
    }
} 