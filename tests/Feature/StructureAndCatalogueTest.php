<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Filiale;
use App\Models\Source;
use App\Models\Campagne;
use App\Models\Produit;
use App\Models\Prospect;
use App\Models\Client;
use App\Models\Vente;
use App\Models\ActivityLog;
use App\Models\ProspectHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StructureAndCatalogueTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $commercial;
    protected Filiale $filiale;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup Roles
        Role::create(['name' => 'Administrateur']);
        Role::create(['name' => 'Commercial']);

        // Create initial Users
        $this->admin = User::factory()->create(['is_active' => true]);
        $this->admin->assignRole('Administrateur');

        $this->commercial = User::factory()->create(['is_active' => true]);
        $this->commercial->assignRole('Commercial');

        // Create a Filiale
        $this->filiale = Filiale::create([
            'nom' => 'Filiale Paris',
            'adresse' => '123 Rue de Rivoli',
            'telephone' => '0102030405',
            'email' => 'paris@filiale.com',
            'ville' => 'Paris',
            'pays' => 'France',
            'statut' => 'actif',
        ]);
    }

    /* =========================================================================
       USER CRUD & CONFIGURATION TESTS
       ========================================================================= */

    public function test_admin_can_crud_users_with_roles_password_hashing_and_active_flag(): void
    {
        // 1. Create User
        $userData = [
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'email' => 'jean.dupont@example.com',
            'telephone' => '0607080910',
            'password' => 'secret12345',
            'password_confirmation' => 'secret12345',
            'is_active' => '1',
            'roles' => ['Commercial'],
        ];

        $response = $this->actingAs($this->admin)->post(route('users.store'), $userData);

        $response->assertRedirect(route('users.index'));
        
        $user = User::where('email', 'jean.dupont@example.com')->firstOrFail();
        $this->assertEquals('Dupont', $user->nom);
        $this->assertEquals('Jean', $user->prenom);
        $this->assertTrue($user->is_active);
        $this->assertTrue(Hash::check('secret12345', $user->password));
        $this->assertTrue($user->hasRole('Commercial'));

        // Check Activity Log for creation and role assignment
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->admin->id,
            'action' => 'Création utilisateur',
            'module' => 'Utilisateurs',
        ]);

        // 2. Update User (Change role, toggle is_active, keep same password by leaving it empty)
        $updateData = [
            'nom' => 'Dupont Modifie',
            'prenom' => 'Jean',
            'email' => 'jean.dupont@example.com',
            'telephone' => '0607080910',
            // password is null/empty, should keep the old hashed password
            'roles' => ['Administrateur'],
        ];

        $response = $this->actingAs($this->admin)->put(route('users.update', $user), $updateData);

        $response->assertRedirect(route('users.index'));
        
        $user->refresh();
        $this->assertEquals('Dupont Modifie', $user->nom);
        $this->assertFalse($user->is_active); // Checked is_active is toggled off since it was absent in request
        $this->assertTrue(Hash::check('secret12345', $user->password)); // Password unchanged
        $this->assertTrue($user->hasRole('Administrateur'));
        $this->assertFalse($user->hasRole('Commercial'));

        // Check Activity Log for modification and role changes
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->admin->id,
            'action' => 'Changement de rôle',
            'module' => 'Utilisateurs',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->admin->id,
            'action' => 'Modification utilisateur',
            'module' => 'Utilisateurs',
        ]);

        // 3. Delete User
        $response = $this->actingAs($this->admin)->delete(route('users.destroy', $user));
        
        $response->assertRedirect(route('users.index'));
        $this->assertSoftDeleted($user);

        // Check Activity Log for deletion
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->admin->id,
            'action' => 'Suppression utilisateur',
            'module' => 'Utilisateurs',
        ]);
    }

    /* =========================================================================
       FILIALE CRUD TESTS
       ========================================================================= */

    public function test_admin_can_crud_filiales(): void
    {
        // 1. Create Filiale
        $filialeData = [
            'nom' => 'Filiale Lyon',
            'adresse' => '456 Rue de la République',
            'telephone' => '0405060708',
            'email' => 'lyon@filiale.com',
            'ville' => 'Lyon',
            'pays' => 'France',
            'statut' => 'actif',
        ];

        $response = $this->actingAs($this->admin)->post(route('filiales.store'), $filialeData);

        $response->assertRedirect(route('filiales.index'));
        $this->assertDatabaseHas('filiales', [
            'nom' => 'Filiale Lyon',
            'ville' => 'Lyon',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->admin->id,
            'action' => 'Création filiale',
            'module' => 'Configuration',
        ]);

        $filiale = Filiale::where('nom', 'Filiale Lyon')->firstOrFail();

        // 2. Update Filiale
        $updateData = [
            'nom' => 'Filiale Lyon Modifiée',
            'statut' => 'inactif',
        ];

        $response = $this->actingAs($this->admin)->put(route('filiales.update', $filiale), $updateData);

        $response->assertRedirect(route('filiales.index'));
        $this->assertDatabaseHas('filiales', [
            'id' => $filiale->id,
            'nom' => 'Filiale Lyon Modifiée',
            'statut' => 'inactif',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->admin->id,
            'action' => 'Modification filiale',
            'module' => 'Configuration',
        ]);

        // 3. Delete Filiale
        $response = $this->actingAs($this->admin)->delete(route('filiales.destroy', $filiale));

        $response->assertRedirect(route('filiales.index'));
        $this->assertSoftDeleted($filiale);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->admin->id,
            'action' => 'Suppression filiale',
            'module' => 'Configuration',
        ]);
    }

    /* =========================================================================
       CATALOGUE CRUD TESTS (SOURCES, CAMPAGNES, PRODUITS)
       ========================================================================= */

    public function test_admin_can_crud_sources_campagnes_produits(): void
    {
        // --- 1. Source CRUD ---
        $sourceData = [
            'nom' => 'LinkedIn Ads',
            'description' => 'Campagnes sur LinkedIn',
            'statut' => 'actif',
        ];

        $response = $this->actingAs($this->admin)->post(route('sources.store'), $sourceData);
        $response->assertRedirect(route('sources.index'));
        $this->assertDatabaseHas('sources', ['nom' => 'LinkedIn Ads']);
        $this->assertDatabaseHas('activity_logs', ['action' => 'Création source', 'module' => 'Marketing']);

        $source = Source::where('nom', 'LinkedIn Ads')->firstOrFail();

        // Update Source
        $response = $this->actingAs($this->admin)->put(route('sources.update', $source), [
            'nom' => 'LinkedIn Ads Modifiée',
            'statut' => 'inactif',
        ]);
        $response->assertRedirect(route('sources.index'));
        $this->assertDatabaseHas('sources', ['id' => $source->id, 'nom' => 'LinkedIn Ads Modifiée']);
        $this->assertDatabaseHas('activity_logs', ['action' => 'Modification source', 'module' => 'Marketing']);

        // Delete Source
        $response = $this->actingAs($this->admin)->delete(route('sources.destroy', $source));
        $response->assertRedirect(route('sources.index'));
        $this->assertSoftDeleted($source);
        $this->assertDatabaseHas('activity_logs', ['action' => 'Suppression source', 'module' => 'Marketing']);


        // --- 2. Campagne CRUD ---
        $campagneData = [
            'filiale_id' => $this->filiale->id,
            'nom' => 'Campagne Printemps 2026',
            'budget' => 5000,
            'statut' => 'actif',
        ];

        $response = $this->actingAs($this->admin)->post(route('campagnes.store'), $campagneData);
        $response->assertRedirect(route('campagnes.index'));
        $this->assertDatabaseHas('campagnes', ['nom' => 'Campagne Printemps 2026']);
        $this->assertDatabaseHas('activity_logs', ['action' => 'Création campagne', 'module' => 'Marketing']);

        $campagne = Campagne::where('nom', 'Campagne Printemps 2026')->firstOrFail();

        // Update Campagne
        $response = $this->actingAs($this->admin)->put(route('campagnes.update', $campagne), [
            'filiale_id' => $this->filiale->id,
            'nom' => 'Campagne Printemps 2026 Modifiée',
            'statut' => 'inactif',
        ]);
        $response->assertRedirect(route('campagnes.index'));
        $this->assertDatabaseHas('campagnes', ['id' => $campagne->id, 'nom' => 'Campagne Printemps 2026 Modifiée']);
        $this->assertDatabaseHas('activity_logs', ['action' => 'Modification campagne', 'module' => 'Marketing']);

        // Delete Campagne
        $response = $this->actingAs($this->admin)->delete(route('campagnes.destroy', $campagne));
        $response->assertRedirect(route('campagnes.index'));
        $this->assertSoftDeleted($campagne);
        $this->assertDatabaseHas('activity_logs', ['action' => 'Suppression campagne', 'module' => 'Marketing']);


        // --- 3. Produit CRUD ---
        $produitData = [
            'filiale_id' => $this->filiale->id,
            'nom' => 'Formation JS Expert',
            'prix' => 1500.00,
            'statut' => 'actif',
        ];

        $response = $this->actingAs($this->admin)->post(route('produits.store'), $produitData);
        $response->assertRedirect(route('produits.index'));
        $this->assertDatabaseHas('produits', ['nom' => 'Formation JS Expert']);
        $this->assertDatabaseHas('activity_logs', ['action' => 'Création produit', 'module' => 'Catalogue']);

        $produit = Produit::where('nom', 'Formation JS Expert')->firstOrFail();

        // Update Produit
        $response = $this->actingAs($this->admin)->put(route('produits.update', $produit), [
            'filiale_id' => $this->filiale->id,
            'nom' => 'Formation JS Advanced',
            'prix' => 1800.00,
            'statut' => 'actif',
        ]);
        $response->assertRedirect(route('produits.index'));
        $this->assertDatabaseHas('produits', ['id' => $produit->id, 'nom' => 'Formation JS Advanced', 'prix' => 1800]);
        $this->assertDatabaseHas('activity_logs', ['action' => 'Modification produit', 'module' => 'Catalogue']);

        // Delete Produit
        $response = $this->actingAs($this->admin)->delete(route('produits.destroy', $produit));
        $response->assertRedirect(route('produits.index'));
        $this->assertSoftDeleted($produit);
        $this->assertDatabaseHas('activity_logs', ['action' => 'Suppression produit', 'module' => 'Catalogue']);
    }

    /* =========================================================================
       PROSPECT CONVERSION TESTS
       ========================================================================= */

    public function test_commercial_can_crud_prospects_with_history_tracking(): void
    {
        // 1. Create Prospect
        $prospectData = [
            'filiale_id' => $this->filiale->id,
            'commercial_id' => $this->commercial->id,
            'nom' => 'Martin',
            'prenom' => 'Alice',
            'email' => 'alice.martin@example.com',
            'telephone' => '0706050403',
            'statut' => 'Nouveau',
        ];

        $response = $this->actingAs($this->commercial)->post(route('prospects.store'), $prospectData);
        $response->assertRedirect(route('prospects.index'));

        $prospect = Prospect::where('email', 'alice.martin@example.com')->firstOrFail();

        // Verify Prospect History entry exists for Creation
        $this->assertDatabaseHas('prospect_histories', [
            'prospect_id' => $prospect->id,
            'action' => 'Création',
            'ancien_statut' => null,
            'nouveau_statut' => 'Nouveau',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'Création prospect',
            'module' => 'Prospects',
        ]);

        // 2. Update Prospect status
        $updateData = $prospect->toArray();
        $updateData['statut'] = 'Contacté';

        $response = $this->actingAs($this->commercial)->put(route('prospects.update', $prospect), $updateData);
        $response->assertRedirect(route('prospects.index'));

        // Verify history entry exists for status change
        $this->assertDatabaseHas('prospect_histories', [
            'prospect_id' => $prospect->id,
            'action' => 'Changement statut',
            'ancien_statut' => 'Nouveau',
            'nouveau_statut' => 'Contacté',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'Changement statut prospect',
            'module' => 'Prospects',
        ]);
    }

    public function test_commercial_can_convert_prospect_to_client(): void
    {
        $prospect = Prospect::create([
            'filiale_id' => $this->filiale->id,
            'commercial_id' => $this->commercial->id,
            'nom' => 'Bernard',
            'prenom' => 'Paul',
            'email' => 'paul.bernard@example.com',
            'telephone' => '0612345678',
            'entreprise' => 'Acme Corp',
            'adresse' => '10 rue de Lyon',
            'ville' => 'Lyon',
            'statut' => 'Qualifié',
        ]);

        $response = $this->actingAs($this->commercial)->post(route('prospects.convert', $prospect));

        // Get the created client
        $client = Client::where('prospect_id', $prospect->id)->firstOrFail();

        // Assert redirect to client page
        $response->assertRedirect(route('clients.show', $client));

        // Assert Client fields match Prospect
        $this->assertEquals('Bernard', $client->nom);
        $this->assertEquals('Paul', $client->prenom);
        $this->assertEquals('paul.bernard@example.com', $client->email);
        $this->assertEquals('Acme Corp', $client->entreprise);
        $this->assertEquals('Actif', $client->statut);

        // Assert Prospect status updated to Gagné
        $prospect->refresh();
        $this->assertEquals('Gagné', $prospect->statut);

        // Assert history entry exists for conversion
        $this->assertDatabaseHas('prospect_histories', [
            'prospect_id' => $prospect->id,
            'action' => 'Conversion client',
            'ancien_statut' => 'Qualifié',
            'nouveau_statut' => 'Gagné',
        ]);

        // Assert activity log exists
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'Conversion client',
            'module' => 'Clients',
        ]);

        // Trying to convert again should redirect back with error
        $response2 = $this->actingAs($this->commercial)->post(route('prospects.convert', $prospect));
        $response2->assertRedirect(route('prospects.show', $prospect));
        $response2->assertSessionHas('error', 'Ce prospect est déjà converti en client.');
    }

    /* =========================================================================
       VENTE CALCULATIONS & PERMISSIONS TESTS
       ========================================================================= */

    public function test_vente_calculations_on_creation_and_update(): void
    {
        // Setup Client and Product
        $client = Client::create([
            'filiale_id' => $this->filiale->id,
            'commercial_id' => $this->commercial->id,
            'nom' => 'Dupond',
            'prenom' => 'Pierre',
            'email' => 'pierre.dupond@example.com',
            'statut' => 'Actif',
        ]);

        $produit = Produit::create([
            'filiale_id' => $this->filiale->id,
            'nom' => 'Logiciel CRM SaaS',
            'prix' => 120.00,
            'statut' => 'actif',
        ]);

        // 1. Create Sale: quantity=5, reduction=100.
        // Price * Qty = 120.00 * 5 = 600.00
        // Expected amount = 600.00 - 100.00 = 500.00
        $venteData = [
            'client_id' => $client->id,
            'produit_id' => $produit->id,
            'commercial_id' => $this->commercial->id,
            'filiale_id' => $this->filiale->id,
            'quantite' => 5,
            'reduction' => 100,
            'statut' => 'En attente',
            'date_vente' => now()->format('Y-m-d H:i:s'),
        ];

        $response = $this->actingAs($this->commercial)->post(route('ventes.store'), $venteData);
        $response->assertRedirect(route('ventes.index'));

        $this->assertDatabaseHas('ventes', [
            'client_id' => $client->id,
            'produit_id' => $produit->id,
            'quantite' => 5,
            'reduction' => 100,
            'montant' => 500.00,
        ]);

        $vente = Vente::where('client_id', $client->id)->firstOrFail();

        // 2. Update Sale: quantity=3, reduction=50.
        // Price * Qty = 120.00 * 3 = 360.00
        // Expected amount = 360.00 - 50.00 = 310.00
        $updateData = [
            'client_id' => $client->id,
            'produit_id' => $produit->id,
            'commercial_id' => $this->commercial->id,
            'filiale_id' => $this->filiale->id,
            'quantite' => 3,
            'reduction' => 50,
            'statut' => 'En attente',
            'date_vente' => now()->format('Y-m-d H:i:s'),
        ];

        $response = $this->actingAs($this->commercial)->put(route('ventes.update', $vente), $updateData);
        $response->assertRedirect(route('ventes.index'));

        $this->assertDatabaseHas('ventes', [
            'id' => $vente->id,
            'quantite' => 3,
            'reduction' => 50,
            'montant' => 310.00,
        ]);
    }

    public function test_commercial_restricted_from_modifying_or_deleting_validated_vente(): void
    {
        // Setup
        $client = Client::create([
            'filiale_id' => $this->filiale->id,
            'commercial_id' => $this->commercial->id,
            'nom' => 'Dupond',
            'prenom' => 'Pierre',
            'email' => 'pierre.dupond@example.com',
            'statut' => 'Actif',
        ]);

        $produit = Produit::create([
            'filiale_id' => $this->filiale->id,
            'nom' => 'Logiciel CRM SaaS',
            'prix' => 100.00,
            'statut' => 'actif',
        ]);

        // Create a validated sale
        $vente = Vente::create([
            'client_id' => $client->id,
            'produit_id' => $produit->id,
            'commercial_id' => $this->commercial->id,
            'filiale_id' => $this->filiale->id,
            'quantite' => 2,
            'reduction' => 0,
            'montant' => 200.00,
            'statut' => 'Validée',
            'date_vente' => now(),
        ]);

        // 1. Check Commercial Access Blocked (Edit, Update, Delete)
        // Edit page
        $response = $this->actingAs($this->commercial)->get(route('ventes.edit', $vente));
        $response->assertStatus(403);

        // Update action
        $updateData = [
            'client_id' => $client->id,
            'produit_id' => $produit->id,
            'commercial_id' => $this->commercial->id,
            'filiale_id' => $this->filiale->id,
            'quantite' => 4,
            'reduction' => 0,
            'statut' => 'Validée',
            'date_vente' => now()->format('Y-m-d H:i:s'),
        ];
        $response = $this->actingAs($this->commercial)->put(route('ventes.update', $vente), $updateData);
        $response->assertStatus(403);

        // Delete action
        $response = $this->actingAs($this->commercial)->delete(route('ventes.destroy', $vente));
        $response->assertStatus(403);

        // 2. Check Admin Access Allowed (Edit, Update, Delete)
        // Edit page
        $response = $this->actingAs($this->admin)->get(route('ventes.edit', $vente));
        $response->assertOk();

        // Update action
        $updateData['quantite'] = 10; // 10 * 100 = 1000
        $response = $this->actingAs($this->admin)->put(route('ventes.update', $vente), $updateData);
        $response->assertRedirect(route('ventes.index'));
        $this->assertDatabaseHas('ventes', [
            'id' => $vente->id,
            'quantite' => 10,
            'montant' => 1000.00,
        ]);

        // Delete action
        $response = $this->actingAs($this->admin)->delete(route('ventes.destroy', $vente));
        $response->assertRedirect(route('ventes.index'));
        $this->assertSoftDeleted($vente);
    }
}
