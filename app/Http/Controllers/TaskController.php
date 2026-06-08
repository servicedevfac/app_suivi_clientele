<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Task::with(['user', 'prospect']);

        // Restrictions par rôle
        if (!auth()->user()->hasRole('Administrateur|Responsable Commercial|Directeur Général')) {
            $query->where('user_id', auth()->id());
        } elseif (request()->filled('user_id')) {
            $query->where('user_id', request('user_id'));
        }

        // Filtres
        if (request()->filled('statut')) {
            $query->where('statut', request('statut'));
        }
        if (request()->filled('priorite')) {
            $query->where('priorite', request('priorite'));
        }
        if (request()->filled('prospect_id')) {
            $query->where('prospect_id', request('prospect_id'));
        }

        if (request('view') === 'kanban') {
            $tasks = $query->latest()->get();
        } else {
            $tasks = $query->latest()->paginate(15);
        }
        $users = \App\Models\User::all();
        $prospects = \App\Models\Prospect::all();

        return view('tasks.index', compact('tasks', 'users', 'prospects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = \App\Models\User::getAssignableUsers();
        // Les commerciaux ne peuvent créer des tâches que pour eux-mêmes (ou par défaut assignées à eux)
        $prospects = auth()->user()->hasRole('Administrateur|Responsable Commercial|Directeur Général') 
            ? \App\Models\Prospect::all() 
            : \App\Models\Prospect::where('commercial_id', auth()->id())->get();

        return view('tasks.create', compact('users', 'prospects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $data = $request->validated();
        
        $assignableUsers = \App\Models\User::getAssignableUsers()->pluck('id')->toArray();
        if (isset($data['user_id']) && !in_array($data['user_id'], $assignableUsers)) {
            abort(403, "Vous ne pouvez pas assigner une tâche à ce collaborateur (supérieur).");
        }

        // Sécurité : si non-admin, la tâche est forcément assignée à soi-même
        if (!auth()->user()->hasRole('Administrateur|Responsable Commercial|Directeur Général')) {
            $data['user_id'] = auth()->id();
        }

        $task = Task::create($data);

        if ($task->user_id && $task->user_id !== auth()->id()) {
            \App\Models\Notification::create([
                'user_id' => $task->user_id,
                'titre' => 'Nouvelle tâche assignée',
                'message' => "La tâche '{$task->titre}' vous a été assignée par " . auth()->user()->name . ".",
                'type' => 'info',
            ]);
        }

        \App\Models\ActivityLog::log(
            'Création tâche',
            'Tâches',
            "Création de la tâche '{$task->titre}'" . ($task->prospect ? " pour le prospect {$task->prospect->nom} {$task->prospect->prenom}" : "")
        );

        return redirect()->route('tasks.index')->with('success', 'Tâche créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        if (!auth()->user()->hasRole('Administrateur|Responsable Commercial|Directeur Général') && $task->user_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à voir cette tâche.');
        }

        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        if (!auth()->user()->hasRole('Administrateur|Responsable Commercial|Directeur Général') && $task->user_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier cette tâche.');
        }

        $users = \App\Models\User::getAssignableUsers();
        $prospects = auth()->user()->hasRole('Administrateur|Responsable Commercial|Directeur Général') 
            ? \App\Models\Prospect::all() 
            : \App\Models\Prospect::where('commercial_id', auth()->id())->get();

        return view('tasks.edit', compact('task', 'users', 'prospects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        if (!auth()->user()->hasRole('Administrateur|Responsable Commercial|Directeur Général') && $task->user_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier cette tâche.');
        }

        $data = $request->validated();

        $assignableUsers = \App\Models\User::getAssignableUsers()->pluck('id')->toArray();
        if (isset($data['user_id']) && !in_array($data['user_id'], $assignableUsers)) {
            abort(403, "Vous ne pouvez pas assigner une tâche à ce collaborateur (supérieur).");
        }

        // Sécurité : si non-admin, on ne peut pas réassigner la tâche à quelqu'un d'autre
        if (!auth()->user()->hasRole('Administrateur|Responsable Commercial|Directeur Général')) {
            $data['user_id'] = auth()->id();
        }

        $oldUserId = $task->user_id;
        $task->update($data);

        if ($task->user_id && $task->user_id !== $oldUserId && $task->user_id !== auth()->id()) {
            \App\Models\Notification::create([
                'user_id' => $task->user_id,
                'titre' => 'Tâche réassignée',
                'message' => "La tâche '{$task->titre}' vous a été réassignée par " . auth()->user()->name . ".",
                'type' => 'info',
            ]);
        }

        \App\Models\ActivityLog::log(
            'Modification tâche',
            'Tâches',
            "Modification de la tâche '{$task->titre}'"
        );

        return redirect()->route('tasks.index')->with('success', 'Tâche mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        if (!auth()->user()->hasRole('Administrateur|Responsable Commercial|Directeur Général') && $task->user_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à supprimer cette tâche.');
        }

        $titre = $task->titre;
        $task->delete();

        \App\Models\ActivityLog::log(
            'Suppression tâche',
            'Tâches',
            "Suppression de la tâche '{$titre}'"
        );

        return redirect()->route('tasks.index')->with('success', 'Tâche supprimée avec succès.');
    }

    /**
     * Update status of the task via AJAX patch.
     */
    public function updateStatus(\Illuminate\Http\Request $request, Task $task)
    {
        if (!auth()->user()->hasRole('Administrateur|Responsable Commercial|Directeur Général') && $task->user_id !== auth()->id()) {
            return response()->json(['error' => 'Vous n\'êtes pas autorisé à modifier cette tâche.'], 403);
        }

        $request->validate([
            'statut' => 'required|string|in:À faire,En cours,Terminé',
        ]);

        $oldStatus = $task->statut;
        $task->update(['statut' => $request->statut]);

        \App\Models\ActivityLog::log(
            'Modification statut tâche',
            'Tâches',
            "Statut de la tâche '{$task->titre}' modifié de '{$oldStatus}' à '{$request->statut}'"
        );

        return response()->json([
            'success' => true,
            'message' => 'Statut de la tâche mis à jour avec succès.',
            'task' => $task
        ]);
    }
}
