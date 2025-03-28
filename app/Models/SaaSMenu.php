<?php

namespace App\Models;

/**
 * Class Menu.
 *
 * @property int $id
 *
 * @method where($parent_id, $id)
 */
class SaaSMenu
{
    /**
     * @return array
     */
    public function allNodes(): array
    {
        return [
            [
                'id' => 1,
                'parent_id' => 0,
                'title' => '应用列表',
                'icon' => 'fa-th-list',
                'uri' => '/',
                // 'children' => [
                //     [
                //         'id' => 4,
                //         'parent_id' => 1,
                //         'title' => 'Dashboard',
                //         'icon' => 'fa-dashboard',
                //         'uri' => '/dashboard',
                //     ],
                //     [
                //         'id' => 5,
                //         'parent_id' => 1,
                //         'title' => 'Dashboard',
                //         'icon' => 'fa-dashboard',
                //         'uri' => '/dashboard',
                //     ],
                //     [
                //         'id' => 6,
                //         'parent_id' => 1,
                //         'title' => 'Dashboard',
                //         'icon' => 'fa-dashboard',
                //         'uri' => '/dashboard',
                //     ],
                // ],
            ],
        ];
    }
}
