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
        $campagne->load(['publications.prospects.client.ventes', 'prospects.publication', 'prospects.commercial', 'prospects.client.ventes']);

        // Calcul des statistiques agrégées par Canal / Moyen de communication
        $publications = $campagne->publications;
        $statsParCanal = collect();

        // 1. Groupement des publications par canal
        $grouped = $publications->groupBy('canal');
        foreach ($grouped as $canal => $pubs) {
            $prospectsCount = $pubs->sum(function ($pub) {
                return $pub->prospects->count();
            });
            $conversionsCount = $pubs->sum('conversions_count');
            $chiffreAffaires = $pubs->sum('chiffre_affaires');
            $budgetCanal = $pubs->sum('budget');
            $tauxConversion = $prospectsCount > 0 ? round(($conversionsCount / $prospectsCount) * 100, 1) : 0;

            $statsParCanal->push((object)[
                'canal' => $canal,
                'nombre_publications' => $pubs->count(),
                'prospects_count' => $prospectsCount,
                'conversions_count' => $conversionsCount,
                'chiffre_affaires' => $chiffreAffaires,
                'budget' => $budgetCanal,
                'taux_conversion' => $tauxConversion,
            ]);
        }

        // 2. Gestion des prospects de la campagne sans publication attribuée
        $prospectsNonAttribues = $campagne->prospects->whereNull('publication_id');
        if ($prospectsNonAttribues->count() > 0) {
            $prospectsCount = $prospectsNonAttribues->count();
            $conversionsCount = $prospectsNonAttribues->filter(fn($p) => $p->client !== null)->count();
            $chiffreAffaires = $prospectsNonAttribues->sum(fn($p) => $p->client ? $p->client->ventes->sum('montant') : 0);
            $tauxConversion = $prospectsCount > 0 ? round(($conversionsCount / $prospectsCount) * 100, 1) : 0;

            $statsParCanal->push((object)[
                'canal' => 'Non attribué / Direct',
                'nombre_publications' => 0,
                'prospects_count' => $prospectsCount,
                'conversions_count' => $conversionsCount,
                'chiffre_affaires' => $chiffreAffaires,
                'budget' => 0,
                'taux_conversion' => $tauxConversion,
            ]);
        }

        // Tri par nombre de prospects par défaut
        $statsParCanal = $statsParCanal->sortByDesc('prospects_count')->values();

        // Détermination du meilleur canal
        $canauxExplicites = $statsParCanal->where('nombre_publications', '>', 0);
        $meilleurCanalProspects = $canauxExplicites->sortByDesc('prospects_count')->first() ?? $statsParCanal->first();
        $meilleurCanalCA = $canauxExplicites->sortByDesc('chiffre_affaires')->first() ?? $statsParCanal->sortByDesc('chiffre_affaires')->first();

        return view('campagnes.show', compact('campagne', 'statsParCanal', 'meilleurCanalProspects', 'meilleurCanalCA'));
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
