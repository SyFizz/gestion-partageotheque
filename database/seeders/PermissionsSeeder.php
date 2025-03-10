<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            ['name' => 'Réserver un objet pour soi-même', 'slug' => 'reserve-item'],
            ['name' => 'Consulter le catalogue des objets', 'slug' => 'view-catalog'],
            ['name' => 'Voir les détails d\'un objet', 'slug' => 'view-item-details'],
            ['name' => 'Voir les détails et l\'historique d\'un objet', 'slug' => 'view-item-history'],
            ['name' => 'Modifier un objet existant', 'slug' => 'edit-item'],
            ['name' => 'Créer un nouvel objet', 'slug' => 'create-item'],
            ['name' => 'Supprimer un objet existant', 'slug' => 'delete-item'],
            ['name' => 'Déclarer un emprunt', 'slug' => 'create-loan'],
            ['name' => 'Déclarer un retour', 'slug' => 'return-loan'],
            ['name' => 'Modifier les informations d\'un emprunt existant', 'slug' => 'edit-loan'],
            ['name' => 'Supprimer un emprunt ou un retour de l\'historique', 'slug' => 'delete-loan'],
            ['name' => 'Prolonger un emprunt', 'slug' => 'extend-loan'],
            ['name' => 'Déclarer une réservation', 'slug' => 'create-reservation'],
            ['name' => 'Modifier une réservation existante', 'slug' => 'edit-reservation'],
            ['name' => 'Réorganiser la file d\'attente pour un objet', 'slug' => 'reorganize-queue'],
            ['name' => 'Supprimer une réservation', 'slug' => 'delete-reservation'],
            ['name' => 'Envoyer manuellement une notification à un utilisateur', 'slug' => 'send-notification'],
            ['name' => 'Enregistrer un paiement', 'slug' => 'create-payment'],
            ['name' => 'Modifier un paiement existant', 'slug' => 'edit-payment'],
            ['name' => 'Supprimer un paiement de l\'historique', 'slug' => 'delete-payment'],
            ['name' => 'Modifier les paramètres de l\'application', 'slug' => 'edit-settings'],
            ['name' => 'Consulter le journal d\'activité de tous', 'slug' => 'view-all-activity-logs'],
            ['name' => 'Consulter son propre journal d\'activité', 'slug' => 'view-own-activity-logs'],
            ['name' => 'Créer un compte', 'slug' => 'create-user'],
            ['name' => 'Modifier les informations d\'un compte', 'slug' => 'edit-user'],
            ['name' => 'Supprimer un compte', 'slug' => 'delete-user'],
            ['name' => 'Réinitialiser le mot de passe d\'un compte', 'slug' => 'reset-user-password'],
            ['name' => 'Assigner un ou plusieurs rôles à un compte', 'slug' => 'assign-roles'],
            ['name' => 'Créer des rôles', 'slug' => 'create-role'],
            ['name' => 'Modifier les permissions d\'un rôle', 'slug' => 'edit-role-permissions'],
            ['name' => 'Supprimer un rôle', 'slug' => 'delete-role'],
            ['name' => 'Supprimer un rôle et choisir un rôle de remplacement', 'slug' => 'delete-role-with-replacement'],
            ['name' => 'Voir les objets archivés', 'slug'=>'view-archived-items']
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}
