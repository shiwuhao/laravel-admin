<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['id' => 5, 'pid' => 0, 'type' => 'route', 'label' => '系统', 'name' => 'SystemMange', 'icon' => 'el-windows', 'path' => '/system'],
            ['id' => 6, 'pid' => 5, 'type' => 'route', 'label' => '用户管理', 'name' => 'UserManage', 'icon' => 'el-icon-user-solid', 'path' => '/system/users'],
            ['id' => 7, 'pid' => 5, 'type' => 'route', 'label' => '角色管理', 'name' => 'RoleManage', 'icon' => 'fa fa-sitemap', 'path' => '/system/roles'],
            ['id' => 8, 'pid' => 5, 'type' => 'route', 'label' => '菜单管理', 'name' => 'MenuManage', 'icon' => 'fa fa-linode', 'path' => '/system/menus'],
            ['id' => 9, 'pid' => 5, 'type' => 'route', 'label' => '动作管理', 'name' => 'ActionManage', 'icon' => 'fa fa-linode', 'path' => '/system/actions'],
            ['id' => 10, 'pid' => 5, 'type' => 'route', 'label' => '权限节点', 'name' => 'PermissionManage', 'icon' => 'fa fa-linode', 'path' => '/system/permissions'],
            ['id' => 11, 'pid' => 5, 'type' => 'route', 'label' => '配置管理', 'name' => 'ConfigManage', 'icon' => 'el-icon-setting', 'path' => '/system/configs'],
        ];

        foreach ($data as $item) {
            $menu = new Menu($item);
            $menu->save();
        }


    }
}
