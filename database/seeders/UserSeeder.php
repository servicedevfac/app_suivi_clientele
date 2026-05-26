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
            [
                'nom' => 'Martin',
                'prenom' => 'Sophie',
                'email' => 'responsable@crm.com',
                'telephone' => '0603040506',
                'password' => Hash::make('password'),
                'is_active' => true,
                'role' => 'Responsable Commercial',
            ],
            [
                'nom' => 'Dubois',
                'prenom' => 'Thomas',
                'email' => 'thomas.dubois@crm.com',
                'telephone' => '0604050607',
                'password' => Hash::make('password'),
                'is_active' => true,
                'role' => 'Commercial',
            ],
            [
                'nom' => 'Lefevre',
                'prenom' => 'Alice',
                'email' => 'alice.lefevre@crm.com',
                'telephone' => '0605060708',
                'password' => Hash::make('password'),
                'is_active' => true,
                'role' => 'Commercial',
            ],
            [
                'nom' => 'Moreau',
                'prenom' => 'Lucas',
                'email' => 'lucas.moreau@crm.com',
                'telephone' => '0606070809',
                'password' => Hash::make('password'),
                'is_active' => true,
                'role' => 'Commercial',
            ],
            [
                'nom' => 'Petit',
                'prenom' => 'Julie',
                'email' => 'julie.petit@crm.com',
                'telephone' => '0607080910',
                'password' => Hash::make('password'),
                'is_active' => false,
                'role' => 'Commercial',
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
