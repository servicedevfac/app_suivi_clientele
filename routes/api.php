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
use Faker\Guesser\Name;
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'profile']);

    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/prospect/form-options', [ProspectController::class, 'formOptions']);
    Route::apiResource('prospect', ProspectController::class);
    Route::post('/prospect/{prospect}/convert', [ProspectController::class, 'convertToClient']);
    Route::apiResource('client', ClientController::class)->only(['index', 'show']);
    
    Route::apiResource('task', TaskController::class);
    Route::apiResource('relance', RelanceController::class);
    
    Route::get('/agendas', [AgendaController::class, 'index']);
    Route::put('/agendas/{relance}/statut', [AgendaController::class, 'updateStatus']);
});
