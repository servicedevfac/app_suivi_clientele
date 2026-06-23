<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prospect;
use App\Models\Client;
use Illuminate\Support\Facades\DB;

class ProspectController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Prospect::query();

        if ($user->hasRole('Commercial')) {
            $query->where('commercial_id', $user->id);
        }

        $prospects = $query->with('commercial')->orderBy('created_at', 'desc')->get();

        return response()->json($prospects);
    }

    public function formOptions(Request $request)
    {
        $user = $request->user();
        if ($user && $user->hasRole('Commercial')) {
            $commercials = \App\Models\User::where('id', $user->id)->get(['id', 'name']);
        } else {
            $commercials = \App\Models\User::role('Commercial')->get(['id', 'name']);
        }

        return response()->json([
            'sources' => \App\Models\Source::all(),
            'filiales' => \App\Models\Filiale::all(),
            'campagnes' => \App\Models\Campagne::all(),
            'commercials' => $commercials,
        ]);
    }

    public function show(Request $request, Prospect $prospect)
    {
        $user = $request->user();
        
        if ($user->hasRole('Commercial') && $prospect->commercial_id !== $user->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $prospect->load(['commercial', 'histories', 'relances', 'tasks']);

        return response()->json($prospect);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'entreprise' => 'nullable|string|max:255',
            'nom' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'telephone' => 'required|string|max:20',
            'statut' => 'required|in:Nouveau,Contacté,Qualifié,En négociation,Gagné,Perdu',
            'montant_estime' => 'nullable|numeric|min:0',
            'commentaire' => 'nullable|string',
            'source_id' => 'nullable|exists:sources,id',
            'filiale_id' => 'nullable|exists:filiales,id',
            'commercial_id' => 'nullable|exists:users,id',
            'campagne_id' => 'nullable|exists:campagnes,id',
            'profession' => 'nullable|string|max:255',
            'adresse' => 'nullable|string',
            'ville' => 'nullable|string|max:255',
            'date_contact' => 'nullable|date',
            'probabilite' => 'nullable|numeric|min:0|max:100',
            'besoin' => 'nullable|string',
            'tags' => 'nullable|string',
            'prochain_rappel' => 'nullable|date',
        ]);

        if ($request->user()->hasRole('Commercial')) {
            if (!empty($validated['commercial_id']) && $validated['commercial_id'] != $request->user()->id) {
                return response()->json(['message' => 'Non autorisé à assigner un autre utilisateur'], 403);
            }
            $validated['commercial_id'] = $request->user()->id;
        } elseif (empty($validated['commercial_id'])) {
            $validated['commercial_id'] = $request->user()->id;
        }

        $prospect = Prospect::create($validated);

        return response()->json($prospect, 201);
    }

    public function update(Request $request, Prospect $prospect)
    {
        $user = $request->user();
        
        if ($user->hasRole('Commercial') && $prospect->commercial_id !== $user->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        if (in_array($prospect->statut, ['Gagné', 'Perdu']) && $request->has('statut') && $request->statut !== $prospect->statut) {
            return response()->json(['message' => 'Impossible de modifier le statut d\'un prospect déjà ' . $prospect->statut . '.'], 403);
        }

        $validated = $request->validate([
            'entreprise' => 'nullable|string|max:255',
            'nom' => 'sometimes|required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'email' => 'sometimes|required|email|max:255',
            'telephone' => 'sometimes|required|string|max:20',
            'statut' => 'sometimes|required|in:Nouveau,Contacté,Qualifié,En négociation,Gagné,Perdu',
            'montant_estime' => 'nullable|numeric|min:0',
            'commentaire' => 'nullable|string',
            'source_id' => 'nullable|exists:sources,id',
            'filiale_id' => 'nullable|exists:filiales,id',
            'commercial_id' => 'nullable|exists:users,id',
            'campagne_id' => 'nullable|exists:campagnes,id',
            'profession' => 'nullable|string|max:255',
            'adresse' => 'nullable|string',
            'ville' => 'nullable|string|max:255',
            'date_contact' => 'nullable|date',
            'probabilite' => 'nullable|numeric|min:0|max:100',
            'besoin' => 'nullable|string',
            'tags' => 'nullable|string',
            'prochain_rappel' => 'nullable|date',
        ]);

        if ($user->hasRole('Commercial') && isset($validated['commercial_id'])) {
            if ($validated['commercial_id'] != $user->id) {
                return response()->json(['message' => 'Non autorisé à assigner un autre utilisateur'], 403);
            }
            $validated['commercial_id'] = $user->id;
        }

        $prospect->update($validated);

        return response()->json($prospect);
    }

    public function destroy(Request $request, Prospect $prospect)
    {
        $user = $request->user();
        
        if ($user->hasRole('Commercial') && $prospect->commercial_id !== $user->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $prospect->delete();

        return response()->json(['message' => 'Prospect supprimé avec succès']);
    }

    public function convertToClient(Request $request, Prospect $prospect)
    {
        $user = $request->user();
        
        if ($user->hasRole('Commercial') && $prospect->commercial_id !== $user->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        if ($prospect->client()->exists()) {
            return response()->json(['message' => 'Ce prospect est déjà converti en client.'], 400);
        }

        $client = DB::transaction(function () use ($prospect, $user) {
            // Create client
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
                'date_conversion' => now(),
            ]);

            $ancienStatut = $prospect->statut;
            $prospect->update(['statut' => 'Gagné']);

            $prospect->histories()->create([
                'user_id' => $user->id,
                'action' => 'Conversion client (Mobile)',
                'description' => "Prospect converti en client. Nouveau client ID : {$client->id}.",
                'ancien_statut' => $ancienStatut,
                'nouveau_statut' => 'Gagné',
            ]);

            return $client;
        });

        return response()->json($client, 201);
    }
}
