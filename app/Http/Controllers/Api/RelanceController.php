<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Relance;
use App\Models\Prospect;

class RelanceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Relance::query();

        if ($user->hasRole('Commercial')) {
            $query->where('commercial_id', $user->id);
        }

        $relances = $query->with('prospect')->orderBy('date_relance', 'asc')->get();

        return response()->json($relances);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'prospect_id' => 'required|exists:prospects,id',
            'date_relance' => 'required|date',
            'heure_relance' => 'nullable|date_format:H:i',
            'canal' => 'required|string|max:255',
            'commentaire' => 'nullable|string',
            'statut' => 'required|in:En attente,Effectuée,Annulée',
            'commercial_id' => 'nullable|exists:users,id',
        ]);

        if (empty($validated['commercial_id'])) {
            $validated['commercial_id'] = $request->user()->id;
        }

        $relance = Relance::create($validated);

        return response()->json($relance, 201);
    }

    public function update(Request $request, Relance $relance)
    {
        $user = $request->user();
        
        if ($user->hasRole('Commercial') && $relance->commercial_id !== $user->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $validated = $request->validate([
            'prospect_id' => 'sometimes|required|exists:prospects,id',
            'date_relance' => 'sometimes|required|date',
            'heure_relance' => 'nullable|date_format:H:i',
            'canal' => 'sometimes|required|string|max:255',
            'commentaire' => 'nullable|string',
            'statut' => 'sometimes|required|in:En attente,Effectuée,Annulée',
            'commercial_id' => 'nullable|exists:users,id',
        ]);

        $relance->update($validated);

        return response()->json($relance);
    }

    public function destroy(Request $request, Relance $relance)
    {
        $user = $request->user();
        
        if ($user->hasRole('Commercial') && $relance->commercial_id !== $user->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $relance->delete();

        return response()->json(['message' => 'Relance supprimée avec succès']);
    }
}
