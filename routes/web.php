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
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProspectHistoryController;



Route::get('/dashboard', function () {
    $user = auth()->user();
    $isAdmin = $user->hasRole(['Administrateur', 'Directeur Général', 'Responsable']);

    $prospectQuery = \App\Models\Prospect::query();
    $clientQuery = \App\Models\Client::query();
    $venteQuery = \App\Models\Vente::query();

    if (!$isAdmin) {
        $prospectQuery->where('commercial_id', $user->id);
        $clientQuery->where('commercial_id', $user->id);
        $venteQuery->where('commercial_id', $user->id);
    }

    $stats = [
        'prospects_count' => $prospectQuery->count(),
        'clients_count' => $clientQuery->count(),
        'ventes_sum' => $venteQuery->sum('montant') ?? 0,
        'produits_count' => \App\Models\Produit::count(),
        'recent_prospects' => (clone $prospectQuery)->with('commercial')->latest()->take(5)->get(),
        'recent_ventes' => (clone $venteQuery)->with(['client', 'produit', 'commercial'])->latest()->take(5)->get(),
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
    Route::post('prospects/{prospect}/convert', [ProspectController::class, 'convertToClient'])->name('prospects.convert');
    Route::resource('prospects', ProspectController::class);
    Route::resource('clients', ClientController::class);
    Route::resource('ventes', VenteController::class);
    
    // Tâches et Relances
    Route::patch('tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
    Route::resource('tasks', TaskController::class);
    Route::resource('relances', RelanceController::class);

    // Entités en lecture seule (ou actions limitées)
    Route::resource('logs', ActivityLogController::class)->only(['index', 'show']);
    Route::resource('notifications', NotificationController::class)->only(['index', 'update']);
    Route::resource('prospects.histories', ProspectHistoryController::class)->only(['index']);
});
