<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory(30)->create();

        $adminRole = Role::find(1);
        $testRole = Role::find(2);

        $permissionIds = Permission::all()->pluck('id')->toArray();
        $testRole->permissions()->sync($permissionIds);

        $user = User::find(1);
        $user->email = 'admin@shiwuhao.com';
        $user->nickname = 'admin';
        $user->password = bcrypt('111111');
        $user->save();
        $user->roles()->sync([$adminRole->id, $testRole->id]);

        $user = User::find(2);
        $user->email = 'test@shiwuhao.com';
        $user->nickname = 'Test';
        $user->password = bcrypt('111111');
        $user->save();
        $user->roles()->sync([$testRole->id]);
    }
}
