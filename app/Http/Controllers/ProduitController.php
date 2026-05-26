<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\Filiale;
use App\Models\ActivityLog;
use App\Http\Requests\StoreProduitRequest;
use App\Http\Requests\UpdateProduitRequest;

class ProduitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produits = Produit::with('filiale')->latest()->paginate(10);
        return view('produits.index', compact('produits'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $filiales = Filiale::all();
        return view('produits.create', compact('filiales'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProduitRequest $request)
    {
        $produit = Produit::create($request->validated());

        ActivityLog::log('Création produit', 'Catalogue', "Création du produit {$produit->nom}.");

        return redirect()->route('produits.index')->with('success', 'Produit créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Produit $produit)
    {
        return view('produits.show', compact('produit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Produit $produit)
    {
        $filiales = Filiale::all();
        return view('produits.edit', compact('produit', 'filiales'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProduitRequest $request, Produit $produit)
    {
        $produit->update($request->validated());

        ActivityLog::log('Modification produit', 'Catalogue', "Modification du produit {$produit->nom}.");

        return redirect()->route('produits.index')->with('success', 'Produit mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produit $produit)
    {
        ActivityLog::log('Suppression produit', 'Catalogue', "Suppression du produit {$produit->nom}.");

        $produit->delete();

        return redirect()->route('produits.index')->with('success', 'Produit supprimé avec succès.');
    }
}
