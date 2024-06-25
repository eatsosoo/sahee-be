<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = Permission::PERMISSION_LIST;

        $admins = [];
        foreach ($permissions as $permission) { 
            $admins[] = ['role_id' => Role::ADMIN, 'permission_id' => $permission['id']];
        }

        foreach ($admins as $admin) {
            RolePermission::create($admin);
        }

        $employee = [];
        $excludes = ['product_delete', 'category_delete', 'order_cancel', 'comment_delete', 'comment_update', 'comment_create'];
        foreach ($permissions as $permission) {
            Log::debug($permission['name']);
            if (!in_array($permission['name'], $excludes)) {
                $employee[] = ['role_id' => Role::EMPLOYEE, 'permission_id' => $permission['id']];
            }
        }
        foreach ($employee as $employee) {
            RolePermission::create($employee);
        }

        $user = [];
        $userExcludes = ['product_delete', 'category_delete', 'order_delete', 'comment_delete'];
        foreach ($permissions as $permission) {
            if (!in_array($permission['name'], $userExcludes)) {
                $user[] = ['role_id' => Role::USER, 'permission_id' => $permission['id']];
            }
        }
        foreach ($user as $user) {
            RolePermission::create($user);
        }
    }
}
