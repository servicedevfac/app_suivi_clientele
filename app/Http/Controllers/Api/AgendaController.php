<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Relance;
use Carbon\Carbon;

class AgendaController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Relance::query();

        if ($user->hasRole('Commercial')) {
            $query->where('commercial_id', $user->id);
        }

        $relances = $query->with('prospect')
            ->orderBy('date_relance', 'asc')
            ->get();

        return response()->json($relances);
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
