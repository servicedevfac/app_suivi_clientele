<?php

namespace App\Http\Controllers;

use App\Models\Prospect;
use App\Models\User;
use App\Models\Filiale;
use App\Models\Source;
use App\Models\Campagne;
use App\Models\ProspectHistory;
use App\Models\ActivityLog;
use App\Models\Client;
use App\Http\Requests\StoreProspectRequest;
use App\Http\Requests\UpdateProspectRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProspectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Prospect::with(['commercial', 'filiale', 'source', 'campagne']);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('filiale_id')) {
            $query->where('filiale_id', $request->filiale_id);
        }

        if ($request->filled('commercial_id')) {
            $query->where('commercial_id', $request->commercial_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%")
                  ->orWhere('entreprise', 'like', "%{$search}%");
            });
        }

        $prospects = $query->latest()->paginate(10)->withQueryString();

        $filiales = Filiale::all();
        $commercials = User::all(); // All users can act as commercial/assignee in this context

        return view('prospects.index', compact('prospects', 'filiales', 'commercials'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $commercials = User::all();
        $filiales = Filiale::all();
        $sources = Source::all();
        $campagnes = Campagne::all();

        return view('prospects.create', compact('commercials', 'filiales', 'sources', 'campagnes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProspectRequest $request)
    {
        $validated = $request->validated();

        $prospect = DB::transaction(function () use ($validated) {
            $prospect = Prospect::create($validated);

            // Add history entry
            $prospect->histories()->create([
                'user_id' => auth()->id(),
                'action' => 'Création',
                'description' => 'Création initiale du prospect.',
                'ancien_statut' => null,
                'nouveau_statut' => $prospect->statut,
            ]);

            return $prospect;
        });

        ActivityLog::log('Création prospect', 'Prospects', "Création du prospect {$prospect->nom} {$prospect->prenom}.");

        return redirect()->route('prospects.index')->with('success', 'Prospect créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Prospect $prospect)
    {
        $prospect->load(['commercial', 'filiale', 'source', 'campagne', 'histories.user', 'relances', 'tasks.user']);

        return view('prospects.show', compact('prospect'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Prospect $prospect)
    {
        $commercials = User::all();
        $filiales = Filiale::all();
        $sources = Source::all();
        $campagnes = Campagne::all();

        return view('prospects.edit', compact('prospect', 'commercials', 'filiales', 'sources', 'campagnes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProspectRequest $request, Prospect $prospect)
    {
        $validated = $request->validated();
        $ancienStatut = $prospect->statut;
        $nouveauStatut = $validated['statut'];

        DB::transaction(function () use ($prospect, $validated, $ancienStatut, $nouveauStatut) {
            $prospect->update($validated);

            if ($ancienStatut !== $nouveauStatut) {
                $prospect->histories()->create([
                    'user_id' => auth()->id(),
                    'action' => 'Changement statut',
                    'description' => "Le statut du prospect a été modifié de '{$ancienStatut}' à '{$nouveauStatut}'.",
                    'ancien_statut' => $ancienStatut,
                    'nouveau_statut' => $nouveauStatut,
                ]);

                ActivityLog::log('Changement statut prospect', 'Prospects', "Statut du prospect {$prospect->nom} {$prospect->prenom} changé à {$nouveauStatut}.");
            }
        });

        ActivityLog::log('Modification prospect', 'Prospects', "Modification du prospect {$prospect->nom} {$prospect->prenom}.");

        return redirect()->route('prospects.index')->with('success', 'Prospect mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prospect $prospect)
    {
        ActivityLog::log('Suppression prospect', 'Prospects', "Suppression du prospect {$prospect->nom} {$prospect->prenom}.");

        $prospect->delete();

        return redirect()->route('prospects.index')->with('success', 'Prospect supprimé avec succès.');
    }

    /**
     * Convert the prospect into a client.
     */
    public function convertToClient(Prospect $prospect)
    {
        if ($prospect->client()->exists()) {
            return redirect()->route('prospects.show', $prospect)->with('error', 'Ce prospect est déjà converti en client.');
        }

        $client = DB::transaction(function () use ($prospect) {
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

            // Save old status for logging
            $ancienStatut = $prospect->statut;

            // Update prospect status to Gagné
            $prospect->update(['statut' => 'Gagné']);

            // Create prospect history entry
            $prospect->histories()->create([
                'user_id' => auth()->id(),
                'action' => 'Conversion client',
                'description' => "Prospect converti en client. Nouveau client ID : {$client->id}.",
                'ancien_statut' => $ancienStatut,
                'nouveau_statut' => 'Gagné',
            ]);

            return $client;
        });

        ActivityLog::log('Conversion client', 'Clients', "Prospect {$prospect->nom} {$prospect->prenom} converti en client (Client ID: {$client->id}).");

        return redirect()->route('clients.show', $client)->with('success', 'Prospect converti en client avec succès.');
    }
}
