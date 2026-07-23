<?php

namespace App\Observers;

use App\Models\Prospect;
use App\Models\Task;

class ProspectObserver
{
    /**
     * Handle the Prospect "saving" event (before create or update).
     */
    public function saving(Prospect $prospect): void
    {
        // Recalculer le score automatiquement avant sauvegarde
        $prospect->score = $prospect->calculateScore();
    }

    /**
     * Handle the Prospect "created" event.
     */
    public function created(Prospect $prospect): void
    {
        // Si assigné à un commercial dès la création, on crée une tâche initiale
        if ($prospect->commercial_id && $prospect->statut === 'Contacté' && !$prospect->is_imported) {
            Task::create([
                'user_id' => $prospect->commercial_id,
                'prospect_id' => $prospect->id,
                'titre' => 'Premier contact',
                'description' => 'Premier contact établi avec le nouveau prospect.',
                'priorite' => 'Haute',
                'date_limite' => now(),
                'statut' => 'Terminé',
            ]);
        }
    }

    /**
     * Handle the Prospect "updated" event.
     */
    public function updated(Prospect $prospect): void
    {
        // Vérifier si le statut a changé
        if ($prospect->isDirty('statut') && $prospect->commercial_id) {
            
            if ($prospect->statut === 'En négociation') {
                Task::create([
                    'user_id' => $prospect->commercial_id,
                    'prospect_id' => $prospect->id,
                    'titre' => 'Préparer proposition commerciale / Devis',
                    'description' => 'Le prospect est passé en phase de négociation. Préparez les documents commerciaux.',
                    'priorite' => 'Haute',
                    'date_limite' => now()->addDays(3),
                    'statut' => 'À faire',
                ]);
            }
        }
    }

    /**
     * Handle the Prospect "deleted" event.
     */
    public function deleted(Prospect $prospect): void
    {
        //
    }

    /**
     * Handle the Prospect "restored" event.
     */
    public function restored(Prospect $prospect): void
    {
        //
    }

    /**
     * Handle the Prospect "force deleted" event.
     */
    public function forceDeleted(Prospect $prospect): void
    {
        //
    }
}
