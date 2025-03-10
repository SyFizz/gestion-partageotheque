<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            PermissionsSeeder::class,
            RolesSeeder::class,
            ItemStatusSeeder::class,
            AdminUserSeeder::class,
            CategoriesSeeder::class,
        ]);
    }
}
