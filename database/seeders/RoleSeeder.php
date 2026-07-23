<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Role\Models\Role; // confirm this path first

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Super Admin', 'slug' => 'super-admin', 'description' => 'Full system access'],
            ['name' => 'Admin', 'slug' => 'admin', 'description' => 'Admin access'],
            ['name' => 'Branch Manager', 'slug' => 'branch-manager', 'description' => 'Manage branch operations'],
            ['name' => 'Staff', 'slug' => 'staff', 'description' => 'Staff user'],
            ['name' => 'Customer', 'slug' => 'customer', 'description' => 'Vehicle rental customer'],
            ['name' => 'Driver', 'slug' => 'driver', 'description' => 'Vehicle rental driver'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['slug' => $role['slug']], $role);
        }
    }
}