<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prospect;
use App\Models\Client;
use App\Models\Relance;
use App\Models\Objectif;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $isCommercial = $user->hasRole('Commercial');
        
        $queryProspects = Prospect::query();
        $queryClients = Client::query();
        $queryRelances = Relance::query();
        
        if ($isCommercial) {
            $queryProspects->where('commercial_id', $user->id);
            $queryClients->whereHas('prospect', function ($q) use ($user) {
                $q->where('commercial_id', $user->id);
            });
            $queryRelances->where('commercial_id', $user->id);
        }

        $stats = [
            'total_prospects' => $queryProspects->count(),
            'total_clients' => $queryClients->count(),
            'nouveaux_prospects_mois' => (clone $queryProspects)->whereMonth('created_at', Carbon::now()->month)->count(),
            'taux_conversion' => $this->calculateConversionRate($isCommercial ? $user->id : null),
        ];

        $relances_du_jour = (clone $queryRelances)
            ->whereDate('date_relance', Carbon::today())
            ->where('statut', 'En attente')
            ->with('prospect')
            ->get();

        $objectifs = [];
        if ($isCommercial) {
            $objectifs = Objectif::where('commercial_id', $user->id)
                ->where('mois', Carbon::now()->startOfMonth()->format('Y-m-d'))
                ->first();
        }

        return response()->json([
            'stats' => $stats,
            'relances_du_jour' => $relances_du_jour,
            'objectifs' => $objectifs
        ]);
    }

    private function calculateConversionRate($commercialId = null)
    {
        $query = Prospect::query();
        if ($commercialId) {
            $query->where('commercial_id', $commercialId);
        }
        
        $total = $query->count();
        if ($total === 0) return 0;
        
        $converted = (clone $query)->where('statut', 'Gagné')->count();
        return round(($converted / $total) * 100, 1);
    }
}
