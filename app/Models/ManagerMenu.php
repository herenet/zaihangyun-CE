<?php

namespace App\Models;

use App\Models\App;

/**
 * Class Menu.
 *
 * @property int $id
 *
 * @method where($parent_id, $id)
 */
class ManagerMenu
{
    /**
     * @return array
     */
    public function allNodes($appKey): array
    {
        return [
            [
                'id' => 1,
                'parent_id' => 0,
                'title' => '应用概况',
                'icon' => 'fa-dashboard',
                'uri' => 'app/manager/' . $appKey,
            ],
            [
                'id' => 2,
                'parent_id' => 0,
                'title' => '应用设置',
                'icon' => 'fa-cog',
                'children' => [
                    [
                        'id' => 21,
                        'parent_id' => 2,
                        'title' => '配置下发',
                        'uri' => 'app/manager/' . $appKey. '/config',
                    ],
                    [
                        'id' => 22,
                        'parent_id' => 2,
                        'title' => '版本管理',
                        'uri' => 'app/manager/' . $appKey. '/upgrade',
                    ],
                ],
            ],
            [
                'id' => 3,
                'parent_id' => 0,
                'title' => '用户管理',
                'icon' => 'fa-user',
                'children' => [
                    [
                        'id' => 31,
                        'parent_id' => 3,
                        'title' => '用户列表',
                        'uri' => 'app/manager/' . $appKey. '/user/list',
                    ],
                    [
                        'id' => 22,
                        'parent_id' => 2,
                        'title' => '接口配置',
                        'uri' => 'app/manager/' . $appKey. '/user/config',
                    ],
                ],
            ],
            [
                'id' => 4,
                'parent_id' => 0,
                'title' => '订单管理',
                'icon' => 'fa-shopping-cart',
                'children' => $this->getOrderMenu($appKey),
            ],
            [
                'id' => 5,
                'parent_id' => 0,
                'title' => '文档管理',
                'icon' => 'fa-file-text',
                'children' => [
                    [
                        'id' => 51,
                        'parent_id' => 5,
                        'title' => '文档分类',
                        'uri' => 'app/manager/' . $appKey. '/article/category',
                    ],
                    [
                        'id' => 52,
                        'parent_id' => 5,
                        'title' => '文档列表',
                        'uri' => 'app/manager/' . $appKey. '/article/list',
                    ],
                    [
                        'id' => 53,
                        'parent_id' => 5,
                        'title' => '接口配置',
                        'uri' => 'app/manager/' . $appKey. '/article/config',
                    ],
                ],
            ],
            [
                'id' => 6,
                'parent_id' => 0,
                'title' => '用户互动',
                'icon' => 'fa-comments',
                'children' => [
                    [
                        'id' => 61,
                        'parent_id' => 6,
                        'title' => '反馈消息列表',
                        'uri' => 'app/manager/' . $appKey. '/feedback/list',
                    ],
                    [
                        'id' => 62,
                        'parent_id' => 6,
                        'title' => '通知下发',
                        'uri' => 'app/manager/' . $appKey. '/notice/list',
                    ],
                    [
                        'id' => 63,
                        'parent_id' => 6,
                        'title' => '接口配置',
                        'uri' => 'app/manager/' . $appKey. '/message/config',
                    ],
                    
                ],
            ],
            [
                'id' => 9,
                'parent_id' => 0,
                'title' => 'API接入工具',
                'icon' => 'fa-wrench',
                'children' => [
                    [
                        'id' => 91,
                        'parent_id' => 9,
                        'title' => '接口调试',
                        'uri' => 'app/manager/' . $appKey. '/api_tester',
                    ],
                ],
            ],
        ];
    }

    public function getOrderMenu($appKey)
    {
        $appInfo = app(App::class)->getAppInfo($appKey);
        if($appInfo['platform_type'] == App::PLATFORM_TYPE_IOS) {
            return [
                [
                    'id' => 41,
                    'parent_id' => 4,
                    'title' => '订单列表',
                    'uri' => 'app/manager/' . $appKey. '/order/apple/list',
                ],
                [
                    'id' => 42,
                    'parent_id' => 4,
                    'title' => '产品列表',
                    'uri' => 'app/manager/' . $appKey. '/order/apple/product',
                ],
                [
                    'id' => 43,
                    'parent_id' => 4,
                    'title' => '苹果回调通知',
                    'uri' => 'app/manager/' . $appKey. '/apple/notification',
                ],
                [
                    'id' => 45,
                    'parent_id' => 4,
                    'title' => '票据验证记录',
                    'uri' => 'app/manager/' . $appKey. '/apple/receipt/verification',
                ],
                [
                    'id' => 44,
                    'parent_id' => 4,
                    'title' => '接口配置',
                    'uri' => 'app/manager/' . $appKey. '/order/config',
                ],
            ];
        }else{
            return [
                [
                    'id' => 41,
                    'parent_id' => 4,
                    'title' => '订单列表',
                    'uri' => 'app/manager/' . $appKey. '/order/android/list',
                ],
                [
                    'id' => 42,
                    'parent_id' => 4,
                    'title' => '产品列表',
                    'uri' => 'app/manager/' . $appKey. '/order/android/product',
                ],
                [
                    'id' => 43,
                    'parent_id' => 4,
                    'title' => '接口配置',
                    'uri' => 'app/manager/' . $appKey. '/order/config',
                ],
            ];
        }
    }
}
