<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
            <div class="flex items-center space-x-3">
                <a href="{{ route('prospects.index') }}" class="inline-flex items-center justify-center p-2 text-slate-500 hover:text-slate-700 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 shadow-sm transition-all duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Création rapide de prospects</h1>
                    <p class="text-sm text-slate-500 mt-1">Saisie simplifiée : Nom, Téléphone, Commercial, Source, Campagne & Publication.</p>
                </div>
            </div>

            <!-- Action Globale -->
            <div class="flex items-center space-x-3">
                <button type="submit" form="batchProspectForm" class="inline-flex items-center justify-center px-5 py-2.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl shadow-md shadow-indigo-600/20 transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    Enregistrer tous les prospects
                </button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6" x-data="prospectFormHandler()">

        <!-- Alert Notification Ajax Toast -->
        <div x-show="toast.show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform -translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-2"
             class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-sm">
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-xs font-semibold" x-text="toast.message"></span>
            </div>
            <button type="button" @click="toast.show = false" class="text-emerald-500 hover:text-emerald-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        @if (session('error'))
            <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl flex items-center space-x-3 shadow-sm">
                <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <span class="text-xs font-semibold">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Multi-row Batch Form -->
        <form id="batchProspectForm" method="POST" action="{{ route('prospects.store') }}">
            @csrf

            <!-- Configuration globale (Filiale) -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs">
                        
                    </div>
                    <div>
                        <label for="filiale_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Filiale d'affectation</label>
                        <p class="text-[11px] text-slate-400">Les prospects créés seront rattachés à cette filiale.</p>
                    </div>
                </div>
                <div class="w-full sm:w-64">
                    <select id="filiale_id" name="filiale_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                        @foreach($filiales as $filiale)
                            <option value="{{ $filiale->id }}" {{ old('filiale_id') == $filiale->id ? 'selected' : '' }}>{{ $filiale->nom }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Tableau des lignes prospects -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="px-4 py-3.5 w-12 text-center">#</th>
                                <th class="px-4 py-3.5 min-w-[170px]">Nom</th>
                                <th class="px-4 py-3.5 min-w-[150px]">Téléphone</th>
                                <th class="px-4 py-3.5 min-w-[160px]">Commercial</th>
                                <th class="px-4 py-3.5 min-w-[160px]">Sources</th>
                                <th class="px-4 py-3.5 min-w-[170px]">Campagne</th>
                                <th class="px-4 py-3.5 min-w-[190px]">Publication</th>
                                <th class="px-4 py-3.5 w-32 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-for="(row, index) in rows" :key="row.id">
                                <tr :class="row.isSaved ? 'bg-emerald-50/40 transition-colors' : 'hover:bg-slate-50/50 transition-colors'">
                                    <td class="px-4 py-3 text-center text-xs font-bold" :class="row.isSaved ? 'text-emerald-600' : 'text-slate-400'" x-text="index + 1"></td>
                                    
                                    <!-- Nom -->
                                    <td class="px-3 py-3">
                                        <input type="text" 
                                               :name="`prospects[${index}][nom]`" 
                                               x-model="row.nom"
                                               :disabled="row.isSaved"
                                               placeholder="Nom du prospect" 
                                               class="w-full border rounded-xl px-3 py-2 text-xs text-slate-800 transition-all placeholder:text-slate-300"
                                               :class="row.isSaved ? 'bg-emerald-50/20 border-emerald-200 text-emerald-900 font-semibold' : 'bg-slate-50/80 border-slate-200 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20'">
                                    </td>

                                    <!-- Téléphone -->
                                    <td class="px-3 py-3">
                                        <input type="text" 
                                               :name="`prospects[${index}][telephone]`" 
                                               x-model="row.telephone"
                                               :disabled="row.isSaved"
                                               placeholder="06 00 00 00 00" 
                                               class="w-full border rounded-xl px-3 py-2 text-xs text-slate-800 transition-all placeholder:text-slate-300"
                                               :class="row.isSaved ? 'bg-emerald-50/20 border-emerald-200 text-emerald-900 font-semibold' : 'bg-slate-50/80 border-slate-200 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20'">
                                    </td>

                                    <!-- Commercial -->
                                    <td class="px-3 py-3">
                                        <select :name="`prospects[${index}][commercial_id]`" 
                                                x-model="row.commercial_id"
                                                :disabled="row.isSaved"
                                                class="w-full border rounded-xl px-3 py-2 text-xs text-slate-700 transition-all"
                                                :class="row.isSaved ? 'bg-emerald-50/20 border-emerald-200 text-emerald-900' : 'bg-slate-50/80 border-slate-200 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20'">
                                            <option value="">Par défaut (Moi)</option>
                                            @foreach($commercials as $comm)
                                                <option value="{{ $comm->id }}">{{ $comm->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <!-- Sources -->
                                    <td class="px-3 py-3">
                                        <select :name="`prospects[${index}][source_id]`" 
                                                x-model="row.source_id"
                                                :disabled="row.isSaved"
                                                class="w-full border rounded-xl px-3 py-2 text-xs text-slate-700 transition-all"
                                                :class="row.isSaved ? 'bg-emerald-50/20 border-emerald-200 text-emerald-900' : 'bg-slate-50/80 border-slate-200 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20'">
                                            <option value="">Sélectionner une source</option>
                                            @foreach($sources as $source)
                                                <option value="{{ $source->id }}">{{ $source->nom }}</option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <!-- Campagne -->
                                    <td class="px-3 py-3">
                                        <select :name="`prospects[${index}][campagne_id]`" 
                                                x-model="row.campagne_id"
                                                :disabled="row.isSaved"
                                                class="w-full border rounded-xl px-3 py-2 text-xs text-slate-700 transition-all"
                                                :class="row.isSaved ? 'bg-emerald-50/20 border-emerald-200 text-emerald-900' : 'bg-slate-50/80 border-slate-200 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20'">
                                            <option value="">Sélectionner une campagne</option>
                                            @foreach($campagnes as $campagne)
                                                <option value="{{ $campagne->id }}">{{ $campagne->nom }}</option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <!-- Publication -->
                                    <td class="px-3 py-3">
                                        <select :name="`prospects[${index}][publication_id]`" 
                                                x-model="row.publication_id"
                                                :disabled="row.isSaved"
                                                class="w-full border rounded-xl px-3 py-2 text-xs text-slate-700 transition-all"
                                                :class="row.isSaved ? 'bg-emerald-50/20 border-emerald-200 text-emerald-900' : 'bg-slate-50/80 border-slate-200 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20'">
                                            <option value="">Sélectionner une publication</option>
                                            @if(isset($publications))
                                                @foreach($publications as $pub)
                                                    <option value="{{ $pub->id }}" x-show="!row.campagne_id || row.campagne_id == '{{ $pub->campagne_id }}'">
                                                        [{{ $pub->canal }}] {{ Str::limit($pub->titre, 35) }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </td>

                                    <!-- Action (SAVE AJAX / Statut Enregistré) -->
                                    <td class="px-3 py-3 text-center">
                                        <div class="flex items-center justify-center space-x-1">
                                            <!-- Bouton quand non sauvegardé -->
                                            <template x-if="!row.isSaved && !row.isSaving">
                                                <button type="button" 
                                                        @click="saveSingleRow(index)"
                                                        title="Enregistrer cette ligne uniquement (sans rechargement)" 
                                                        class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 rounded-lg shadow-sm transition-all duration-150">
                                                    SAVE
                                                </button>
                                            </template>

                                            <!-- Spinner de chargement -->
                                            <template x-if="row.isSaving">
                                                <span class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 rounded-lg">
                                                    <svg class="animate-spin -ml-0.5 mr-1.5 h-3.5 w-3.5 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    En cours...
                                                </span>
                                            </template>

                                            <!-- Badge Enregistré -->
                                            <template x-if="row.isSaved">
                                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold text-emerald-700 bg-emerald-100 rounded-lg border border-emerald-200">
                                                    ✔ Enregistré
                                                </span>
                                            </template>

                                            <!-- Bouton Supprimer -->
                                            <button type="button" 
                                                    x-show="rows.length > 1 && !row.isSaved" 
                                                    @click="removeRow(index)" 
                                                    title="Supprimer la ligne" 
                                                    class="inline-flex items-center justify-center p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Footer du tableau -->
                <div class="px-6 py-4 bg-slate-50/70 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <button type="button" 
                            @click="addRow()" 
                            class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-slate-700 bg-white hover:bg-slate-100 rounded-xl border border-slate-200 shadow-sm transition-all">
                        <svg class="w-4 h-4 mr-1.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        + Ajouter une ligne
                    </button>

                    <div class="flex items-center space-x-3">
                        <a href="{{ route('prospects.index') }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 transition-colors">
                            Annuler / Terminer
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center px-5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-md shadow-indigo-600/20 transition-all">
                            Enregistrer tous les prospects
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Script AlpineJS pour la gestion dynamique et AJAX sans rechargement -->
    <script nonce="{{ Vite::cspNonce() }}">
        document.addEventListener('alpine:init', () => {
            Alpine.data('prospectFormHandler', () => ({
                toast: { show: false, message: '' },
                rows: [
                    { id: Date.now() + 1, nom: '', telephone: '', commercial_id: '{{ auth()->id() }}', source_id: '', campagne_id: '', publication_id: '', isSaving: false, isSaved: false },
                    { id: Date.now() + 2, nom: '', telephone: '', commercial_id: '{{ auth()->id() }}', source_id: '', campagne_id: '', publication_id: '', isSaving: false, isSaved: false },
                    { id: Date.now() + 3, nom: '', telephone: '', commercial_id: '{{ auth()->id() }}', source_id: '', campagne_id: '', publication_id: '', isSaving: false, isSaved: false },
                    { id: Date.now() + 4, nom: '', telephone: '', commercial_id: '{{ auth()->id() }}', source_id: '', campagne_id: '', publication_id: '', isSaving: false, isSaved: false },
                ],
                addRow() {
                    this.rows.push({
                        id: Date.now() + Math.random(),
                        nom: '',
                        telephone: '',
                        commercial_id: '{{ auth()->id() }}',
                        source_id: '',
                        campagne_id: '',
                        publication_id: '',
                        isSaving: false,
                        isSaved: false
                    });
                },
                removeRow(index) {
                    if (this.rows.length > 1 && !this.rows[index].isSaved) {
                        this.rows.splice(index, 1);
                    }
                },
                showNotification(msg) {
                    this.toast.message = msg;
                    this.toast.show = true;
                    setTimeout(() => { this.toast.show = false; }, 4000);
                },
                async saveSingleRow(index) {
                    const row = this.rows[index];
                    if (row.isSaving || row.isSaved) return;

                    const nomVal = row.nom ? row.nom.trim() : '';
                    const phoneVal = row.telephone ? row.telephone.trim() : '';

                    if (!nomVal && !phoneVal) {
                        alert('Veuillez renseigner au moins le Nom ou le Téléphone avant de sauvegarder cette ligne.');
                        return;
                    }

                    row.isSaving = true;

                    try {
                        const filialeId = document.getElementById('filiale_id').value;
                        const response = await fetch("{{ route('prospects.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                filiale_id: filialeId,
                                nom: nomVal,
                                telephone: phoneVal,
                                commercial_id: row.commercial_id,
                                source_id: row.source_id,
                                campagne_id: row.campagne_id,
                                publication_id: row.publication_id
                            })
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            row.isSaving = false;
                            row.isSaved = true;
                            const prospectName = data.prospect && data.prospect.nom ? data.prospect.nom : nomVal;
                            this.showNotification(`Prospect "${prospectName}" enregistré avec succès !`);
                        } else {
                            row.isSaving = false;
                            alert(data.message || data.error || 'Erreur lors de l\'enregistrement de la ligne.');
                        }
                    } catch (err) {
                        row.isSaving = false;
                        alert('Une erreur réseau est survenue lors de l\'enregistrement.');
                    }
                }
            }));
        });
    </script>
</x-app-layout>
