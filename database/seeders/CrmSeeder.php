<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Filiale;
use App\Models\Source;
use App\Models\Campagne;
use App\Models\Produit;
use App\Models\Prospect;
use App\Models\Client;
use App\Models\Vente;
use App\Models\Task;
use App\Models\Relance;
use App\Models\ActivityLog;
use App\Models\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class CrmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Filiales
        $filialesData = [
            [
                'nom' => 'Filiale France Nord (Paris)',
                'adresse' => '12 Rue de la Paix',
                'telephone' => '+33 1 45 67 89 01',
                'email' => 'paris@crm-commercial.com',
                'ville' => 'Paris',
                'pays' => 'France',
                'statut' => 'actif',
            ],
            [
                'nom' => 'Filiale France Sud (Marseille)',
                'adresse' => '45 Avenue du Prado',
                'telephone' => '+33 4 91 23 45 67',
                'email' => 'marseille@crm-commercial.com',
                'ville' => 'Marseille',
                'pays' => 'France',
                'statut' => 'actif',
            ],
            [
                'nom' => 'Filiale Belgique (Bruxelles)',
                'adresse' => '100 Rue de la Loi',
                'telephone' => '+32 2 500 12 34',
                'email' => 'bruxelles@crm-commercial.com',
                'ville' => 'Bruxelles',
                'pays' => 'Belgique',
                'statut' => 'actif',
            ],
            [
                'nom' => 'Filiale Maroc (Casablanca)',
                'adresse' => '88 Boulevard d\'Anfa',
                'telephone' => '+212 5 22 34 56 78',
                'email' => 'casablanca@crm-commercial.com',
                'ville' => 'Casablanca',
                'pays' => 'Maroc',
                'statut' => 'actif',
            ],
        ];

        $filiales = [];
        foreach ($filialesData as $data) {
            $filiales[] = Filiale::create($data);
        }

        // 2. Retrieve Users & Active Commercials (seeded by UserSeeder)
        $users = User::all();
        $commercials = User::whereHas('roles', function ($q) {
            $q->where('name', 'Commercial');
        })->where('is_active', true)->get();

        // 3. Create Sources
        $sourcesData = [
            ['nom' => 'Site Internet', 'description' => 'Formulaire de contact web', 'statut' => 'actif'],
            ['nom' => 'LinkedIn Ads', 'description' => 'Publicités LinkedIn', 'statut' => 'actif'],
            ['nom' => 'Recommandation', 'description' => 'Bouche à oreille & partenaires', 'statut' => 'actif'],
            ['nom' => 'Salon Professionnel', 'description' => 'Rencontres lors de salons', 'statut' => 'actif'],
            ['nom' => 'Cold Outreach', 'description' => 'Prospection téléphonique & emailing', 'statut' => 'actif'],
        ];

        $sources = [];
        foreach ($sourcesData as $data) {
            $sources[] = Source::create($data);
        }

        // 4. Create Produits
        $produitsData = [
            [
                'filiale_id' => $filiales[0]->id, // Paris
                'nom' => 'Formation JS Fullstack',
                'description' => 'Formation de 3 mois sur Node.js, React et Express.',
                'prix' => 2500.00,
                'type' => 'Formation',
                'statut' => 'actif',
            ],
            [
                'filiale_id' => $filiales[0]->id, // Paris
                'nom' => 'Formation Symfony Advanced',
                'description' => 'Formation de 5 jours sur Symfony, API Platform et Docker.',
                'prix' => 1800.00,
                'type' => 'Formation',
                'statut' => 'actif',
            ],
            [
                'filiale_id' => $filiales[1]->id, // Marseille
                'nom' => 'Consulting DevOps (journée)',
                'description' => 'Accompagnement CI/CD, Kubernetes et monitoring.',
                'prix' => 950.00,
                'type' => 'Prestation',
                'statut' => 'actif',
            ],
            [
                'filiale_id' => $filiales[2]->id, // Bruxelles
                'nom' => 'Licence CRM SaaS (Annuelle)',
                'description' => 'Abonnement annuel pour 10 utilisateurs.',
                'prix' => 1200.00,
                'type' => 'Produit',
                'statut' => 'actif',
            ],
            [
                'filiale_id' => $filiales[3]->id, // Casablanca
                'nom' => 'Formation Flutter & Dart',
                'description' => 'Développement mobile multiplateforme de 10 jours.',
                'prix' => 1500.00,
                'type' => 'Formation',
                'statut' => 'actif',
            ],
        ];

        $produits = [];
        foreach ($produitsData as $data) {
            $produits[] = Produit::create($data);
        }

        // 5. Create Campagnes
        $campagnesData = [
            [
                'filiale_id' => $filiales[0]->id, // Paris
                'nom' => 'Campagne Black Friday 2026',
                'description' => 'Offres spéciales sur les formations JS.',
                'budget' => 10000.00,
                'date_debut' => Carbon::parse('2026-11-20'),
                'date_fin' => Carbon::parse('2026-11-30'),
                'statut' => 'actif',
            ],
            [
                'filiale_id' => $filiales[2]->id, // Bruxelles
                'nom' => 'Salon Tech Bruxelles 2026',
                'description' => 'Présentation de notre outil CRM.',
                'budget' => 5000.00,
                'date_debut' => Carbon::parse('2026-06-15'),
                'date_fin' => Carbon::parse('2026-06-18'),
                'statut' => 'actif',
            ],
            [
                'filiale_id' => $filiales[3]->id, // Casablanca
                'nom' => 'Campagne Digital Maroc',
                'description' => 'Génération de leads via Google Ads.',
                'budget' => 3000.00,
                'date_debut' => Carbon::parse('2026-05-01'),
                'date_fin' => Carbon::parse('2026-05-31'),
                'statut' => 'actif',
            ],
        ];

        $campagnes = [];
        foreach ($campagnesData as $data) {
            $campagnes[] = Campagne::create($data);
        }

        // 6. Create Prospects
        $prospectsNames = [
            ['nom' => 'Dupont', 'prenom' => 'Jean', 'entreprise' => 'Renault', 'email' => 'jean.dupont@renault.fr', 'tel' => '0611223344', 'ville' => 'Paris', 'statut' => 'Nouveau'],
            ['nom' => 'Martin', 'prenom' => 'Alice', 'entreprise' => 'Decathlon', 'email' => 'alice.martin@decathlon.fr', 'tel' => '0622334455', 'ville' => 'Lille', 'statut' => 'Contacté'],
            ['nom' => 'Bernard', 'prenom' => 'Paul', 'entreprise' => 'Michelin', 'email' => 'paul.bernard@michelin.fr', 'tel' => '0633445566', 'ville' => 'Clermont-Ferrand', 'statut' => 'Qualifié'],
            ['nom' => 'Thomas', 'prenom' => 'Sophie', 'entreprise' => 'Suez', 'email' => 'sophie.thomas@suez.fr', 'tel' => '0644556677', 'ville' => 'Paris', 'statut' => 'En négociation'],
            ['nom' => 'Robert', 'prenom' => 'Julien', 'entreprise' => 'Bpost', 'email' => 'j.robert@bpost.be', 'tel' => '+32 475 12 34 56', 'ville' => 'Bruxelles', 'statut' => 'Gagné'],
            ['nom' => 'Richard', 'prenom' => 'Emilie', 'entreprise' => 'Solvay', 'email' => 'emilie.richard@solvay.com', 'tel' => '+32 486 98 76 54', 'ville' => 'Bruxelles', 'statut' => 'Gagné'],
            ['nom' => 'Petit', 'prenom' => 'Maxime', 'entreprise' => 'Attijariwafa Bank', 'email' => 'm.petit@attijari.ma', 'tel' => '+212 6 61 23 45 67', 'ville' => 'Casablanca', 'statut' => 'Gagné'],
            ['nom' => 'Durand', 'prenom' => 'Chantal', 'entreprise' => 'Royal Air Maroc', 'email' => 'c.durand@ram.ma', 'tel' => '+212 6 62 98 76 54', 'ville' => 'Casablanca', 'statut' => 'Gagné'],
            ['nom' => 'Leroy', 'prenom' => 'Marc', 'entreprise' => 'TotalEnergies', 'email' => 'marc.leroy@total.com', 'tel' => '0655667788', 'ville' => 'Marseille', 'statut' => 'Perdu'],
            ['nom' => 'Moreau', 'prenom' => 'Claire', 'entreprise' => 'Air France', 'email' => 'claire.moreau@airfrance.fr', 'tel' => '0666778899', 'ville' => 'Nice', 'statut' => 'Nouveau'],
            ['nom' => 'Simon', 'prenom' => 'Nicolas', 'entreprise' => 'Belgacom', 'email' => 'nicolas.simon@proximus.be', 'tel' => '+32 499 11 22 33', 'ville' => 'Anvers', 'statut' => 'Contacté'],
            ['nom' => 'Laurent', 'prenom' => 'Sarah', 'entreprise' => 'Maroc Telecom', 'email' => 's.laurent@iam.ma', 'tel' => '+212 6 63 11 22 33', 'ville' => 'Rabat', 'statut' => 'Qualifié'],
            ['nom' => 'Lefevre', 'prenom' => 'David', 'entreprise' => 'Carrefour', 'email' => 'david.lefevre@carrefour.fr', 'tel' => '0677889900', 'ville' => 'Paris', 'statut' => 'En négociation'],
            ['nom' => 'Michel', 'prenom' => 'Valérie', 'entreprise' => 'Orange', 'email' => 'valerie.michel@orange.fr', 'tel' => '0688990011', 'ville' => 'Lyon', 'statut' => 'Nouveau'],
            ['nom' => 'Garcia', 'prenom' => 'Antoine', 'entreprise' => 'L\'Oréal', 'email' => 'antoine.garcia@loreal.com', 'tel' => '0699001122', 'ville' => 'Paris', 'statut' => 'Contacté'],
        ];

        $prospects = [];
        foreach ($prospectsNames as $index => $pData) {
            $filiale = $filiales[$index % count($filiales)];
            $commercial = $commercials[$index % count($commercials)];
            $source = $sources[$index % count($sources)];
            $campagne = ($index % 3 === 0) ? $campagnes[$index % count($campagnes)] : null;

            $prospect = Prospect::create([
                'filiale_id' => $filiale->id,
                'commercial_id' => $commercial->id,
                'source_id' => $source->id,
                'campagne_id' => $campagne ? $campagne->id : null,
                'nom' => $pData['nom'],
                'prenom' => $pData['prenom'],
                'email' => $pData['email'],
                'telephone' => $pData['tel'],
                'entreprise' => $pData['entreprise'],
                'profession' => 'Responsable IT / Manager',
                'adresse' => '12 Route du Développement',
                'ville' => $pData['ville'],
                'statut' => $pData['statut'],
                'besoin' => 'Recherche une solution d\'accompagnement sur mesure pour ses équipes de développement.',
                'commentaire' => 'Lead qualifié d\'intérêt élevé.',
                'date_contact' => Carbon::now()->subDays($index * 2),
                'prochain_rappel' => ($pData['statut'] !== 'Gagné' && $pData['statut'] !== 'Perdu') ? Carbon::now()->addDays($index) : null,
            ]);

            // Add history entries
            $prospect->histories()->create([
                'user_id' => $users[0]->id, // Created by Admin
                'action' => 'Création',
                'description' => 'Saisie initiale du prospect dans le CRM.',
                'ancien_statut' => null,
                'nouveau_statut' => 'Nouveau',
                'created_at' => Carbon::now()->subDays($index * 2 + 1),
            ]);

            if ($pData['statut'] !== 'Nouveau') {
                $prospect->histories()->create([
                    'user_id' => $commercial->id,
                    'action' => 'Changement statut',
                    'description' => "Avancement du prospect à l'état {$pData['statut']}.",
                    'ancien_statut' => 'Nouveau',
                    'nouveau_statut' => $pData['statut'],
                    'created_at' => Carbon::now()->subDays($index * 2),
                ]);
            }

            $prospects[] = $prospect;

            // Generate an ActivityLog for prospect creation
            ActivityLog::create([
                'user_id' => $commercial->id,
                'action' => 'Création prospect',
                'module' => 'Prospects',
                'description' => "Création du prospect {$prospect->nom} {$prospect->prenom} ({$prospect->entreprise}).",
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'created_at' => Carbon::now()->subDays($index * 2 + 1),
            ]);
        }

        // 7. Create Clients (for Gagné prospects)
        $clients = [];
        foreach ($prospects as $prospect) {
            if ($prospect->statut === 'Gagné') {
                $client = Client::create([
                    'prospect_id' => $prospect->id,
                    'commercial_id' => $prospect->commercial_id,
                    'filiale_id' => $prospect->filiale_id,
                    'nom' => $prospect->nom,
                    'prenom' => $prospect->prenom,
                    'email' => $prospect->email,
                    'telephone' => $prospect->telephone,
                    'adresse' => $prospect->adresse,
                    'ville' => $prospect->ville,
                    'entreprise' => $prospect->entreprise,
                    'statut' => 'Actif',
                    'date_conversion' => Carbon::now()->subDays(2),
                ]);

                // Create Prospect History conversion log
                $prospect->histories()->create([
                    'user_id' => $prospect->commercial_id,
                    'action' => 'Conversion client',
                    'description' => "Prospect converti avec succès en client. Fiche Client ID : {$client->id}.",
                    'ancien_statut' => 'En négociation',
                    'nouveau_statut' => 'Gagné',
                    'created_at' => Carbon::now()->subDays(2),
                ]);

                // ActivityLog
                ActivityLog::create([
                    'user_id' => $prospect->commercial_id,
                    'action' => 'Conversion client',
                    'module' => 'Clients',
                    'description' => "Prospect {$prospect->nom} {$prospect->prenom} converti en client.",
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                    'created_at' => Carbon::now()->subDays(2),
                ]);

                $clients[] = $client;
            }
        }

        // 8. Create Ventes
        $ventesData = [
            [
                'client_index' => 0, // Robert Julien (Bruxelles client)
                'produit_index' => 3, // Licence CRM SaaS (Bruxelles product)
                'quantite' => 15,
                'reduction' => 2000.00,
                'statut' => 'Validée',
            ],
            [
                'client_index' => 1, // Richard Emilie (Bruxelles client)
                'produit_index' => 3, // Licence CRM SaaS
                'quantite' => 5,
                'reduction' => 0.00,
                'statut' => 'En attente',
            ],
            [
                'client_index' => 2, // Petit Maxime (Casablanca client)
                'produit_index' => 4, // Formation Flutter & Dart (Casablanca product)
                'quantite' => 8,
                'reduction' => 1000.00,
                'statut' => 'Validée',
            ],
            [
                'client_index' => 3, // Durand Chantal (Casablanca client)
                'produit_index' => 4, // Formation Flutter & Dart
                'quantite' => 2,
                'reduction' => 100.00,
                'statut' => 'Annulée',
            ],
        ];

        // Let's add two more client & sale manually on France Nord (Paris)
        $parisClient = Client::create([
            'commercial_id' => $commercials[0]->id,
            'filiale_id' => $filiales[0]->id, // Paris
            'nom' => 'Gérard',
            'prenom' => 'Paul',
            'email' => 'paul.gerard@societe.fr',
            'telephone' => '0623344556',
            'entreprise' => 'Société Générale',
            'statut' => 'Actif',
            'date_conversion' => Carbon::now()->subDays(5),
        ]);
        $clients[] = $parisClient;

        $venteParis1 = Vente::create([
            'client_id' => $parisClient->id,
            'produit_id' => $produits[0]->id, // Formation JS Fullstack (2500xof)
            'commercial_id' => $commercials[0]->id,
            'filiale_id' => $filiales[0]->id,
            'quantite' => 4, // Total 10000xof
            'reduction' => 500.00, // Final 9500xof
            'montant' => 9500.00,
            'statut' => 'Validée',
            'date_vente' => Carbon::now()->subDays(4),
        ]);

        $venteParis2 = Vente::create([
            'client_id' => $parisClient->id,
            'produit_id' => $produits[1]->id, // Formation Symfony Advanced (1800xof)
            'commercial_id' => $commercials[0]->id,
            'filiale_id' => $filiales[0]->id,
            'quantite' => 1,
            'reduction' => 0.00,
            'montant' => 1800.00,
            'statut' => 'En attente',
            'date_vente' => Carbon::now()->subDays(1),
        ]);

        foreach ($ventesData as $vData) {
            $client = $clients[$vData['client_index']];
            $produit = $produits[$vData['produit_index']];
            $commercial = $client->commercial;
            $filiale = $client->filiale;

            $montant = ($produit->prix * $vData['quantite']) - $vData['reduction'];

            Vente::create([
                'client_id' => $client->id,
                'produit_id' => $produit->id,
                'commercial_id' => $commercial->id,
                'filiale_id' => $filiale->id,
                'quantite' => $vData['quantite'],
                'reduction' => $vData['reduction'],
                'montant' => max(0, $montant),
                'statut' => $vData['statut'],
                'date_vente' => Carbon::now()->subDays(3),
            ]);

            ActivityLog::create([
                'user_id' => $commercial->id,
                'action' => 'Création vente',
                'module' => 'Ventes',
                'description' => "Création de la vente d'un montant de {$montant}xof pour {$client->entreprise}.",
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'created_at' => Carbon::now()->subDays(3),
            ]);
        }

        // 9. Create Tasks
        $tasksData = [
            ['titre' => 'Rappeler Jean pour offre', 'priorite' => 'Haute', 'statut' => 'À faire', 'prospect_index' => 0],
            ['titre' => 'Envoyer brochure commerciale', 'priorite' => 'Moyenne', 'statut' => 'En cours', 'prospect_index' => 1],
            ['titre' => 'Qualifier le besoin technique', 'priorite' => 'Urgente', 'statut' => 'À faire', 'prospect_index' => 2],
            ['titre' => 'Préparer démonstration produit', 'priorite' => 'Haute', 'statut' => 'Terminé', 'prospect_index' => 3],
            ['titre' => 'Régler contrat de licence', 'priorite' => 'Urgente', 'statut' => 'À faire', 'prospect_index' => 12],
        ];

        foreach ($tasksData as $tData) {
            $prospect = $prospects[$tData['prospect_index']];
            $commercial = $prospect->commercial;

            Task::create([
                'user_id' => $commercial->id,
                'prospect_id' => $prospect->id,
                'titre' => $tData['titre'],
                'description' => 'Cette tâche a été générée automatiquement lors du seeding pour simulation.',
                'priorite' => $tData['priorite'],
                'statut' => $tData['statut'],
                'date_limite' => Carbon::now()->addDays(5),
            ]);
        }

        // 10. Create Relances
        $relancesData = [
            ['canal' => 'Appel', 'offset_days' => 0, 'prospect_index' => 0],
            ['canal' => 'Email', 'offset_days' => 2, 'prospect_index' => 1],
            ['canal' => 'WhatsApp', 'offset_days' => 0, 'prospect_index' => 2],
            ['canal' => 'Rendez-vous', 'offset_days' => 4, 'prospect_index' => 3],
            ['canal' => 'SMS', 'offset_days' => -3, 'prospect_index' => 13],
        ];

        foreach ($relancesData as $rData) {
            $prospect = $prospects[$rData['prospect_index']];
            $commercial = $prospect->commercial;

            Relance::create([
                'prospect_id' => $prospect->id,
                'commercial_id' => $commercial->id,
                'canal' => $rData['canal'],
                'date_relance' => Carbon::now()->addDays($rData['offset_days']),
                'statut' => $rData['offset_days'] < 0 ? 'Réalisée' : 'En attente',
                'commentaire' => 'Relance commerciale automatisée.',
            ]);
        }

        // 11. Create Notifications
        foreach ($commercials as $comm) {
            Notification::create([
                'user_id' => $comm->id,
                'titre' => 'Nouvelle tâche assignée',
                'message' => 'Une tâche commerciale prioritaire vous a été assignée par le responsable.',
                'type' => 'info',
                'is_read' => false,
            ]);

            Notification::create([
                'user_id' => $comm->id,
                'titre' => 'Rappel de relance aujourd\'hui',
                'message' => 'Vous avez une ou plusieurs relances planifiées pour aujourd\'hui.',
                'type' => 'warning',
                'is_read' => false,
            ]);
        }
    }
}
