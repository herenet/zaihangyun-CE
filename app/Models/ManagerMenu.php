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
                        'uri' => 'app/manager/' . $appKey. '/user',
                    ],
                    [
                        'id' => 22,
                        'parent_id' => 2,
                        'title' => '登录接口配置',
                        'uri' => 'app/manager/' . $appKey. '/user/login/config',
                    ],
                ],
            ],
            [
                'id' => 3,
                'parent_id' => 0,
                'title' => '收款管理',
                'icon' => 'fa-shopping-cart',
                'children' => [
                    [
                        'id' => 31,
                        'parent_id' => 3,
                        'title' => '订单列表',
                        'uri' => 'app/manager/' . $appKey. '/payment',
                    ],
                    [
                        'id' => 33,
                        'parent_id' => 3,
                        'title' => '订单接口配置',
                        'uri' => 'app/manager/' . $appKey. '/payment/config',
                    ],
                ],
            ],
            [
                'id' => 4,
                'parent_id' => 0,
                'title' => '第三方接口配置',
                'icon' => 'fa-cogs',
                'children' => [
                    [
                        'id' => 41,
                        'parent_id' => 4,
                        'title' => '微信开放平台配置',
                        'uri' => 'app/manager/' . $appKey. '/config/wechat',
                    ],
                    [
                        'id' => 42,
                        'parent_id' => 4,
                        'title' => '微信商户号配置',
                        'uri' => 'app/manager/' . $appKey. '/config/wechat/payment',
                    ],
                    [
                        'id' => 43,
                        'parent_id' => 4,
                        'title' => '支付宝配置',
                        'uri' => 'app/manager/' . $appKey. '/config/alipay',
                    ],
                    [
                        'id' => 44,
                        'parent_id' => 4,
                        'title' => '阿里云短信配置',
                        'uri' => 'app/manager/' . $appKey. '/config/aliyun/sms',
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
