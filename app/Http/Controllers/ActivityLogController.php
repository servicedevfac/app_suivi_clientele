<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Restriction d'accès stricte : Administrateur uniquement
        if (!auth()->user()->hasRole('Administrateur')) {
            abort(403, 'Accès interdit. Seul un Administrateur peut consulter les journaux d\'audit.');
        }

        $logs = ActivityLog::with(['user.roles'])->latest()->paginate(20);

        return view('logs.index', compact('logs'));
    }

    /**
     * Display the specified resource.
     */
    public function show(ActivityLog $log)
    {
        if (!auth()->user()->hasRole('Administrateur')) {
            abort(403, 'Accès interdit.');
        }

        return view('logs.show', compact('log'));
    }
}
