<?php

namespace App\Http\Controllers;

use App\Models\Campagne;
use App\Models\Filiale;
use App\Models\ActivityLog;
use App\Http\Requests\StoreCampagneRequest;
use App\Http\Requests\UpdateCampagneRequest;

class CampagneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $campagnes = Campagne::with('filiale')->latest()->paginate(10);
        return view('campagnes.index', compact('campagnes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $filiales = Filiale::all();
        return view('campagnes.create', compact('filiales'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCampagneRequest $request)
    {
        $campagne = Campagne::create($request->validated());

        ActivityLog::log('Création campagne', 'Marketing', "Création de la campagne {$campagne->nom}.");

        return redirect()->route('campagnes.index')->with('success', 'Campagne créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Campagne $campagne)
    {
        return view('campagnes.show', compact('campagne'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Campagne $campagne)
    {
        $filiales = Filiale::all();
        return view('campagnes.edit', compact('campagne', 'filiales'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCampagneRequest $request, Campagne $campagne)
    {
        $campagne->update($request->validated());

        ActivityLog::log('Modification campagne', 'Marketing', "Modification de la campagne {$campagne->nom}.");

        return redirect()->route('campagnes.index')->with('success', 'Campagne mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Campagne $campagne)
    {
        ActivityLog::log('Suppression campagne', 'Marketing', "Suppression de la campagne {$campagne->nom}.");

        $campagne->delete();

        return redirect()->route('campagnes.index')->with('success', 'Campagne supprimée avec succès.');
    }
}
