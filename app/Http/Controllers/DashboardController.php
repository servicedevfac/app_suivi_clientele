<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Prospect;
use App\Models\Client;
use App\Models\Vente;
use App\Models\Relance;
use App\Models\Task;
use App\Models\Objectif;
use App\Models\ProspectHistory;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole(['Administrateur', 'Directeur Général', 'Responsable Commercial']);
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        // ──────────────────────────────────────────────
        // Base queries with role-based scoping
        // ──────────────────────────────────────────────
        $prospectQuery = Prospect::query();
        $clientQuery = Client::query();
        $venteQuery = Vente::query();
        $relanceQuery = Relance::query();
        $taskQuery = Task::query();

        if (!$isAdmin) {
            $prospectQuery->where('commercial_id', $user->id);
            $clientQuery->where('commercial_id', $user->id);
            $venteQuery->where('commercial_id', $user->id);
            $relanceQuery->where('commercial_id', $user->id);
            $taskQuery->where('user_id', $user->id);
        }

        // ──────────────────────────────────────────────
        // KPI 1: Chiffre d'affaires du mois
        // ──────────────────────────────────────────────
        $caMois = (clone $venteQuery)
            ->whereMonth('date_vente', $currentMonth)
            ->whereYear('date_vente', $currentYear)
            ->where('statut', '!=', 'Annulée')
            ->sum('montant');

        $caMoisPrecedent = (clone $venteQuery)
            ->whereMonth('date_vente', $now->copy()->subMonth()->month)
            ->whereYear('date_vente', $now->copy()->subMonth()->year)
            ->where('statut', '!=', 'Annulée')
            ->sum('montant');

        $caTendance = $caMoisPrecedent > 0
            ? round((($caMois - $caMoisPrecedent) / $caMoisPrecedent) * 100, 1)
            : ($caMois > 0 ? 100 : 0);

        // ──────────────────────────────────────────────
        // KPI 2: Objectif atteint (%)
        // ──────────────────────────────────────────────
        $objectif = Objectif::where('commercial_id', $user->id)
            ->where('mois', $now->format('Y-m'))
            ->first();

        $objectifMontant = $objectif ? $objectif->montant_cible : 0;
        $objectifPct = $objectifMontant > 0
            ? min(100, round(($caMois / $objectifMontant) * 100, 1))
            : 0;

        // ──────────────────────────────────────────────
        // KPI 3: Prospects gérés (ce mois)
        // ──────────────────────────────────────────────
        $prospectsCount = (clone $prospectQuery)->count();

        $prospectsMois = (clone $prospectQuery)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        $prospectsMoisPrec = (clone $prospectQuery)
            ->whereMonth('created_at', $now->copy()->subMonth()->month)
            ->whereYear('created_at', $now->copy()->subMonth()->year)
            ->count();

        $prospectsTendance = $prospectsMoisPrec > 0
            ? round((($prospectsMois - $prospectsMoisPrec) / $prospectsMoisPrec) * 100, 1)
            : ($prospectsMois > 0 ? 100 : 0);

        // ──────────────────────────────────────────────
        // KPI 4: Taux de conversion
        // ──────────────────────────────────────────────
        $totalProspects = (clone $prospectQuery)->count();
        $totalClients = (clone $clientQuery)->count();
        $tauxConversion = $totalProspects > 0
            ? round(($totalClients / $totalProspects) * 100, 1)
            : 0;

        // Taux mois précédent pour tendance
        $prospectsPrecAll = (clone $prospectQuery)
            ->where('created_at', '<', $now->copy()->startOfMonth())
            ->count();
        $clientsPrecAll = (clone $clientQuery)
            ->where('created_at', '<', $now->copy()->startOfMonth())
            ->count();
        $tauxConvPrec = $prospectsPrecAll > 0
            ? round(($clientsPrecAll / $prospectsPrecAll) * 100, 1)
            : 0;
        $conversionTendance = $tauxConvPrec > 0
            ? round($tauxConversion - $tauxConvPrec, 1)
            : 0;

        // ──────────────────────────────────────────────
        // KPI 5: Appels effectués (ce mois)
        // ──────────────────────────────────────────────
        $appelsMois = (clone $relanceQuery)
            ->where('canal', 'Appel')
            ->where('statut', 'Réalisée')
            ->whereMonth('date_relance', $currentMonth)
            ->whereYear('date_relance', $currentYear)
            ->count();

        $appelsMoisPrec = (clone $relanceQuery)
            ->where('canal', 'Appel')
            ->where('statut', 'Réalisée')
            ->whereMonth('date_relance', $now->copy()->subMonth()->month)
            ->whereYear('date_relance', $now->copy()->subMonth()->year)
            ->count();

        $appelsTendance = $appelsMoisPrec > 0
            ? round((($appelsMois - $appelsMoisPrec) / $appelsMoisPrec) * 100, 1)
            : ($appelsMois > 0 ? 100 : 0);

        // ──────────────────────────────────────────────
        // KPI 6: Rendez-vous réalisés (ce mois)
        // ──────────────────────────────────────────────
        $rdvMois = (clone $relanceQuery)
            ->where('canal', 'Rendez-vous')
            ->where('statut', 'Réalisée')
            ->whereMonth('date_relance', $currentMonth)
            ->whereYear('date_relance', $currentYear)
            ->count();

        $rdvMoisPrec = (clone $relanceQuery)
            ->where('canal', 'Rendez-vous')
            ->where('statut', 'Réalisée')
            ->whereMonth('date_relance', $now->copy()->subMonth()->month)
            ->whereYear('date_relance', $now->copy()->subMonth()->year)
            ->count();

        $rdvTendance = $rdvMoisPrec > 0
            ? round((($rdvMois - $rdvMoisPrec) / $rdvMoisPrec) * 100, 1)
            : ($rdvMois > 0 ? 100 : 0);

        // ──────────────────────────────────────────────
        // KPI 7: Devis envoyés (prospects en négociation ce mois)
        // ──────────────────────────────────────────────
        $devisMois = (clone $prospectQuery)
            ->where('statut', 'En négociation')
            ->count();

        // ──────────────────────────────────────────────
        // KPI 8: Ventes conclues (ce mois)
        // ──────────────────────────────────────────────
        $ventesConclues = (clone $venteQuery)
            ->where('statut', 'Validée')
            ->whereMonth('date_vente', $currentMonth)
            ->whereYear('date_vente', $currentYear)
            ->count();

        $ventesConcluesPrec = (clone $venteQuery)
            ->where('statut', 'Validée')
            ->whereMonth('date_vente', $now->copy()->subMonth()->month)
            ->whereYear('date_vente', $now->copy()->subMonth()->year)
            ->count();

        $ventesTendance = $ventesConcluesPrec > 0
            ? round((($ventesConclues - $ventesConcluesPrec) / $ventesConcluesPrec) * 100, 1)
            : ($ventesConclues > 0 ? 100 : 0);

        // ──────────────────────────────────────────────
        // KPI 9: Satisfaction client (taux de rétention)
        // ──────────────────────────────────────────────
        $clientsActifs = (clone $clientQuery)->where('statut', 'Actif')->count();
        $clientsTotal = (clone $clientQuery)->count();
        $satisfaction = $clientsTotal > 0
            ? round(($clientsActifs / $clientsTotal) * 100, 1)
            : 0;

        // ──────────────────────────────────────────────
        // Chart: Évolution mensuelle des performances (6 mois)
        // ──────────────────────────────────────────────
        $evolutionLabels = [];
        $evolutionCA = [];
        $evolutionProspects = [];
        $evolutionConversions = [];
        $evolutionAppels = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $m = $date->month;
            $y = $date->year;
            $evolutionLabels[] = $date->translatedFormat('M Y');

            $evolutionCA[] = (clone $venteQuery)
                ->whereMonth('date_vente', $m)
                ->whereYear('date_vente', $y)
                ->where('statut', '!=', 'Annulée')
                ->sum('montant');

            $evolutionProspects[] = (clone $prospectQuery)
                ->whereMonth('created_at', $m)
                ->whereYear('created_at', $y)
                ->count();

            $evolutionConversions[] = (clone $clientQuery)
                ->whereMonth('date_conversion', $m)
                ->whereYear('date_conversion', $y)
                ->count();

            $evolutionAppels[] = (clone $relanceQuery)
                ->where('canal', 'Appel')
                ->where('statut', 'Réalisée')
                ->whereMonth('date_relance', $m)
                ->whereYear('date_relance', $y)
                ->count();
        }

        // ──────────────────────────────────────────────
        // Classement des commerciaux (qualité du suivi)
        // Only for admin/responsable views
        // ──────────────────────────────────────────────
        $classement = [];
        if ($isAdmin) {
            $commerciaux = User::whereHas('prospects')->get();

            foreach ($commerciaux as $commercial) {
                $scores = $this->calculateQualityScore($commercial);
                $classement[] = [
                    'user' => $commercial,
                    'score_global' => $scores['global'],
                    'relance_score' => $scores['relance'],
                    'conversion_score' => $scores['conversion'],
                    'task_score' => $scores['task'],
                    'interaction_score' => $scores['interaction'],
                    'completude_score' => $scores['completude'],
                ];
            }

            // Trier par score global décroissant
            usort($classement, fn($a, $b) => $b['score_global'] <=> $a['score_global']);
        }

        // ──────────────────────────────────────────────
        // Prospects par statut (pie chart)
        // ──────────────────────────────────────────────
        $prospects = (clone $prospectQuery)->get()->groupBy('statut');
        $prospectsLabels = $prospects->keys()->map(fn($s) => ucfirst($s));
        $prospectsData = $prospects->map(fn($group) => $group->count())->values();

        // ──────────────────────────────────────────────
        // Derniers prospects et ventes
        // ──────────────────────────────────────────────
        $recentProspects = (clone $prospectQuery)->with('commercial')->latest()->take(5)->get();
        $recentVentes = (clone $venteQuery)->with(['client', 'produit', 'commercial'])->latest()->take(5)->get();

        // ──────────────────────────────────────────────
        // Compile all stats
        // ──────────────────────────────────────────────
        $stats = [
            // KPI Cards
            'ca_mois' => $caMois,
            'ca_tendance' => $caTendance,
            'objectif_pct' => $objectifPct,
            'objectif_montant' => $objectifMontant,
            'prospects_count' => $prospectsCount,
            'prospects_mois' => $prospectsMois,
            'prospects_tendance' => $prospectsTendance,
            'taux_conversion' => $tauxConversion,
            'conversion_tendance' => $conversionTendance,
            'appels_mois' => $appelsMois,
            'appels_tendance' => $appelsTendance,
            'rdv_mois' => $rdvMois,
            'rdv_tendance' => $rdvTendance,
            'devis_mois' => $devisMois,
            'ventes_conclues' => $ventesConclues,
            'ventes_tendance' => $ventesTendance,
            'satisfaction' => $satisfaction,
            'clients_actifs' => $clientsActifs,
            'clients_total' => $clientsTotal,

            // Charts
            'evolution_labels' => $evolutionLabels,
            'evolution_ca' => $evolutionCA,
            'evolution_prospects' => $evolutionProspects,
            'evolution_conversions' => $evolutionConversions,
            'evolution_appels' => $evolutionAppels,

            // Pie chart
            'chart_prospects_labels' => $prospectsLabels,
            'chart_prospects_data' => $prospectsData,

            // Tables
            'recent_prospects' => $recentProspects,
            'recent_ventes' => $recentVentes,

            // Classement
            'classement' => $classement,
        ];

        return view('dashboard', compact('stats'));
    }

    /**
     * Calcule le score de qualité du suivi pour un commercial.
     *
     * Critères pondérés :
     * - Relances à temps    : 30%
     * - Taux de conversion  : 25%
     * - Tâches complétées   : 20%
     * - Régularité interactions : 15%
     * - Complétude profils  : 10%
     */
    private function calculateQualityScore(User $commercial): array
    {
        // 1. Taux de relances effectuées à temps (30%)
        $totalRelances = Relance::where('commercial_id', $commercial->id)->count();
        $relancesATemps = Relance::where('commercial_id', $commercial->id)
            ->where('statut', 'Réalisée')
            ->count();
        $relanceScore = $totalRelances > 0
            ? round(($relancesATemps / $totalRelances) * 100, 1)
            : 0;

        // 2. Taux de conversion (25%)
        $totalProspects = Prospect::where('commercial_id', $commercial->id)->count();
        $convertis = Client::where('commercial_id', $commercial->id)->count();
        $conversionScore = $totalProspects > 0
            ? round(($convertis / $totalProspects) * 100, 1)
            : 0;

        // 3. Taux de tâches complétées (20%)
        $totalTasks = Task::where('user_id', $commercial->id)->count();
        $tasksTerminees = Task::where('user_id', $commercial->id)
            ->where('statut', 'Terminé')
            ->count();
        $taskScore = $totalTasks > 0
            ? round(($tasksTerminees / $totalTasks) * 100, 1)
            : 0;

        // 4. Régularité des interactions (15%)
        // Nombre moyen d'interactions (prospect_histories) par prospect
        $totalHistories = ProspectHistory::where('user_id', $commercial->id)->count();
        $interactionScore = $totalProspects > 0
            ? min(100, round(($totalHistories / $totalProspects) * 20, 1))
            : 0;

        // 5. Complétude des profils prospects (10%)
        $prospects = Prospect::where('commercial_id', $commercial->id)->get();
        $completudeTotal = 0;
        $fieldsToCheck = ['email', 'telephone', 'entreprise', 'besoin', 'commentaire'];

        foreach ($prospects as $prospect) {
            $filled = 0;
            foreach ($fieldsToCheck as $field) {
                if (!empty($prospect->$field)) {
                    $filled++;
                }
            }
            $completudeTotal += ($filled / count($fieldsToCheck)) * 100;
        }
        $completudeScore = $totalProspects > 0
            ? round($completudeTotal / $totalProspects, 1)
            : 0;

        // Score global pondéré
        $global = round(
            ($relanceScore * 0.30) +
            ($conversionScore * 0.25) +
            ($taskScore * 0.20) +
            ($interactionScore * 0.15) +
            ($completudeScore * 0.10),
            1
        );

        return [
            'global' => $global,
            'relance' => $relanceScore,
            'conversion' => min($conversionScore, 100),
            'task' => $taskScore,
            'interaction' => $interactionScore,
            'completude' => $completudeScore,
        ];
    }
}
