<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = Permission::PERMISSION_LIST;

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}
