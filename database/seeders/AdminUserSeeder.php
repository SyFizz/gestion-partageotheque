<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $admin = User::create([
            'name' => 'Administrateur',
            'email' => 'admin@partageoteque.org',
            'password' => Hash::make('password'),
            'is_validated' => true,
        ]);

        $adminRole = Role::where('slug', 'administrateur')->first();
        $admin->roles()->attach($adminRole->id);
    }
}
