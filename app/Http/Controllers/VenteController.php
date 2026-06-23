<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use App\Models\Client;
use App\Models\Produit;
use App\Models\User;
use App\Models\Filiale;
use App\Models\ActivityLog;
use App\Http\Requests\StoreVenteRequest;
use App\Http\Requests\UpdateVenteRequest;
use Illuminate\Http\Request;

class VenteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Vente::with(['client', 'produit', 'commercial', 'filiale']);

        if (!auth()->user()->hasRole('Administrateur|Responsable Commercial|Directeur Général')) {
            $query->where('commercial_id', auth()->id());
        }

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
            $query->whereHas('client', function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('entreprise', 'like', "%{$search}%");
            });
        }

        $ventes = $query->latest()->paginate(10)->withQueryString();

        $filiales = Filiale::all();
        $commercials = User::getAssignableUsers();

        return view('ventes.index', compact('ventes', 'filiales', 'commercials'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::where('statut', 'Actif')->get();
        $produits = Produit::all();
        $commercials = User::getAssignableUsers();
        $filiales = Filiale::all();

        return view('ventes.create', compact('clients', 'produits', 'commercials', 'filiales'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVenteRequest $request)
    {
        $validated = $request->validated();

        // Calculate amount: (product price * quantity) - reduction
        $produit = Produit::findOrFail($validated['produit_id']);
        $reduction = $validated['reduction'] ?? 0;
        $montantCalculer = ($produit->prix * $validated['quantite']) - $reduction;
        
        $validated['montant'] = max(0, $montantCalculer);

        $assignableUsers = User::getAssignableUsers()->pluck('id')->toArray();
        if (isset($validated['commercial_id']) && !in_array($validated['commercial_id'], $assignableUsers)) {
            abort(403, "Vous ne pouvez pas assigner une vente à ce collaborateur.");
        }
        if (!auth()->user()->hasRole('Administrateur|Responsable Commercial|Directeur Général')) {
            $validated['commercial_id'] = auth()->id();
        }

        $vente = Vente::create($validated);

        ActivityLog::log('Création vente', 'Ventes', "Création de la vente ID {$vente->id} pour un montant de {$vente->montant} xof.");

        return redirect()->route('ventes.index')->with('success', 'Vente enregistrée avec succès.');
    }

    /**
     * Display the specified resource (redirecting to index or show if view exists, let's support show).
     */
    public function show(Vente $vente)
    {
        $vente->load(['client', 'produit', 'commercial', 'filiale']);
        return view('ventes.show', compact('vente'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vente $vente)
    {
        // Restrict modification of validated sales to Admins
        if ($vente->statut === 'Validée' && !auth()->user()->hasRole('Administrateur')) {
            abort(403, 'Seul un administrateur peut modifier ou annuler une vente validée.');
        }

        $clients = Client::all();
        $produits = Produit::all();
        $commercials = User::getAssignableUsers();
        $filiales = Filiale::all();

        return view('ventes.edit', compact('vente', 'clients', 'produits', 'commercials', 'filiales'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVenteRequest $request, Vente $vente)
    {
        // Restrict modification of validated sales to Admins
        if ($vente->statut === 'Validée' && !auth()->user()->hasRole('Administrateur')) {
            abort(403, 'Seul un administrateur peut modifier ou annuler une vente validée.');
        }

        $validated = $request->validated();

        // Calculate amount: (product price * quantity) - reduction
        $produit = Produit::findOrFail($validated['produit_id']);
        $reduction = $validated['reduction'] ?? 0;
        $montantCalculer = ($produit->prix * $validated['quantite']) - $reduction;

        $validated['montant'] = max(0, $montantCalculer);

        $assignableUsers = User::getAssignableUsers()->pluck('id')->toArray();
        if (isset($validated['commercial_id']) && !in_array($validated['commercial_id'], $assignableUsers)) {
            abort(403, "Vous ne pouvez pas assigner une vente à ce collaborateur.");
        }
        if (!auth()->user()->hasRole('Administrateur|Responsable Commercial|Directeur Général')) {
            $validated['commercial_id'] = auth()->id();
        }

        $vente->update($validated);

        ActivityLog::log('Modification vente', 'Ventes', "Modification de la vente ID {$vente->id}.");

        return redirect()->route('ventes.index')->with('success', 'Vente mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vente $vente)
    {
        // Restrict deletion of validated sales to Admins
        if ($vente->statut === 'Validée' && !auth()->user()->hasRole('Administrateur')) {
            abort(403, 'Seul un administrateur peut supprimer une vente validée.');
        }

        ActivityLog::log('Suppression vente', 'Ventes', "Suppression de la vente ID {$vente->id}.");

        $vente->delete();

        return redirect()->route('ventes.index')->with('success', 'Vente supprimée avec succès.');
    }
}
