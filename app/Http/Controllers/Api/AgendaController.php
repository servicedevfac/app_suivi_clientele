<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Relance;
use App\Models\Task;
use Carbon\Carbon;

class AgendaController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $queryRelances = Relance::query();
        $queryTasks = Task::query();

        if ($user->hasRole('Commercial')) {
            $queryRelances->where('commercial_id', $user->id);
            $queryTasks->where('user_id', $user->id);
        }

        $relances = $queryRelances->with('prospect')
            ->orderBy('date_relance', 'asc')
            ->get();

        $tasks = $queryTasks->with('prospect')
            ->orderBy('date_limite', 'asc')
            ->get();

        return response()->json([
            'relances' => $relances,
            'tasks' => $tasks,
        ]);
    }

    public function updateStatus(Request $request, Relance $relance)
    {
        $user = $request->user();

        if ($user->hasRole('Commercial') && $relance->commercial_id !== $user->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $validated = $request->validate([
            'statut' => 'required|in:En attente,Effectuée,Annulée'
        ]);

        $relance->update(['statut' => $validated['statut']]);

        return response()->json($relance);
    }
}
