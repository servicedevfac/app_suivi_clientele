<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Prospect;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Task::query();

        if ($user->hasRole('Commercial')) {
            $query->where('user_id', $user->id);
        }

        $tasks = $query->with('prospect')->orderBy('date_limite', 'asc')->get();

        return response()->json($tasks);
    }

    public function show(Request $request, Task $task)
    {
        $user = $request->user();
        
        if ($user->hasRole('Commercial') && $task->user_id !== $user->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $task->load('prospect');

        return response()->json($task);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'prospect_id' => 'required|exists:prospects,id',
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priorite' => 'required|in:Basse,Moyenne,Haute',
            'date_limite' => 'nullable|date',
            'statut' => 'required|in:À faire,En cours,Terminée',
            'user_id' => 'nullable|exists:users,id',
        ]);

        if (empty($validated['user_id'])) {
            $validated['user_id'] = $request->user()->id;
        }

        $task = Task::create($validated);

        return response()->json($task, 201);
    }

    public function update(Request $request, Task $task)
    {
        $user = $request->user();
        
        if ($user->hasRole('Commercial') && $task->user_id !== $user->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $validated = $request->validate([
            'prospect_id' => 'sometimes|required|exists:prospects,id',
            'titre' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'priorite' => 'sometimes|required|in:Basse,Moyenne,Haute',
            'date_limite' => 'nullable|date',
            'statut' => 'sometimes|required|in:À faire,En cours,Terminée',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $task->update($validated);

        return response()->json($task);
    }

    public function destroy(Request $request, Task $task)
    {
        $user = $request->user();
        
        if ($user->hasRole('Commercial') && $task->user_id !== $user->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $task->delete();

        return response()->json(['message' => 'Tâche supprimée avec succès']);
    }
}
