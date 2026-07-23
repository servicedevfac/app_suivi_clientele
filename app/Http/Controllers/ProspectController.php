<?php

namespace App\Http\Controllers;

use App\Models\Prospect;
use App\Models\User;
use App\Models\Filiale;
use App\Models\Source;
use App\Models\Campagne;
use App\Models\Publication;
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
        $sources = Source::all();
        $campagnes = Campagne::all();

        return view('prospects.index', compact('prospects', 'filiales', 'commercials', 'sources', 'campagnes'));
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
        $publications = Publication::where('statut', 'active')->latest()->get();

        return view('prospects.create', compact('commercials', 'filiales', 'sources', 'campagnes', 'publications'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProspectRequest $request)
    {
        $defaultFilialeId = $request->input('filiale_id') ?: (\App\Models\Filiale::first()->id ?? 1);

        if ($request->has('prospects') && is_array($request->prospects)) {
            $createdCount = 0;
            DB::transaction(function () use ($request, $defaultFilialeId, &$createdCount) {
                foreach ($request->prospects as $item) {
                    $nom = trim($item['nom'] ?? '');
                    $phone = trim($item['telephone'] ?? '');

                    // Skip row if both name and phone are empty
                    if (empty($nom) && empty($phone)) {
                        continue;
                    }

                    if (empty($nom)) {
                        $nom = 'Inconnu (' . ($phone ?: 'Sans numéro') . ')';
                    }

                    $filialeId = !empty($item['filiale_id']) ? $item['filiale_id'] : $defaultFilialeId;

                    $commercialId = auth()->id();
                    if (auth()->user()->hasRole('Administrateur|Responsable Commercial|Directeur Général') && !empty($item['commercial_id'])) {
                        $commercialId = $item['commercial_id'];
                    }

                    $prospect = Prospect::create([
                        'nom' => $nom,
                        'telephone' => $phone ?: null,
                        'source_id' => !empty($item['source_id']) ? $item['source_id'] : null,
                        'campagne_id' => !empty($item['campagne_id']) ? $item['campagne_id'] : null,
                        'publication_id' => !empty($item['publication_id']) ? $item['publication_id'] : null,
                        'filiale_id' => $filialeId,
                        'commercial_id' => $commercialId,
                        'statut' => 'Nouveau',
                        'date_contact' => now(),
                    ]);

                    $prospect->histories()->create([
                        'user_id' => auth()->id(),
                        'action' => 'Création',
                        'description' => 'Création initiale du prospect.',
                        'ancien_statut' => null,
                        'nouveau_statut' => 'Nouveau',
                    ]);

                    ActivityLog::log('Création prospect', 'Prospects', "Création du prospect {$prospect->nom}.");
                    $createdCount++;
                }
            });

            if ($createdCount === 0) {
                return redirect()->back()->with('error', 'Veuillez remplir au moins un prospect (Nom ou Téléphone).');
            }

            return redirect()->route('prospects.index')->with('success', "$createdCount prospect(s) créé(s) avec succès.");
        }

        $validated = $request->validated();
        if (empty($validated['nom'])) {
            $validated['nom'] = 'Inconnu (' . ($validated['telephone'] ?? 'Sans numéro') . ')';
        }

        $validated['filiale_id'] = $validated['filiale_id'] ?? $defaultFilialeId;
        $validated['statut'] = $validated['statut'] ?? 'Nouveau';
        $validated['montant_estime'] = $validated['montant_estime'] ?? null;
        $validated['probabilite'] = $validated['probabilite'] ?? 0;
        $validated['score'] = $validated['score'] ?? 0;
        $validated['date_contact'] = $validated['date_contact'] ?? now();

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

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Prospect créé avec succès.',
                'prospect' => $prospect
            ]);
        }

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
        $publications = Publication::where('statut', 'active')->latest()->get();

        return view('prospects.edit', compact('prospect', 'commercials', 'filiales', 'sources', 'campagnes', 'publications'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProspectRequest $request, Prospect $prospect)
    {
        $validated = $request->validated();
        if (empty($validated['nom'])) {
            $validated['nom'] = 'Inconnu (' . ($validated['telephone'] ?? 'Sans numéro') . ')';
        }
        // Set default values for missing fields
        $validated['montant_estime'] = $validated['montant_estime'] ?? $prospect->montant_estime;
        $validated['probabilite'] = $validated['probabilite'] ?? $prospect->probabilite;
        $validated['score'] = $validated['score'] ?? $prospect->score;
        
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
        $filename = "prospects_export_" . date('Y-m-d_H-i-s') . ".xlsx";
        $columns = ['ID', 'Nom', 'Prénom', 'Email', 'Téléphone', 'Entreprise', 'Profession', 'Filiale', 'Commercial', 'Source', 'Campagne', 'Statut', 'Date Création'];

        $rows = [];
        foreach ($prospects as $prospect) {
            $rows[] = [
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
            ];
        }

        return \App\Support\SimpleExcel::export($filename, $columns, $rows);
    }

    /**
     * Import prospects from Excel (.xlsx) or CSV.
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|max:10240',
            'filiale_id' => 'required|exists:filiales,id',
            'source_id' => 'nullable|exists:sources,id',
            'campagne_id' => 'nullable|exists:campagnes,id',
        ]);

        $file = $request->file('csv_file');

        try {
            $rows = \App\Support\SimpleExcel::import($file->getRealPath(), $file->getClientOriginalName());
        } catch (\Exception $e) {
            return back()->with('error', "Erreur lors de la lecture du fichier : " . $e->getMessage());
        }

        if (empty($rows) || count($rows) < 1) {
            return back()->with('error', "Le document importé est vide.");
        }

        $headerRow = $rows[0];
        $phoneColIdx = 0; // Par défaut colonne 0 si une seule colonne
        $nomColIdx = 0;
        $prenomColIdx = 1;
        $emailColIdx = 2;
        $entrepriseColIdx = 4;
        $commentaireColIdx = 5;
        $sourceColIdx = -1;
        $campagneColIdx = -1;
        $dateColIdx = -1;

        // Si le fichier contient plusieurs colonnes ou une ligne d'en-tête
        if (count($headerRow) > 1 || preg_match('/nom|t[eé]l|mail|phone|num/i', (string) ($headerRow[0] ?? ''))) {
            $phoneColIdx = 3; // défaut standard
            foreach ($headerRow as $idx => $colTitle) {
                $t = strtolower(trim((string) $colTitle));
                if (preg_match('/t[eé]l|phone|mob|num/i', $t)) {
                    $phoneColIdx = $idx;
                } elseif (preg_match('/pr[eé]nom/i', $t)) {
                    $prenomColIdx = $idx;
                } elseif (preg_match('/^nom|name/i', $t) && !preg_match('/pr[eé]nom/i', $t)) {
                    $nomColIdx = $idx;
                } elseif (preg_match('/mail/i', $t)) {
                    $emailColIdx = $idx;
                } elseif (preg_match('/entr|soc|comp/i', $t)) {
                    $entrepriseColIdx = $idx;
                } elseif (preg_match('/comm|note|desc|besoin|obs|rem/i', $t)) {
                    $commentaireColIdx = $idx;
                } elseif (preg_match('/source|orig/i', $t)) {
                    $sourceColIdx = $idx;
                } elseif (preg_match('/camp/i', $t)) {
                    $campagneColIdx = $idx;
                } elseif (preg_match('/date|cr[eé]at/i', $t)) {
                    $dateColIdx = $idx;
                }
            }
            array_shift($rows); // Ignorer l'en-tête
        }

        $count = 0;
        DB::beginTransaction();
        try {
            foreach ($rows as $data) {
                $phone = isset($data[$phoneColIdx]) ? trim((string) $data[$phoneColIdx]) : '';
                if (empty($phone)) {
                    continue; // Seul le numéro de téléphone est obligatoire !
                }

                $nom = isset($data[$nomColIdx]) && $nomColIdx !== $phoneColIdx ? trim((string) $data[$nomColIdx]) : '';
                if (empty($nom)) {
                    $nom = 'Inconnu (' . $phone . ')';
                }

                $sourceId = $request->source_id ?: null;
                if ($sourceColIdx !== -1 && isset($data[$sourceColIdx]) && !empty(trim((string) $data[$sourceColIdx]))) {
                    $val = trim((string) $data[$sourceColIdx]);
                    $foundSource = Source::where('nom', 'like', $val)->orWhere('id', $val)->first();
                    if ($foundSource) {
                        $sourceId = $foundSource->id;
                    }
                }

                $campagneId = $request->campagne_id ?: null;
                if ($campagneColIdx !== -1 && isset($data[$campagneColIdx]) && !empty(trim((string) $data[$campagneColIdx]))) {
                    $val = trim((string) $data[$campagneColIdx]);
                    $foundCamp = Campagne::where('nom', 'like', $val)->orWhere('id', $val)->first();
                    if ($foundCamp) {
                        $campagneId = $foundCamp->id;
                    }
                }

                $parsedDate = null;
                if ($dateColIdx !== -1 && isset($data[$dateColIdx]) && !empty(trim((string) $data[$dateColIdx]))) {
                    $val = trim((string) $data[$dateColIdx]);
                    try {
                        // Gestion des numéros de série Excel (ex: 45300) vs dates en texte ('Y-m-d', 'd/m/Y')
                        if (is_numeric($val) && $val > 20000 && $val < 100000) {
                            $parsedDate = \Carbon\Carbon::createFromDate(1900, 1, 1)->addDays($val - 2);
                        } else {
                            $parsedDate = \Carbon\Carbon::parse($val);
                        }
                    } catch (\Exception $e) {
                        $parsedDate = null;
                    }
                }

                $prospect = new Prospect([
                    'nom' => $nom,
                    'prenom' => isset($data[$prenomColIdx]) && $prenomColIdx !== $phoneColIdx ? trim((string) $data[$prenomColIdx]) ?: null : null,
                    'email' => isset($data[$emailColIdx]) && $emailColIdx !== $phoneColIdx ? trim((string) $data[$emailColIdx]) ?: null : null,
                    'telephone' => $phone,
                    'entreprise' => isset($data[$entrepriseColIdx]) && $entrepriseColIdx !== $phoneColIdx ? trim((string) $data[$entrepriseColIdx]) ?: null : null,
                    'statut' => 'Nouveau',
                    'commentaire' => isset($data[$commentaireColIdx]) && $commentaireColIdx !== $phoneColIdx ? trim((string) $data[$commentaireColIdx]) ?: null : null,
                    'filiale_id' => $request->filiale_id,
                    'source_id' => $sourceId,
                    'campagne_id' => $campagneId,
                    'commercial_id' => auth()->id(),
                    'montant_estime' => null,
                    'probabilite' => 0,
                    'score' => 0,
                    'date_contact' => $parsedDate ?? now(),
                ]);

                $prospect->is_imported = true;

                if ($parsedDate) {
                    $prospect->created_at = $parsedDate;
                    $prospect->updated_at = $parsedDate;
                    $prospect->save(['timestamps' => false]);
                } else {
                    $prospect->save();
                }

                $count++;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', "Erreur lors de l'importation : " . $e->getMessage());
        }

        ActivityLog::log('Import prospects', 'Prospects', "$count prospects importés via fichier Excel/CSV.");

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
