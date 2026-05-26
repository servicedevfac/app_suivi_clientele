<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Prospect;
use App\Models\Filiale;
use App\Models\Task;
use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TaskStatusTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $commercial1;
    protected User $commercial2;
    protected Filiale $filiale;
    protected Prospect $prospect;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Spatie Roles
        Role::create(['name' => 'Administrateur']);
        Role::create(['name' => 'Commercial']);

        // Create Users
        $this->admin = User::factory()->create(['is_active' => true]);
        $this->admin->assignRole('Administrateur');

        $this->commercial1 = User::factory()->create(['is_active' => true]);
        $this->commercial1->assignRole('Commercial');

        $this->commercial2 = User::factory()->create(['is_active' => true]);
        $this->commercial2->assignRole('Commercial');

        // Create Filiale
        $this->filiale = Filiale::create(['nom' => 'Test Filiale']);

        // Create Prospect
        $this->prospect = Prospect::create([
            'filiale_id' => $this->filiale->id,
            'nom' => 'Doe',
            'prenom' => 'John',
            'email' => 'john.status@example.com',
            'commercial_id' => $this->commercial1->id,
            'statut' => 'Nouveau'
        ]);
    }

    public function test_admin_can_update_any_task_status(): void
    {
        $task = Task::create([
            'user_id' => $this->commercial1->id,
            'prospect_id' => $this->prospect->id,
            'titre' => 'Tâche pour Commercial 1',
            'description' => 'Test drag and drop',
            'priorite' => 'Moyenne',
            'statut' => 'À faire',
        ]);

        $response = $this->actingAs($this->admin)->patchJson(route('tasks.update-status', $task), [
            'statut' => 'En cours'
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'message' => 'Statut de la tâche mis à jour avec succès.',
            'task' => [
                'id' => $task->id,
                'statut' => 'En cours'
            ]
        ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'statut' => 'En cours'
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'Modification statut tâche',
            'module' => 'Tâches',
            'description' => "Statut de la tâche 'Tâche pour Commercial 1' modifié de 'À faire' à 'En cours'"
        ]);
    }

    public function test_commercial_can_update_own_task_status(): void
    {
        $task = Task::create([
            'user_id' => $this->commercial1->id,
            'prospect_id' => $this->prospect->id,
            'titre' => 'Ma propre tâche',
            'description' => 'Changement par moi-même',
            'priorite' => 'Haute',
            'statut' => 'À faire',
        ]);

        $response = $this->actingAs($this->commercial1)->patchJson(route('tasks.update-status', $task), [
            'statut' => 'Terminé'
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'task' => [
                'id' => $task->id,
                'statut' => 'Terminé'
            ]
        ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'statut' => 'Terminé'
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'Modification statut tâche',
            'module' => 'Tâches',
            'description' => "Statut de la tâche 'Ma propre tâche' modifié de 'À faire' à 'Terminé'"
        ]);
    }

    public function test_commercial_cannot_update_others_task_status(): void
    {
        // Tâche de commercial 1
        $task = Task::create([
            'user_id' => $this->commercial1->id,
            'prospect_id' => $this->prospect->id,
            'titre' => 'Tâche de Commercial 1',
            'description' => 'Interdit pour commercial 2',
            'priorite' => 'Haute',
            'statut' => 'À faire',
        ]);

        // Commercial 2 tente de modifier la tâche de Commercial 1
        $response = $this->actingAs($this->commercial2)->patchJson(route('tasks.update-status', $task), [
            'statut' => 'En cours'
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'error' => "Vous n'êtes pas autorisé à modifier cette tâche."
        ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'statut' => 'À faire'
        ]);
    }

    public function test_task_status_update_requires_valid_status(): void
    {
        $task = Task::create([
            'user_id' => $this->commercial1->id,
            'prospect_id' => $this->prospect->id,
            'titre' => 'Tâche Test Validation',
            'description' => 'Test validation statut',
            'priorite' => 'Haute',
            'statut' => 'À faire',
        ]);

        // Statut invalide
        $response = $this->actingAs($this->commercial1)->patchJson(route('tasks.update-status', $task), [
            'statut' => 'En attente'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['statut']);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'statut' => 'À faire'
        ]);
    }
}
