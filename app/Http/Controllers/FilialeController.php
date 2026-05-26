<?php

namespace App\Http\Controllers;

use App\Models\Filiale;
use App\Http\Requests\StoreFilialeRequest;
use App\Http\Requests\UpdateFilialeRequest;
use App\Models\ActivityLog;

class FilialeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $filiales = Filiale::latest()->paginate(10);
        return view('filiales.index', compact('filiales'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('filiales.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFilialeRequest $request)
    {
        $filiale = Filiale::create($request->validated());

        ActivityLog::log('Création filiale', 'Configuration', "Création de la filiale {$filiale->nom}.");

        return redirect()->route('filiales.index')->with('success', 'Filiale créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Filiale $filiale)
    {
        return view('filiales.show', compact('filiale'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Filiale $filiale)
    {
        return view('filiales.edit', compact('filiale'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFilialeRequest $request, Filiale $filiale)
    {
        $filiale->update($request->validated());

        ActivityLog::log('Modification filiale', 'Configuration', "Modification de la filiale {$filiale->nom}.");

        return redirect()->route('filiales.index')->with('success', 'Filiale mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Filiale $filiale)
    {
        ActivityLog::log('Suppression filiale', 'Configuration', "Suppression de la filiale {$filiale->nom}.");

        $filiale->delete();

        return redirect()->route('filiales.index')->with('success', 'Filiale supprimée avec succès.');
    }
}
