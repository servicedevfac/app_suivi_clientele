<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usersData = [
            [
                'nom' => 'Admin',
                'prenom' => 'CRM',
                'email' => 'admin@crm.com',
                'telephone' => '0601020304',
                'password' => Hash::make('password'),
                'is_active' => true,
                'role' => 'Administrateur',
            ],
            [
                'nom' => 'Dupont',
                'prenom' => 'Pierre',
                'email' => 'directeur@crm.com',
                'telephone' => '0602030405',
                'password' => Hash::make('password'),
                'is_active' => true,
                'role' => 'Directeur Général',
            ],
           
        ];

        foreach ($usersData as $data) {
            $roleName = $data['role'];
            unset($data['role']);

            $user = User::updateOrCreate(
                ['email' => $data['email']],
                $data
            );

            // Sync/assign the role to the user
            $user->syncRoles([$roleName]);
        }
    }
}
