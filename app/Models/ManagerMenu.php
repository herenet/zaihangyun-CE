<?php

namespace App\Models;

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
                'title' => '用户管理',
                'icon' => 'fa-user',
                'children' => [
                    [
                        'id' => 21,
                        'parent_id' => 2,
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
                'id' => 3,
                'parent_id' => 0,
                'title' => '订单管理',
                'icon' => 'fa-shopping-cart',
                'children' => [
                    [
                        'id' => 31,
                        'parent_id' => 3,
                        'title' => '订单列表',
                        'uri' => 'app/manager/' . $appKey. '/order/list',
                    ],
                    [
                        'id' => 32,
                        'parent_id' => 3,
                        'title' => '产品列表',
                        'uri' => 'app/manager/' . $appKey. '/order/product',
                    ],
                    [
                        'id' => 33,
                        'parent_id' => 3,
                        'title' => '接口配置',
                        'uri' => 'app/manager/' . $appKey. '/order/config',
                    ],
                ],
            ],
            [
                'id' => 5,
                'parent_id' => 0,
                'title' => 'API接入工具',
                'icon' => 'fa-wrench',
                'children' => [
                    [
                        'id' => 51,
                        'parent_id' => 5,
                        'title' => '接口调试',
                        'uri' => 'app/manager/' . $appKey. '/tool/api/test',
                    ],
                    [
                        'id' => 52,
                        'parent_id' => 5,
                        'title' => 'API接入文档',
                        'uri' => 'app/manager/' . $appKey. '/tool/api/doc',
                    ],
                ],
            ],
        ];
    }
}
