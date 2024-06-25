<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'role' => 1
        ]);

        User::factory()->create([
            'name' => 'Employee1',
            'email' => 'employee1@gmail.com',
            'role' => 2
        ]);

        User::factory()->count(10)->create();
    }
}
