<?php

namespace App\Http\Controllers;

use App\Models\Campagne;
use App\Models\Publication;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class PublicationController extends Controller
{
    /**
     * Store a newly created publication for a campaign.
     */
    public function store(Request $request, Campagne $campagne)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'canal' => 'required|string|max:255',
            'url_support' => 'nullable|url|max:500',
            'budget' => 'nullable|numeric|min:0',
            'date_publication' => 'nullable|date',
            'statut' => 'nullable|string|max:50',
        ]);

        if (!isset($validated['statut']) || empty($validated['statut'])) {
            $validated['statut'] = 'active';
        }

        $publication = $campagne->publications()->create($validated);

        ActivityLog::log('Création publication', 'Marketing', "Ajout de la publication '{$publication->titre}' ({$publication->canal}) sur la campagne '{$campagne->nom}'.");

        return redirect()->route('campagnes.show', $campagne)->with('success', 'Publication / moyen de communication ajouté avec succès.');
    }

    /**
     * Update the specified publication.
     */
    public function update(Request $request, Campagne $campagne, Publication $publication)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'canal' => 'required|string|max:255',
            'url_support' => 'nullable|url|max:500',
            'budget' => 'nullable|numeric|min:0',
            'date_publication' => 'nullable|date',
            'statut' => 'nullable|string|max:50',
        ]);

        if (!isset($validated['statut']) || empty($validated['statut'])) {
            $validated['statut'] = 'active';
        }

        $publication->update($validated);

        ActivityLog::log('Modification publication', 'Marketing', "Modification de la publication '{$publication->titre}' sur la campagne '{$campagne->nom}'.");

        return redirect()->route('campagnes.show', $campagne)->with('success', 'Publication / moyen de communication mis à jour avec succès.');
    }

    /**
     * Remove the specified publication from storage.
     */
    public function destroy(Campagne $campagne, Publication $publication)
    {
        $titre = $publication->titre;
        $publication->delete();

        ActivityLog::log('Suppression publication', 'Marketing', "Suppression de la publication '{$titre}' sur la campagne '{$campagne->nom}'.");

        return redirect()->route('campagnes.show', $campagne)->with('success', 'Publication supprimée avec succès.');
    }
}
