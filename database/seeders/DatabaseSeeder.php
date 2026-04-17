<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Role
        $superadminRole = Role::create(['name' => 'superadmin']);
        $userRole = Role::create(['name' => 'user']); // Untuk pemilik kendaraan nanti

        // 2. Buat Akun Superadmin
        $admin = User::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('admin12345'),
        ]);

        // 3. Assign Role ke User
        $admin->assignRole($superadminRole);
    }
}
