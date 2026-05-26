<?php

namespace App\Http\Controllers;

use App\Models\Source;
use App\Http\Requests\StoreSourceRequest;
use App\Http\Requests\UpdateSourceRequest;
use App\Models\ActivityLog;

class SourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sources = Source::latest()->paginate(10);
        return view('sources.index', compact('sources'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('sources.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSourceRequest $request)
    {
        $source = Source::create($request->validated());

        ActivityLog::log('Création source', 'Marketing', "Création de la source de prospects {$source->nom}.");

        return redirect()->route('sources.index')->with('success', 'Source de prospects créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Source $source)
    {
        return view('sources.show', compact('source'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Source $source)
    {
        return view('sources.edit', compact('source'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSourceRequest $request, Source $source)
    {
        $source->update($request->validated());

        ActivityLog::log('Modification source', 'Marketing', "Modification de la source de prospects {$source->nom}.");

        return redirect()->route('sources.index')->with('success', 'Source de prospects mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Source $source)
    {
        ActivityLog::log('Suppression source', 'Marketing', "Suppression de la source de prospects {$source->nom}.");

        $source->delete();

        return redirect()->route('sources.index')->with('success', 'Source de prospects supprimée avec succès.');
    }
}
