<?php

namespace App\Http\Controllers;

use App\Models\Prospect;
use App\Models\User;
use App\Models\Filiale;
use App\Models\Source;
use App\Models\Campagne;
use App\Models\ProspectHistory;
use App\Models\ActivityLog;
use App\Models\Client;
use App\Mail\ProspectEmail;
use App\Http\Requests\StoreProspectRequest;
use App\Http\Requests\UpdateProspectRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ProspectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Prospect::with(['commercial', 'filiale', 'source', 'campagne']);

        if (!auth()->user()->hasRole('Administrateur|Responsable Commercial|Directeur Général')) {
            $query->where('commercial_id', auth()->id());
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('filiale_id')) {
            $query->where('filiale_id', $request->filiale_id);
        }

        if ($request->filled('commercial_id')) {
            $query->where('commercial_id', $request->commercial_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%")
                  ->orWhere('entreprise', 'like', "%{$search}%");
            });
        }

        if ($request->view === 'kanban') {
            $prospects = $query->latest()->get();
        } else {
            $prospects = $query->latest()->paginate(10)->withQueryString();
        }

        $filiales = Filiale::all();
        $commercials = User::getAssignableUsers();

        return view('prospects.index', compact('prospects', 'filiales', 'commercials'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $commercials = User::getAssignableUsers();
        $filiales = Filiale::all();
        $sources = Source::all();
        $campagnes = Campagne::all();

        return view('prospects.create', compact('commercials', 'filiales', 'sources', 'campagnes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProspectRequest $request)
    {
        $validated = $request->validated();

        $assignableUsers = User::getAssignableUsers()->pluck('id')->toArray();
        if (isset($validated['commercial_id']) && !in_array($validated['commercial_id'], $assignableUsers)) {
            abort(403, "Vous ne pouvez pas assigner un prospect à ce collaborateur.");
        }

        if (!auth()->user()->hasRole('Administrateur|Responsable Commercial|Directeur Général')) {
            $validated['commercial_id'] = auth()->id();
        }

        if (isset($validated['tags']) && is_string($validated['tags'])) {
            $tagsArray = array_filter(array_map('trim', explode(',', $validated['tags'])));
            $validated['tags'] = array_values($tagsArray);
        } else {
            $validated['tags'] = [];
        }

        $prospect = DB::transaction(function () use ($validated) {
            $prospect = Prospect::create($validated);

            // Add history entry
            $prospect->histories()->create([
                'user_id' => auth()->id(),
                'action' => 'Création',
                'description' => 'Création initiale du prospect.',
                'ancien_statut' => null,
                'nouveau_statut' => $prospect->statut,
            ]);

            return $prospect;
        });

        ActivityLog::log('Création prospect', 'Prospects', "Création du prospect {$prospect->nom} {$prospect->prenom}.");

        return redirect()->route('prospects.index')->with('success', 'Prospect créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Prospect $prospect)
    {
        $prospect->load(['commercial', 'filiale', 'source', 'campagne', 'histories.user', 'relances', 'tasks.user', 'documents']);

        return view('prospects.show', compact('prospect'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Prospect $prospect)
    {
        $commercials = User::getAssignableUsers();
        $filiales = Filiale::all();
        $sources = Source::all();
        $campagnes = Campagne::all();

        return view('prospects.edit', compact('prospect', 'commercials', 'filiales', 'sources', 'campagnes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProspectRequest $request, Prospect $prospect)
    {
        $validated = $request->validated();
        
        $assignableUsers = User::getAssignableUsers()->pluck('id')->toArray();
        if (isset($validated['commercial_id']) && !in_array($validated['commercial_id'], $assignableUsers)) {
            abort(403, "Vous ne pouvez pas assigner un prospect à ce collaborateur.");
        }

        if (!auth()->user()->hasRole('Administrateur|Responsable Commercial|Directeur Général')) {
            $validated['commercial_id'] = auth()->id();
        }

        if (isset($validated['tags']) && is_string($validated['tags'])) {
            $tagsArray = array_filter(array_map('trim', explode(',', $validated['tags'])));
            $validated['tags'] = array_values($tagsArray);
        } elseif (!isset($validated['tags'])) {
            $validated['tags'] = [];
        }

        $ancienStatut = $prospect->statut;
        $nouveauStatut = $validated['statut'] ?? $ancienStatut;

        if (in_array($ancienStatut, ['Gagné', 'Perdu']) && $ancienStatut !== $nouveauStatut) {
            return redirect()->back()->with('error', 'Impossible de modifier le statut d\'un prospect déjà ' . $ancienStatut . '.');
        }

        DB::transaction(function () use ($prospect, $validated, $ancienStatut, $nouveauStatut) {
            $prospect->update($validated);

            if ($ancienStatut !== $nouveauStatut) {
                $prospect->histories()->create([
                    'user_id' => auth()->id(),
                    'action' => 'Changement statut',
                    'description' => "Le statut du prospect a été modifié de '{$ancienStatut}' à '{$nouveauStatut}'.",
                    'ancien_statut' => $ancienStatut,
                    'nouveau_statut' => $nouveauStatut,
                ]);

                ActivityLog::log('Changement statut prospect', 'Prospects', "Statut du prospect {$prospect->nom} {$prospect->prenom} changé à {$nouveauStatut}.");
            }
        });

        ActivityLog::log('Modification prospect', 'Prospects', "Modification du prospect {$prospect->nom} {$prospect->prenom}.");

        return redirect()->route('prospects.index')->with('success', 'Prospect mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prospect $prospect)
    {
        ActivityLog::log('Suppression prospect', 'Prospects', "Suppression du prospect {$prospect->nom} {$prospect->prenom}.");

        $prospect->delete();

        return redirect()->route('prospects.index')->with('success', 'Prospect supprimé avec succès.');
    }

    /**
     * Convert the prospect into a client.
     */
    public function convertToClient(Prospect $prospect)
    {
        if ($prospect->client()->exists()) {
            return redirect()->route('prospects.show', $prospect)->with('error', 'Ce prospect est déjà converti en client.');
        }

        $client = DB::transaction(function () use ($prospect) {
            // Create client
            $client = Client::create([
                'prospect_id' => $prospect->id,
                'commercial_id' => $prospect->commercial_id,
                'filiale_id' => $prospect->filiale_id,
                'nom' => $prospect->nom,
                'prenom' => $prospect->prenom,
                'email' => $prospect->email,
                'telephone' => $prospect->telephone,
                'adresse' => $prospect->adresse,
                'ville' => $prospect->ville,
                'entreprise' => $prospect->entreprise,
                'statut' => 'Actif',
                'date_conversion' => now(),
            ]);

            // Save old status for logging
            $ancienStatut = $prospect->statut;

            // Update prospect status to Gagné
            $prospect->update(['statut' => 'Gagné']);

            // Create prospect history entry
            $prospect->histories()->create([
                'user_id' => auth()->id(),
                'action' => 'Conversion client',
                'description' => "Prospect converti en client. Nouveau client ID : {$client->id}.",
                'ancien_statut' => $ancienStatut,
                'nouveau_statut' => 'Gagné',
            ]);

            return $client;
        });

        ActivityLog::log('Conversion client', 'Clients', "Prospect {$prospect->nom} {$prospect->prenom} converti en client (Client ID: {$client->id}).");

        return redirect()->route('clients.show', $client)->with('success', 'Prospect converti en client avec succès.');
    }
    /**
     * Update status of the prospect via AJAX patch.
     */
    public function updateStatus(Request $request, Prospect $prospect)
    {
        if (!auth()->user()->hasRole('Administrateur|Responsable Commercial|Directeur Général') && $prospect->commercial_id !== auth()->id()) {
            return response()->json(['error' => 'Vous n\'êtes pas autorisé à modifier ce prospect.'], 403);
        }

        $request->validate([
            'statut' => 'required|string|in:Nouveau,Contacté,Qualifié,En négociation,Gagné,Perdu',
        ]);

        $ancienStatut = $prospect->statut;
        $nouveauStatut = $request->statut;

        if (in_array($ancienStatut, ['Gagné', 'Perdu']) && $ancienStatut !== $nouveauStatut) {
            return response()->json(['error' => 'Impossible de modifier le statut d\'un prospect déjà ' . $ancienStatut . '.'], 403);
        }

        if ($ancienStatut !== $nouveauStatut) {
            DB::transaction(function () use ($prospect, $ancienStatut, $nouveauStatut) {
                $prospect->update(['statut' => $nouveauStatut]);

                $prospect->histories()->create([
                    'user_id' => auth()->id(),
                    'action' => 'Changement statut',
                    'description' => "Le statut du prospect a été modifié (Kanban) de '{$ancienStatut}' à '{$nouveauStatut}'.",
                    'ancien_statut' => $ancienStatut,
                    'nouveau_statut' => $nouveauStatut,
                ]);

                ActivityLog::log('Changement statut prospect', 'Prospects', "Statut du prospect {$prospect->nom} {$prospect->prenom} changé à {$nouveauStatut} (via Kanban).");
            });
        }

        return response()->json([
            'success' => true,
            'message' => 'Statut du prospect mis à jour avec succès.',
            'prospect' => $prospect
        ]);
    }
    /**
     * Upload a document for the prospect.
     */
    public function uploadDocument(Request $request, Prospect $prospect)
    {
        $request->validate([
            'document' => 'required|file|max:10240', // max 10MB
        ]);

        $file = $request->file('document');
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getMimeType();
        $size = $file->getSize();

        // Store file in storage/app/public/documents/prospects
        $path = $file->store('documents/prospects', 'public');

        $prospect->documents()->create([
            'nom_fichier' => $originalName,
            'chemin_fichier' => $path,
            'type_mime' => $mimeType,
            'taille' => $size,
        ]);

        ActivityLog::log('Ajout document prospect', 'Prospects', "Document '{$originalName}' ajouté au prospect {$prospect->nom} {$prospect->prenom}.");

        return back()->with('success', 'Document ajouté avec succès.');
    }
    /**
     * Export prospects to CSV.
     */
    public function export(Request $request)
    {
        $query = Prospect::with(['commercial', 'filiale', 'source', 'campagne']);

        if (!auth()->user()->hasRole('Administrateur|Responsable Commercial|Directeur Général')) {
            $query->where('commercial_id', auth()->id());
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('filiale_id')) {
            $query->where('filiale_id', $request->filiale_id);
        }

        if ($request->filled('commercial_id')) {
            $query->where('commercial_id', $request->commercial_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%")
                  ->orWhere('entreprise', 'like', "%{$search}%");
            });
        }

        $prospects = $query->get();
        $filename = "prospects_export_" . date('Y-m-d_H-i-s') . ".csv";
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Nom', 'Prénom', 'Email', 'Téléphone', 'Entreprise', 'Profession', 'Filiale', 'Commercial', 'Source', 'Campagne', 'Statut', 'Date Création'];

        $callback = function() use($prospects, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM for Excel
            fputcsv($file, $columns, ';');

            foreach ($prospects as $prospect) {
                fputcsv($file, [
                    $prospect->id,
                    $prospect->nom,
                    $prospect->prenom,
                    $prospect->email,
                    $prospect->telephone,
                    $prospect->entreprise,
                    $prospect->profession,
                    $prospect->filiale ? $prospect->filiale->nom : '',
                    $prospect->commercial ? $prospect->commercial->name : '',
                    $prospect->source ? $prospect->source->nom : '',
                    $prospect->campagne ? $prospect->campagne->nom : '',
                    $prospect->statut,
                    $prospect->created_at->format('d/m/Y')
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import prospects from CSV.
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
            'filiale_id' => 'required|exists:filiales,id',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), "r");
        
        // Detect BOM and remove it if present
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        // Try to read first line to detect separator (; or ,)
        $firstLine = fgets($handle);
        $separator = strpos($firstLine, ';') !== false ? ';' : ',';
        rewind($handle);
        if ($bom === "\xEF\xBB\xBF") {
            fread($handle, 3); // Skip BOM again
        }
        
        // Skip header
        fgetcsv($handle, 1000, $separator);
        
        $count = 0;
        DB::beginTransaction();
        try {
            while (($data = fgetcsv($handle, 1000, $separator)) !== FALSE) {
                if (count($data) >= 1 && !empty($data[0])) { // Nom is required
                    Prospect::create([
                        'nom' => $data[0] ?? 'Inconnu',
                        'prenom' => $data[1] ?? null,
                        'email' => $data[2] ?? null,
                        'telephone' => $data[3] ?? null,
                        'entreprise' => $data[4] ?? null,
                        'statut' => 'Nouveau',
                        'filiale_id' => $request->filiale_id,
                        'commercial_id' => auth()->id(),
                    ]);
                    $count++;
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            return back()->with('error', "Erreur lors de l'importation: " . $e->getMessage());
        }
        fclose($handle);
        
        ActivityLog::log('Import prospects', 'Prospects', "$count prospects importés via CSV.");

        return redirect()->route('prospects.index')->with('success', "$count prospects ont été importés avec succès.");
    }

    /**
     * Send an email to the prospect.
     */
    public function sendEmail(Request $request, Prospect $prospect)
    {
        $request->validate([
            'sujet' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        if (empty($prospect->email)) {
            return back()->with('error', "Ce prospect n'a pas d'adresse email renseignée.");
        }

        try {
            Mail::to($prospect->email)->send(new ProspectEmail($request->sujet, $request->message));
            
            ActivityLog::log(
                'Email envoyé', 
                'Prospects', 
                "Email envoyé à {$prospect->nom} ({$prospect->email}). Sujet: {$request->sujet}"
            );

            return back()->with('success', 'Email envoyé avec succès au prospect.');
        } catch (\Exception $e) {
            return back()->with('error', "Erreur lors de l'envoi de l'email : " . $e->getMessage());
        }
    }
}
