<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Relance;

class AgendaController extends Controller
{
    /**
     * Display the agenda with tasks and relances.
     */
    public function index()
    {
        $user = auth()->user();
        
        $tasksQuery = Task::with('prospect');
        $relancesQuery = Relance::with('prospect');

        // Permissions restrictives
        if (!$user->hasRole('Administrateur|Responsable Commercial|Directeur Général')) {
            $tasksQuery->where('user_id', $user->id);
            $relancesQuery->where('commercial_id', $user->id);
        }

        $tasks = $tasksQuery->get();
        $relances = $relancesQuery->get();

        $events = [];

        // Format Tasks
        foreach ($tasks as $task) {
            if ($task->date_limite) {
                $color = match($task->priorite) {
                    'Haute' => '#ef4444', // red-500
                    'Moyenne' => '#f59e0b', // amber-500
                    'Basse' => '#3b82f6', // blue-500
                    default => '#64748b' // slate-500
                };
                
                $events[] = [
                    'id' => 'task_' . $task->id,
                    'title' => '[Tâche] ' . $task->titre,
                    'start' => $task->date_limite->format('Y-m-d'),
                    'url' => route('tasks.show', $task->id),
                    'color' => $color,
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'type' => 'Tâche',
                        'statut' => $task->statut,
                    ]
                ];
            }
        }

        // Format Relances
        foreach ($relances as $relance) {
            if ($relance->date_relance) {
                $dateTime = $relance->date_relance->format('Y-m-d') . 'T' . ($relance->heure_relance ? $relance->heure_relance . ':00' : '00:00:00');
                
                $color = match($relance->statut) {
                    'Effectuée' => '#10b981', // emerald-500
                    'Annulée' => '#94a3b8', // slate-400
                    'En attente' => '#8b5cf6', // violet-500
                    default => '#8b5cf6'
                };

                $events[] = [
                    'id' => 'relance_' . $relance->id,
                    'title' => '[Relance] ' . ($relance->prospect ? $relance->prospect->nom : 'Inconnu'),
                    'start' => $dateTime,
                    'url' => route('relances.show', $relance->id),
                    'color' => $color,
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'type' => 'Relance',
                        'statut' => $relance->statut,
                    ]
                ];
            }
        }

        return view('agenda.index', compact('events'));
    }
}
