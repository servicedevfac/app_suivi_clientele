<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ProspectController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\AgendaController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\RelanceController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'profile']);

    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/prospects/form-options', [ProspectController::class, 'formOptions']);
    Route::apiResource('prospects', ProspectController::class);
    Route::post('/prospects/{prospect}/convert', [ProspectController::class, 'convertToClient']);
    Route::apiResource('clients', ClientController::class)->only(['index', 'show']);
    
    Route::apiResource('tasks', TaskController::class);
    Route::apiResource('relances', RelanceController::class);
    
    // Legacy Agenda endpoint if needed
    Route::get('/agenda', [AgendaController::class, 'index']);
    Route::put('/agenda/{relance}/statut', [AgendaController::class, 'updateStatus']);
});
