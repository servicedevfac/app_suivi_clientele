<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Prospect;
use App\Models\Filiale;
use App\Models\Task;
use App\Models\Relance;
use App\Models\Notification;
use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TaskAndRelanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $commercial1;
    protected User $commercial2;
    protected Filiale $filiale;
    protected Prospect $prospect1;
    protected Prospect $prospect2;

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

        // Create Prospects
        $this->prospect1 = Prospect::create([
            'filiale_id' => $this->filiale->id,
            'nom' => 'Doe',
            'prenom' => 'John',
            'email' => 'john@example.com',
            'commercial_id' => $this->commercial1->id,
            'statut' => 'Nouveau'
        ]);

        $this->prospect2 = Prospect::create([
            'filiale_id' => $this->filiale->id,
            'nom' => 'Smith',
            'prenom' => 'Jane',
            'email' => 'jane@example.com',
            'commercial_id' => $this->commercial2->id,
            'statut' => 'Nouveau'
        ]);
    }

    /* =========================================================================
       TASKS TESTS
       ========================================================================= */

    public function test_admin_can_crud_tasks(): void
    {
        // 1. Create
        $response = $this->actingAs($this->admin)->post(route('tasks.store'), [
            'user_id' => $this->commercial1->id,
            'prospect_id' => $this->prospect1->id,
            'titre' => 'Tâche Test Admin',
            'description' => 'Description de test',
            'priorite' => 'Haute',
            'date_limite' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'statut' => 'À faire',
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'titre' => 'Tâche Test Admin',
            'user_id' => $this->commercial1->id
        ]);

        // Check Activity Log
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'Création tâche',
            'module' => 'Tâches',
        ]);

        $task = Task::where('titre', 'Tâche Test Admin')->firstOrFail();

        // 2. Read
        $response = $this->actingAs($this->admin)->get(route('tasks.show', $task));
        $response->assertOk();
        $response->assertSee('Tâche Test Admin');

        // 3. Update
        $response = $this->actingAs($this->admin)->put(route('tasks.update', $task), [
            'user_id' => $this->commercial2->id, // Reassign
            'prospect_id' => $this->prospect1->id,
            'titre' => 'Tâche Test Admin Modifiée',
            'priorite' => 'Urgente',
            'statut' => 'En cours',
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'titre' => 'Tâche Test Admin Modifiée',
            'user_id' => $this->commercial2->id,
            'priorite' => 'Urgente',
            'statut' => 'En cours',
        ]);

        // Check Activity Log
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'Modification tâche',
            'module' => 'Tâches',
        ]);

        // 4. Delete
        $response = $this->actingAs($this->admin)->delete(route('tasks.destroy', $task));
        $response->assertRedirect(route('tasks.index'));
        $this->assertSoftDeleted('tasks', ['id' => $task->id]);

        // Check Activity Log
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'Suppression tâche',
            'module' => 'Tâches',
        ]);
    }

    public function test_commercial_can_crud_own_tasks(): void
    {
        // 1. Create
        $response = $this->actingAs($this->commercial1)->post(route('tasks.store'), [
            'user_id' => $this->commercial1->id,
            'prospect_id' => $this->prospect1->id,
            'titre' => 'Ma Tâche',
            'priorite' => 'Faible',
            'statut' => 'À faire',
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'titre' => 'Ma Tâche',
            'user_id' => $this->commercial1->id
        ]);

        $task = Task::where('titre', 'Ma Tâche')->firstOrFail();

        // 2. Read
        $response = $this->actingAs($this->commercial1)->get(route('tasks.show', $task));
        $response->assertOk();

        // 3. Update (ensure user_id remains commercial1 even if they try to change it)
        $response = $this->actingAs($this->commercial1)->put(route('tasks.update', $task), [
            'user_id' => $this->commercial2->id, // Attempt to reassign (should be ignored by controller security)
            'titre' => 'Ma Tâche Modifiée',
            'priorite' => 'Moyenne',
            'statut' => 'Terminé',
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'titre' => 'Ma Tâche Modifiée',
            'user_id' => $this->commercial1->id, // Maintained as commercial1
            'statut' => 'Terminé',
        ]);

        // 4. Delete
        $response = $this->actingAs($this->commercial1)->delete(route('tasks.destroy', $task));
        $response->assertRedirect(route('tasks.index'));
        $this->assertSoftDeleted('tasks', ['id' => $task->id]);
    }

    public function test_commercial_cannot_view_other_user_task(): void
    {
        $task = Task::create([
            'user_id' => $this->commercial2->id,
            'titre' => 'Tâche de Commercial 2',
            'priorite' => 'Moyenne',
            'statut' => 'À faire',
        ]);

        $response = $this->actingAs($this->commercial1)->get(route('tasks.show', $task));
        $response->assertStatus(403);
    }

    public function test_commercial_cannot_update_other_user_task(): void
    {
        $task = Task::create([
            'user_id' => $this->commercial2->id,
            'titre' => 'Tâche de Commercial 2',
            'priorite' => 'Moyenne',
            'statut' => 'À faire',
        ]);

        $response = $this->actingAs($this->commercial1)->put(route('tasks.update', $task), [
            'user_id' => $this->commercial2->id,
            'titre' => 'Tentative Hack',
            'priorite' => 'Moyenne',
            'statut' => 'En cours',
        ]);
        $response->assertStatus(403);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'titre' => 'Tâche de Commercial 2',
        ]);
    }

    public function test_commercial_cannot_delete_other_user_task(): void
    {
        $task = Task::create([
            'user_id' => $this->commercial2->id,
            'titre' => 'Tâche de Commercial 2',
            'priorite' => 'Moyenne',
            'statut' => 'À faire',
        ]);

        $response = $this->actingAs($this->commercial1)->delete(route('tasks.destroy', $task));
        $response->assertStatus(403);
        $this->assertNotSoftDeleted('tasks', ['id' => $task->id]);
    }

    public function test_task_filters(): void
    {
        // Create multiple tasks
        $task1 = Task::create([
            'user_id' => $this->commercial1->id,
            'prospect_id' => $this->prospect1->id,
            'titre' => 'Task A',
            'priorite' => 'Urgente',
            'statut' => 'À faire',
        ]);

        $task2 = Task::create([
            'user_id' => $this->commercial1->id,
            'prospect_id' => $this->prospect2->id,
            'titre' => 'Task B',
            'priorite' => 'Moyenne',
            'statut' => 'En cours',
        ]);

        // Filter by status
        $response = $this->actingAs($this->admin)->get(route('tasks.index', ['statut' => 'En cours']));
        $response->assertOk();
        $response->assertViewHas('tasks', function ($tasks) use ($task1, $task2) {
            return !$tasks->contains('id', $task1->id) && $tasks->contains('id', $task2->id);
        });

        // Filter by priority
        $response = $this->actingAs($this->admin)->get(route('tasks.index', ['priorite' => 'Urgente']));
        $response->assertOk();
        $response->assertViewHas('tasks', function ($tasks) use ($task1, $task2) {
            return $tasks->contains('id', $task1->id) && !$tasks->contains('id', $task2->id);
        });

        // Filter by user
        $response = $this->actingAs($this->admin)->get(route('tasks.index', ['user_id' => $this->commercial2->id]));
        $response->assertOk();
        $response->assertViewHas('tasks', function ($tasks) use ($task1, $task2) {
            return !$tasks->contains('id', $task1->id) && !$tasks->contains('id', $task2->id);
        });
    }

    /* =========================================================================
       RELANCES TESTS
       ========================================================================= */

    public function test_admin_can_crud_relances(): void
    {
        // 1. Create
        $response = $this->actingAs($this->admin)->post(route('relances.store'), [
            'prospect_id' => $this->prospect1->id,
            'commercial_id' => $this->commercial1->id,
            'date_relance' => today()->format('Y-m-d'),
            'heure_relance' => '14:30',
            'canal' => 'WhatsApp',
            'commentaire' => 'Message de relance WhatsApp',
            'statut' => 'En attente',
        ]);

        $response->assertRedirect(route('relances.index'));
        $this->assertDatabaseHas('relances', [
            'prospect_id' => $this->prospect1->id,
            'commercial_id' => $this->commercial1->id,
            'canal' => 'WhatsApp',
        ]);

        // Check Activity Log
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'Planification relance',
            'module' => 'Relances',
        ]);

        $relance = Relance::where('canal', 'WhatsApp')->firstOrFail();

        // 2. Read
        $response = $this->actingAs($this->admin)->get(route('relances.show', $relance));
        $response->assertOk();
        $response->assertSee('WhatsApp');

        // 3. Update
        $response = $this->actingAs($this->admin)->put(route('relances.update', $relance), [
            'prospect_id' => $this->prospect2->id, // Change prospect
            'commercial_id' => $this->commercial2->id, // Reassign
            'date_relance' => today()->addDay()->format('Y-m-d'),
            'heure_relance' => '10:00',
            'canal' => 'Email',
            'commentaire' => 'Commentaire email',
            'statut' => 'Réalisée',
        ]);

        $response->assertRedirect(route('relances.index'));
        $this->assertDatabaseHas('relances', [
            'id' => $relance->id,
            'prospect_id' => $this->prospect2->id,
            'commercial_id' => $this->commercial2->id,
            'canal' => 'Email',
            'statut' => 'Réalisée',
        ]);

        // Check Activity Log
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'Modification relance',
            'module' => 'Relances',
        ]);

        // 4. Delete
        $response = $this->actingAs($this->admin)->delete(route('relances.destroy', $relance));
        $response->assertRedirect(route('relances.index'));
        $this->assertSoftDeleted('relances', ['id' => $relance->id]);

        // Check Activity Log
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'Suppression relance',
            'module' => 'Relances',
        ]);
    }

    public function test_commercial_can_crud_own_relances(): void
    {
        // 1. Create
        $response = $this->actingAs($this->commercial1)->post(route('relances.store'), [
            'prospect_id' => $this->prospect1->id,
            'commercial_id' => $this->commercial1->id,
            'date_relance' => today()->format('Y-m-d'),
            'canal' => 'SMS',
            'statut' => 'En attente',
        ]);

        $response->assertRedirect(route('relances.index'));
        $this->assertDatabaseHas('relances', [
            'prospect_id' => $this->prospect1->id,
            'commercial_id' => $this->commercial1->id,
            'canal' => 'SMS',
        ]);

        $relance = Relance::where('canal', 'SMS')->firstOrFail();

        // 2. Read
        $response = $this->actingAs($this->commercial1)->get(route('relances.show', $relance));
        $response->assertOk();

        // 3. Update (ensure commercial_id remains commercial1)
        $response = $this->actingAs($this->commercial1)->put(route('relances.update', $relance), [
            'prospect_id' => $this->prospect1->id,
            'commercial_id' => $this->commercial2->id, // Attempt to reassign (should be overwritten)
            'date_relance' => today()->format('Y-m-d'),
            'canal' => 'Appel',
            'statut' => 'Réalisée',
        ]);

        $response->assertRedirect(route('relances.index'));
        $this->assertDatabaseHas('relances', [
            'id' => $relance->id,
            'commercial_id' => $this->commercial1->id, // Retained
            'canal' => 'Appel',
        ]);

        // 4. Delete
        $response = $this->actingAs($this->commercial1)->delete(route('relances.destroy', $relance));
        $response->assertRedirect(route('relances.index'));
        $this->assertSoftDeleted('relances', ['id' => $relance->id]);
    }

    public function test_commercial_cannot_view_other_user_relance(): void
    {
        $relance = Relance::create([
            'prospect_id' => $this->prospect2->id,
            'commercial_id' => $this->commercial2->id,
            'date_relance' => today(),
            'canal' => 'Appel',
            'statut' => 'En attente'
        ]);

        $response = $this->actingAs($this->commercial1)->get(route('relances.show', $relance));
        $response->assertStatus(403);
    }

    public function test_commercial_cannot_update_other_user_relance(): void
    {
        $relance = Relance::create([
            'prospect_id' => $this->prospect2->id,
            'commercial_id' => $this->commercial2->id,
            'date_relance' => today(),
            'canal' => 'Appel',
            'statut' => 'En attente'
        ]);

        $response = $this->actingAs($this->commercial1)->put(route('relances.update', $relance), [
            'prospect_id' => $this->prospect2->id,
            'commercial_id' => $this->commercial2->id,
            'date_relance' => today()->addDay()->format('Y-m-d'),
            'canal' => 'Email',
            'statut' => 'Réalisée'
        ]);
        $response->assertStatus(403);
    }

    public function test_commercial_cannot_delete_other_user_relance(): void
    {
        $relance = Relance::create([
            'prospect_id' => $this->prospect2->id,
            'commercial_id' => $this->commercial2->id,
            'date_relance' => today(),
            'canal' => 'Appel',
            'statut' => 'En attente'
        ]);

        $response = $this->actingAs($this->commercial1)->delete(route('relances.destroy', $relance));
        $response->assertStatus(403);
        $this->assertNotSoftDeleted('relances', ['id' => $relance->id]);
    }

    public function test_relance_date_filters(): void
    {
        // 1. Today
        $todayRelance = Relance::create([
            'prospect_id' => $this->prospect1->id,
            'commercial_id' => $this->commercial1->id,
            'date_relance' => today(),
            'canal' => 'Appel',
            'statut' => 'En attente'
        ]);

        // 2. Upcoming
        $upcomingRelance = Relance::create([
            'prospect_id' => $this->prospect1->id,
            'commercial_id' => $this->commercial1->id,
            'date_relance' => today()->addDays(2),
            'canal' => 'Email',
            'statut' => 'En attente'
        ]);

        // 3. Overdue (past and En attente)
        $overdueRelance = Relance::create([
            'prospect_id' => $this->prospect1->id,
            'commercial_id' => $this->commercial1->id,
            'date_relance' => today()->subDays(2),
            'canal' => 'SMS',
            'statut' => 'En attente'
        ]);

        // Filter: today
        $response = $this->actingAs($this->admin)->get(route('relances.index', ['filter' => 'today']));
        $response->assertOk();
        $response->assertViewHas('relances', function ($relances) use ($todayRelance, $upcomingRelance, $overdueRelance) {
            return $relances->contains('id', $todayRelance->id) 
                && !$relances->contains('id', $upcomingRelance->id)
                && !$relances->contains('id', $overdueRelance->id);
        });

        // Filter: upcoming
        $response = $this->actingAs($this->admin)->get(route('relances.index', ['filter' => 'upcoming']));
        $response->assertOk();
        $response->assertViewHas('relances', function ($relances) use ($todayRelance, $upcomingRelance, $overdueRelance) {
            return !$relances->contains('id', $todayRelance->id) 
                && $relances->contains('id', $upcomingRelance->id)
                && !$relances->contains('id', $overdueRelance->id);
        });

        // Filter: overdue
        $response = $this->actingAs($this->admin)->get(route('relances.index', ['filter' => 'overdue']));
        $response->assertOk();
        $response->assertViewHas('relances', function ($relances) use ($todayRelance, $upcomingRelance, $overdueRelance) {
            return !$relances->contains('id', $todayRelance->id) 
                && !$relances->contains('id', $upcomingRelance->id)
                && $relances->contains('id', $overdueRelance->id);
        });
    }

    /* =========================================================================
       NOTIFICATIONS TESTS
       ========================================================================= */

    public function test_user_can_view_own_notifications(): void
    {
        $notif1 = Notification::create([
            'user_id' => $this->commercial1->id,
            'titre' => 'Notif Perso',
            'message' => 'Ceci est ton alerte.',
            'type' => 'info',
            'is_read' => false
        ]);

        $notif2 = Notification::create([
            'user_id' => $this->commercial2->id,
            'titre' => 'Notif Autre',
            'message' => 'Ceci est pour quelqu\'un d\'autre.',
            'type' => 'info',
            'is_read' => false
        ]);

        $response = $this->actingAs($this->commercial1)->get(route('notifications.index'));
        $response->assertOk();
        $response->assertSee('Notif Perso');
        $response->assertDontSee('Notif Autre');
    }

    public function test_user_can_mark_notification_as_read(): void
    {
        $notif = Notification::create([
            'user_id' => $this->commercial1->id,
            'titre' => 'Alerte urgente',
            'message' => 'À lire immédiatement.',
            'type' => 'danger',
            'is_read' => false
        ]);

        $response = $this->actingAs($this->commercial1)->patch(route('notifications.update', $notif));
        $response->assertRedirect();
        
        $this->assertTrue($notif->fresh()->is_read);
    }

    public function test_user_cannot_mark_other_user_notification_as_read(): void
    {
        $notif = Notification::create([
            'user_id' => $this->commercial2->id,
            'titre' => 'Alerte Commercial 2',
            'message' => 'Message secret.',
            'type' => 'info',
            'is_read' => false
        ]);

        $response = $this->actingAs($this->commercial1)->patch(route('notifications.update', $notif));
        $response->assertStatus(403);
        
        $this->assertFalse($notif->fresh()->is_read);
    }
}
