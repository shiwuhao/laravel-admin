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
            ['id' => 1, 'pid' => 0, 'type' => Menu::TYPE_DIR, 'label' => '仪表盘', 'name' => 'Dashboard', 'icon' => 'ep:data-analysis', 'path' => '/dashboard', 'component' => 'LAYOUT'],
            ['id' => 2, 'pid' => 1, 'type' => Menu::TYPE_MENU, 'label' => '分析页', 'name' => 'Analysis', 'icon' => 'ep:aim', 'path' => '/dashboard/analysis', 'component' => '/views/dashboard/analysis/index.vue'],
            ['id' => 3, 'pid' => 1, 'type' => Menu::TYPE_MENU, 'label' => '工作台', 'name' => 'Workplace', 'icon' => 'ep:watch', 'path' => '/dashboard/workplace', 'component' => '/views/dashboard/workplace/index.vue'],
            ['id' => 4, 'pid' => 1, 'type' => Menu::TYPE_MENU, 'label' => '监控页', 'name' => 'Monitor', 'icon' => 'ep:watch', 'path' => '/dashboard/monitor', 'component' => '/views/dashboard/monitor/index.vue'],

            ['id' => 5, 'pid' => 0, 'type' => Menu::TYPE_DIR, 'label' => '系统', 'name' => 'SystemMange', 'icon' => 'ep:menu', 'path' => '/system', 'component' => 'LAYOUT'],
            ['id' => 6, 'pid' => 5, 'type' => Menu::TYPE_MENU, 'label' => '用户管理', 'name' => 'UserManage', 'icon' => 'ep:user-filled', 'path' => '/system/users', 'component' => '/views/system/users/index.vue'],
            ['id' => 7, 'pid' => 5, 'type' => Menu::TYPE_MENU, 'label' => '角色管理', 'name' => 'RoleManage', 'icon' => 'fa:sitemap', 'path' => '/system/roles', 'component' => '/views/system/roles/index.vue'],
            ['id' => 8, 'pid' => 5, 'type' => Menu::TYPE_MENU, 'label' => '菜单管理', 'name' => 'MenuManage', 'icon' => 'fa:linode', 'path' => '/system/menus', 'component' => '/views/system/menus/index.vue'],
            ['id' => 9, 'pid' => 5, 'type' => Menu::TYPE_MENU, 'label' => '动作管理', 'name' => 'ActionManage', 'icon' => 'fa:linode', 'path' => '/system/actions', 'component' => '/views/system/actions/index.vue'],
            ['id' => 10, 'pid' => 5, 'type' => Menu::TYPE_MENU, 'label' => '权限节点', 'name' => 'PermissionManage', 'icon' => 'fa:linode', 'path' => '/system/permissions', 'component' => '/views/system/permissions/index.vue'],
            ['id' => 11, 'pid' => 5, 'type' => Menu::TYPE_MENU, 'label' => '配置管理', 'name' => 'ConfigManage', 'icon' => 'ep:setting', 'path' => '/system/configs', 'component' => '/views/system/configs/index.vue'],
        ];

        foreach ($data as $item) {
            $menu = new Menu($item);
            $menu->save();
        }

    }
}
