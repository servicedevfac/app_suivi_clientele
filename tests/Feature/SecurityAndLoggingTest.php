<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SecurityAndLoggingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create the necessary role for testing
        Role::create(['name' => 'Administrateur']);
        Role::create(['name' => 'Commercial']);
    }

    public function test_active_user_can_access_dashboard(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
    }

    public function test_inactive_user_is_redirected_and_logged_out_with_audit_log(): void
    {
        $user = User::factory()->create(['is_active' => false]);

        $response = $this->actingAs($user)->get('/dashboard');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error', 'Votre compte a été désactivé. Veuillez contacter l\'administrateur.');

        // Verify audit log entry
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'action' => 'Déconnexion forcée',
            'module' => 'Sécurité',
            'description' => 'Accès refusé : compte utilisateur désactivé.',
        ]);
    }

    public function test_successful_login_is_logged(): void
    {
        $user = User::factory()->create([
            'is_active' => true,
            'password' => bcrypt($password = 'secret-password'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard'));

        // Verify audit log entry
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'action' => 'Connexion utilisateur',
            'module' => 'Authentification',
        ]);
    }

    public function test_logout_is_logged(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');

        // Verify audit log entry
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'action' => 'Déconnexion',
            'module' => 'Authentification',
        ]);
    }

    public function test_admin_can_access_audit_logs(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('Administrateur');

        $response = $this->actingAs($admin)->get('/logs');

        $response->assertOk();
    }

    public function test_non_admin_cannot_access_audit_logs(): void
    {
        $commercial = User::factory()->create(['is_active' => true]);
        $commercial->assignRole('Commercial');

        $response = $this->actingAs($commercial)->get('/logs');

        $response->assertStatus(403);
    }
}
