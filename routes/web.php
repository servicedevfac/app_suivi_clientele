<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FilialeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SourceController;
use App\Http\Controllers\CampagneController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\ProspectController;
use App\Http\Controllers\RelanceController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProspectHistoryController;



Route::get('/dashboard', function () {
    $user = auth()->user();
    $isAdmin = $user->hasRole(['Administrateur', 'Directeur Général', 'Responsable Commercial']);

    $prospectQuery = \App\Models\Prospect::query();
    $clientQuery = \App\Models\Client::query();
    $venteQuery = \App\Models\Vente::query();

    if (!$isAdmin) {
        $prospectQuery->where('commercial_id', $user->id);
        $clientQuery->where('commercial_id', $user->id);
        $venteQuery->where('commercial_id', $user->id);
    }

    $ventes = (clone $venteQuery)
        ->where(function($q) {
            $q->where('date_vente', '>=', now()->subMonths(5)->startOfMonth())
              ->orWhere(function($q2) {
                  $q2->whereNull('date_vente')
                     ->where('created_at', '>=', now()->subMonths(5)->startOfMonth());
              });
        })
        ->get()
        ->groupBy(function($vente) {
            $date = $vente->date_vente ?? $vente->created_at;
            return $date ? $date->format('m/Y') : now()->format('m/Y');
        });

    $ventesLabels = $ventes->keys();
    $ventesData = $ventes->map(fn($group) => $group->sum('montant'))->values();

    $prospects = (clone $prospectQuery)->get()->groupBy('statut');
    $prospectsLabels = $prospects->keys()->map(fn($s) => ucfirst($s));
    $prospectsData = $prospects->map(fn($group) => $group->count())->values();

    $objectif = \App\Models\Objectif::where('commercial_id', $user->id)
        ->where('mois', now()->format('Y-m'))
        ->first();
        
    $caPrevisionnel = (clone $prospectQuery)->whereNotIn('statut', ['Gagné', 'Perdu'])
        ->get()
        ->sum(function($p) {
            return ($p->montant_estime ?? 0) * (($p->probabilite ?? 0) / 100);
        });

    $ventesMoisSum = (clone $venteQuery)
        ->whereMonth('date_vente', now()->month)
        ->whereYear('date_vente', now()->year)
        ->sum('montant');

    $stats = [
        'prospects_count' => $prospectQuery->count(),
        'clients_count' => $clientQuery->count(),
        'ventes_sum' => $venteQuery->sum('montant') ?? 0,
        'ventes_mois_sum' => $ventesMoisSum ?? 0,
        'produits_count' => \App\Models\Produit::count(),
        'objectif_mois' => $objectif ? $objectif->montant_cible : null,
        'ca_previsionnel' => $caPrevisionnel,
        'recent_prospects' => (clone $prospectQuery)->with('commercial')->latest()->take(5)->get(),
        'recent_ventes' => (clone $venteQuery)->with(['client', 'produit', 'commercial'])->latest()->take(5)->get(),
        'chart_ventes_labels' => $ventesLabels,
        'chart_ventes_data' => $ventesData,
        'chart_prospects_labels' => $prospectsLabels,
        'chart_prospects_data' => $prospectsData,
    ];
    return view('dashboard', compact('stats'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Toutes les routes CRM sont protégées par le middleware d'authentification et la vérification du statut actif
Route::middleware(['auth', 'user.status'])->group(function () {
    
    // Entités de configuration / Admin
    Route::resource('filiales', FilialeController::class);
    Route::resource('users', UserController::class)->middleware('role:Administrateur|Directeur Général');
    // Entités Catalogue & Marketing
    Route::resource('sources', SourceController::class);
    Route::resource('campagnes', CampagneController::class);
    Route::resource('produits', ProduitController::class);
    
    // Coeur du CRM
    Route::patch('prospects/{prospect}/status', [ProspectController::class, 'updateStatus'])->name('prospects.update-status');
    Route::post('prospects/{prospect}/convert', [ProspectController::class, 'convertToClient'])->name('prospects.convert');
    Route::post('prospects/{prospect}/documents', [ProspectController::class, 'uploadDocument'])->name('prospects.documents.store');
    Route::post('prospects/{prospect}/email', [ProspectController::class, 'sendEmail'])->name('prospects.email.send');
    Route::get('prospects/export', [ProspectController::class, 'export'])->name('prospects.export');
    Route::post('prospects/import', [ProspectController::class, 'import'])->name('prospects.import');
    Route::resource('prospects', ProspectController::class);
    Route::resource('clients', ClientController::class);
    Route::resource('ventes', VenteController::class);
    
    // Tâches et Relances
    Route::patch('tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
    Route::resource('tasks', TaskController::class);
    Route::resource('relances', RelanceController::class);
    Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');

    // Entités en lecture seule (ou actions limitées)
    Route::resource('logs', ActivityLogController::class)->only(['index', 'show']);
    Route::resource('notifications', NotificationController::class)->only(['index', 'update']);
    Route::resource('prospects.histories', ProspectHistoryController::class)->only(['index']);
    
    // Rapports et Objectifs
    Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::resource('objectifs', \App\Http\Controllers\ObjectifController::class)->only(['index', 'store']);
});
