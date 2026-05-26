<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use App\Models\Filiale;
use App\Models\Prospect;
use App\Models\ActivityLog;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Client::with(['prospect', 'commercial', 'filiale']);

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

        $clients = $query->latest()->paginate(10)->withQueryString();

        $filiales = Filiale::all();
        $commercials = User::all();

        return view('clients.index', compact('clients', 'filiales', 'commercials'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $commercials = User::all();
        $filiales = Filiale::all();
        // Only load prospects that haven't been converted to clients yet
        $prospects = Prospect::whereDoesntHave('client')->get();

        return view('clients.create', compact('commercials', 'filiales', 'prospects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientRequest $request)
    {
        $client = Client::create($request->validated());

        ActivityLog::log('Création client', 'Clients', "Création du client {$client->nom} {$client->prenom}.");

        return redirect()->route('clients.index')->with('success', 'Client créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        $client->load(['prospect', 'commercial', 'filiale', 'ventes.produit', 'ventes.commercial']);

        return view('clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        $commercials = User::all();
        $filiales = Filiale::all();
        // Load prospects that are either not converted or are the one associated with this client
        $prospects = Prospect::whereDoesntHave('client')
            ->orWhere('id', $client->prospect_id)
            ->get();

        return view('clients.edit', compact('client', 'commercials', 'filiales', 'prospects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientRequest $request, Client $client)
    {
        $client->update($request->validated());

        ActivityLog::log('Modification client', 'Clients', "Modification du client {$client->nom} {$client->prenom}.");

        return redirect()->route('clients.index')->with('success', 'Client mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        ActivityLog::log('Suppression client', 'Clients', "Suppression du client {$client->nom} {$client->prenom}.");

        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client supprimé avec succès.');
    }
}
