<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Objectif;
use App\Models\User;

class ObjectifController extends Controller
{
    public function index()
    {
        $this->authorizeRoles(['Administrateur', 'Directeur Général', 'Responsable Commercial']);
        
        $commercials = User::whereDoesntHave('roles', function($q) {
            $q->where('name', 'Administrateur');
        })->get();
        
        $currentMonth = request('mois', date('Y-m'));
        
        $objectifs = Objectif::where('mois', $currentMonth)->get()->keyBy('commercial_id');

        return view('objectifs.index', compact('commercials', 'currentMonth', 'objectifs'));
    }

    public function store(Request $request)
    {
        $this->authorizeRoles(['Administrateur', 'Directeur Général', 'Responsable Commercial']);
        
        $request->validate([
            'mois' => 'required|date_format:Y-m',
            'objectifs' => 'required|array',
            'objectifs.*.montant_cible' => 'nullable|numeric|min:0'
        ]);

        foreach ($request->objectifs as $commercial_id => $data) {
            $montant = $data['montant_cible'] ?? 0;
            
            Objectif::updateOrCreate(
                ['commercial_id' => $commercial_id, 'mois' => $request->mois],
                ['montant_cible' => $montant]
            );
        }

        return redirect()->route('objectifs.index', ['mois' => $request->mois])
            ->with('success', 'Objectifs mis à jour avec succès.');
    }
    
    private function authorizeRoles($roles)
    {
        if (!auth()->user()->hasRole($roles)) {
            abort(403, 'Accès non autorisé.');
        }
    }
}
