<?php

namespace Database\Seeders;

use App\Models\ItemStatus;
use Illuminate\Database\Seeder;

class ItemStatusSeeder extends Seeder
{
    public function run()
    {
        $statuses = [
            ['name' => 'En stock', 'slug' => 'in-stock'],
            ['name' => 'Prêté', 'slug' => 'on-loan'],
            ['name' => 'Réservé', 'slug' => 'reserved'],
            ['name' => 'En réparation', 'slug' => 'in-repair'],
            ['name' => 'Indisponible temporairement', 'slug' => 'temporarily-unavailable'],
        ];

        foreach ($statuses as $status) {
            ItemStatus::create($status);
        }
    }
}
