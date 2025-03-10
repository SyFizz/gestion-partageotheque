<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run()
    {
        // Création du rôle Adhérent
        $memberRole = Role::create([
            'name' => 'Adhérent',
            'slug' => 'adherent',
            'description' => 'Membre de l\'association pouvant emprunter du matériel',
            'requires_validation' => true,
        ]);

        // Création du rôle Bénévole
        $volunteerRole = Role::create([
            'name' => 'Bénévole',
            'slug' => 'benevole',
            'description' => 'Membre actif gérant les emprunts/retours au quotidien',
            'requires_validation' => false,
        ]);

        // Création du rôle Administrateur
        $adminRole = Role::create([
            'name' => 'Administrateur',
            'slug' => 'administrateur',
            'description' => 'Accès complet pour la gestion globale',
            'requires_validation' => false,
        ]);

        // Attribution des permissions
        $memberPermissions = [
            'view-catalog',
            'view-item-details',
            'reserve-item',
        ];

        $volunteerPermissions = [
            'view-catalog',
            'view-item-details',
            'view-item-history',
            'edit-item',
            'create-item',
            'create-loan',
            'return-loan',
            'extend-loan',
            'create-reservation',
            'edit-reservation',
            'reorganize-queue',
            'delete-reservation',
            'send-notification',
            'view-own-activity-logs',
            'create-user',
            'view-archived-items',
        ];

        // L'administrateur a toutes les permissions
        $adminPermissions = Permission::pluck('slug')->toArray();

        // Attribution des permissions aux rôles
        $memberRole->permissions()->attach(Permission::whereIn('slug', $memberPermissions)->pluck('id'));
        $volunteerRole->permissions()->attach(Permission::whereIn('slug', $volunteerPermissions)->pluck('id'));
        $adminRole->permissions()->attach(Permission::pluck('id'));
    }
}
