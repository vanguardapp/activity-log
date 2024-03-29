<?php

namespace Vanguard\UserActivity\Database\Seeders;

use Illuminate\Database\Seeder;
use Vanguard\Permission;
use Vanguard\Role;

class ActivityPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminRole = Role::where('name', 'Admin')->first();

        $permission = Permission::create([
            'name' => 'users.activity',
            'display_name' => 'View System Activity Log',
            'description' => 'View activity log for all system users.',
            'removable' => false,
        ]);

        $adminRole->attachPermission($permission);
    }
}
