<?php

namespace App\Http\Controllers;

use App\Models\Relance;
use App\Http\Requests\StoreRelanceRequest;
use App\Http\Requests\UpdateRelanceRequest;

class RelanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Relance::with(['prospect', 'commercial']);

        // Restrictions par rôle
        if (!auth()->user()->hasRole('Administrateur')) {
            $query->where('commercial_id', auth()->id());
        } elseif (request()->filled('commercial_id')) {
            $query->where('commercial_id', request('commercial_id'));
        }

        // Filtres
        if (request()->filled('statut')) {
            $query->where('statut', request('statut'));
        }
        if (request()->filled('canal')) {
            $query->where('canal', request('canal'));
        }
        if (request()->filled('prospect_id')) {
            $query->where('prospect_id', request('prospect_id'));
        }

        // Planification spécifique (Relances du jour, à venir, en retard)
        if (request('filter') === 'today') {
            $query->whereDate('date_relance', today());
        } elseif (request('filter') === 'upcoming') {
            $query->whereDate('date_relance', '>', today());
        } elseif (request('filter') === 'overdue') {
            $query->whereDate('date_relance', '<', today())->where('statut', 'En attente');
        }

        $relances = $query->orderBy('date_relance', 'asc')->orderBy('heure_relance', 'asc')->paginate(15);
        $commercials = \App\Models\User::all();
        $prospects = auth()->user()->hasRole('Administrateur') 
            ? \App\Models\Prospect::all() 
            : \App\Models\Prospect::where('commercial_id', auth()->id())->get();

        return view('relances.index', compact('relances', 'commercials', 'prospects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $commercials = \App\Models\User::all();
        $prospects = auth()->user()->hasRole('Administrateur') 
            ? \App\Models\Prospect::all() 
            : \App\Models\Prospect::where('commercial_id', auth()->id())->get();

        return view('relances.create', compact('commercials', 'prospects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRelanceRequest $request)
    {
        $data = $request->validated();

        if (!auth()->user()->hasRole('Administrateur')) {
            $data['commercial_id'] = auth()->id();
        }

        $relance = Relance::create($data);

        if ($relance->commercial_id && $relance->commercial_id !== auth()->id()) {
            $prospectNom = $relance->prospect ? "{$relance->prospect->nom} {$relance->prospect->prenom}" : "Inconnu";
            \App\Models\Notification::create([
                'user_id' => $relance->commercial_id,
                'titre' => 'Nouvelle relance assignée',
                'message' => "Une relance pour le prospect {$prospectNom} vous a été assignée par " . auth()->user()->name . ".",
                'type' => 'info',
            ]);
        }

        \App\Models\ActivityLog::log(
            'Planification relance',
            'Relances',
            "Planification d'une relance par " . ($relance->canal ?? 'Canal non spécifié') . " pour le prospect " . ($relance->prospect ? "{$relance->prospect->nom} {$relance->prospect->prenom}" : "Inconnu")
        );

        return redirect()->route('relances.index')->with('success', 'Relance planifiée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Relance $relance)
    {
        if (!auth()->user()->hasRole('Administrateur') && $relance->commercial_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à voir cette relance.');
        }

        return view('relances.show', compact('relance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Relance $relance)
    {
        if (!auth()->user()->hasRole('Administrateur') && $relance->commercial_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier cette relance.');
        }

        $commercials = \App\Models\User::all();
        $prospects = auth()->user()->hasRole('Administrateur') 
            ? \App\Models\Prospect::all() 
            : \App\Models\Prospect::where('commercial_id', auth()->id())->get();

        return view('relances.edit', compact('relance', 'commercials', 'prospects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRelanceRequest $request, Relance $relance)
    {
        if (!auth()->user()->hasRole('Administrateur') && $relance->commercial_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier cette relance.');
        }

        $data = $request->validated();

        if (!auth()->user()->hasRole('Administrateur')) {
            $data['commercial_id'] = auth()->id();
        }

        $oldCommercialId = $relance->commercial_id;
        $relance->update($data);

        if ($relance->commercial_id && $relance->commercial_id !== $oldCommercialId && $relance->commercial_id !== auth()->id()) {
            $prospectNom = $relance->prospect ? "{$relance->prospect->nom} {$relance->prospect->prenom}" : "Inconnu";
            \App\Models\Notification::create([
                'user_id' => $relance->commercial_id,
                'titre' => 'Relance réassignée',
                'message' => "Une relance pour le prospect {$prospectNom} vous a été réassignée par " . auth()->user()->name . ".",
                'type' => 'info',
            ]);
        }

        \App\Models\ActivityLog::log(
            'Modification relance',
            'Relances',
            "Modification de la relance pour le prospect " . ($relance->prospect ? "{$relance->prospect->nom} {$relance->prospect->prenom}" : "Inconnu")
        );

        return redirect()->route('relances.index')->with('success', 'Relance mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Relance $relance)
    {
        if (!auth()->user()->hasRole('Administrateur') && $relance->commercial_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à supprimer cette relance.');
        }

        $nomProspect = $relance->prospect ? "{$relance->prospect->nom} {$relance->prospect->prenom}" : "Inconnu";
        $relance->delete();

        \App\Models\ActivityLog::log(
            'Suppression relance',
            'Relances',
            "Suppression de la relance pour le prospect {$nomProspect}"
        );

        return redirect()->route('relances.index')->with('success', 'Relance supprimée avec succès.');
    }
}
