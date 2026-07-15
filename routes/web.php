<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FilialeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SourceController;
use App\Http\Controllers\CampagneController;
use App\Http\Controllers\PublicationController;
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
use App\Http\Controllers\DashboardController;



Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

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
    Route::resource('campagnes.publications', PublicationController::class)->only(['store', 'update', 'destroy']);
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
    
    // Rapports et Objectif
    Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::resource('objectifs', \App\Http\Controllers\ObjectifController::class)->only(['index', 'store']);
});
