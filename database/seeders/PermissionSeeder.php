<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $modules = [
            ['model' => 'Roles', 'permissions' => ['View', 'Create', 'Edit', 'Delete', 'Restore', 'Export']],
            ['model' => 'Users', 'permissions' => ['View', 'Create', 'Edit', 'Delete', 'Restore', 'Export', 'Password']],
            ['model' => 'Action Logs', 'permissions' => ['View', 'Export']],
            ['model' => 'Settings', 'permissions' => ['Edit']],
        ];
        
        foreach ($modules as $module) {
            foreach ($module['permissions'] as $permission) {
                $permissionName = $permission . ' ' . $module['model'];

                if (!Permission::where('name', $permissionName)->exists()) {
                    Permission::create([
                        'name' => $permissionName,
                        'guard_name' => 'web',
                    ]);
                }
            }
        }
        $role_name = 'Admin Developing-Team';
        $role = Role::firstOrCreate(['name' => $role_name, 'guard_name' => 'web']);
        $permissions = Permission::all();
        $role->syncPermissions($permissions);

        $super_admin_role_id = Role::where('name', $role_name)->first()->id;
        $super_admin_role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $super_admin_role->syncPermissions($permissions);
        $super_admin_role_id_new = $super_admin_role->id;
        $users = [
            [
                "name" => 'Admin',
                "email" => 'admin@gmail.com',
                'mobile' => '8888888888',
                "user_type" => 'admin',
                "password" => Hash::make('admin123'),
                "role_id" => $super_admin_role_id,
                "is_show"=> 0,
            ],
            [
                "name" => 'Super Admin',
                "email" => 'superadmin@gmail.com',
                'mobile' => '7777777777',
                "user_type" => 'admin',
                "password" => Hash::make('superadmin123'),
                "role_id" => $super_admin_role_id_new,
            ]
        ];

        foreach ($users as $data) {
            $user = User::updateOrCreate(['mobile' => $data['mobile']], $data);
        
            if (($data['user_type'] ?? null) === 'admin') {
                $user->assignRole($data['role_id']);
            }
        }
    }
}
