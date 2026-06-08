<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prospect;
use App\Models\Client;
use App\Models\Source;
use App\Models\Vente;

class ReportController extends Controller
{
    public function index()
    {
        $totalProspects = Prospect::count();
        $totalClients = Client::count();
        
        $conversionRate = $totalProspects > 0 ? ($totalClients / $totalProspects) * 100 : 0;
        
        // Taux de conversion par source
        $sources = Source::withCount(['prospects', 'prospects as converted_count' => function ($query) {
            $query->whereHas('client');
        }])->get();
        
        $conversionBySource = $sources->map(function($source) {
            $rate = $source->prospects_count > 0 ? ($source->converted_count / $source->prospects_count) * 100 : 0;
            return [
                'nom' => $source->nom,
                'prospects_count' => $source->prospects_count,
                'converted_count' => $source->converted_count,
                'rate' => round($rate, 2)
            ];
        });

        // Temps moyen de conversion (en jours)
        $clientsWithProspects = Client::whereNotNull('prospect_id')->with('prospect')->get();
        $totalDays = 0;
        $countConv = 0;
        
        foreach ($clientsWithProspects as $client) {
            if ($client->prospect && $client->date_conversion) {
                $days = $client->date_conversion->diffInDays($client->prospect->created_at);
                $totalDays += $days;
                $countConv++;
            }
        }
        $avgTimeToConvert = $countConv > 0 ? round($totalDays / $countConv, 1) : 0;

        // Ventes du mois actuel vs le mois précédent
        $currentMonthSales = Vente::whereMonth('date_vente', date('m'))->whereYear('date_vente', date('Y'))->sum('montant');
        $lastMonthSales = Vente::whereMonth('date_vente', date('m', strtotime('-1 month')))->whereYear('date_vente', date('Y', strtotime('-1 month')))->sum('montant');
        
        return view('reports.index', compact(
            'totalProspects', 
            'totalClients', 
            'conversionRate', 
            'conversionBySource', 
            'avgTimeToConvert',
            'currentMonthSales',
            'lastMonthSales'
        ));
    }
}
