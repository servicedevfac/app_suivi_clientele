<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div class="flex items-center space-x-3">
                <a href="{{ route('prospects.index') }}" class="inline-flex items-center justify-center p-2 text-slate-500 hover:text-slate-700 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 shadow-sm transition-all duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <div class="flex items-center space-x-2.5">
                        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $prospect->prenom }} {{ $prospect->nom }}</h1>
                        @php
                            $badgeClasses = match($prospect->statut) {
                                'Nouveau' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                                'Contacté' => 'bg-sky-50 text-sky-700 border-sky-100',
                                'Qualifié' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                'En négociation' => 'bg-amber-50 text-amber-700 border-amber-100',
                                'Gagné' => 'bg-green-50 text-green-700 border-green-100',
                                'Perdu' => 'bg-rose-50 text-rose-700 border-rose-100',
                                default => 'bg-slate-50 text-slate-700 border-slate-100',
                            };
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $badgeClasses }}">
                            {{ $prospect->statut }}
                        </span>
                    </div>
                    <p class="text-sm text-slate-500 mt-1">{{ $prospect->entreprise ?? 'Aucune entreprise' }} • {{ $prospect->profession ?? 'Pas de profession renseignée' }}</p>
                    @if($prospect->tags && count($prospect->tags) > 0)
                        <div class="mt-2 flex flex-wrap gap-1.5">
                            @foreach($prospect->tags as $tag)
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-600 text-[10px] font-bold rounded-md border border-slate-200">
                                    {{ trim($tag) }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="flex items-center space-x-2">
                @if(!$prospect->client()->exists() && $prospect->statut !== 'Gagné')
                    <form action="{{ route('prospects.convert', $prospect) }}" method="POST" class="inline-block" onsubmit="return confirm('Voulez-vous vraiment convertir ce prospect en client ?');">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-850 rounded-xl shadow-md shadow-emerald-600/10 hover:shadow-emerald-600/20 transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Convertir en Client
                        </button>
                    </form>
                @else
                    <span class="inline-flex items-center px-4 py-2 text-xs font-semibold text-slate-500 bg-slate-100 border border-slate-200 rounded-xl select-none">
                        Converti le {{ $prospect->client ? $prospect->client->date_conversion->format('d/m/Y') : '' }}
                    </span>
                @endif
                
                <a href="{{ route('prospects.edit', $prospect) }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-xl border border-indigo-200 transition-all">
                    Modifier
                </a>
                
                @if($prospect->email)
                <button type="button" onclick="document.getElementById('emailModal').classList.remove('hidden')" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-white bg-slate-800 hover:bg-slate-900 rounded-xl shadow-md transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    Email
                </button>
                @endif
            </div>
        </div>
    </x-slot>

    <!-- Main Content Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Column 1 & 2: Main Info Cards -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Contact Card -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Informations de Contact</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Email</span>
                        <span class="text-sm font-medium text-slate-800 block mt-1">{{ $prospect->email ?? 'Non renseigné' }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Téléphone</span>
                        <span class="text-sm font-medium text-slate-800 block mt-1">{{ $prospect->telephone ?? 'Non renseigné' }}</span>
                    </div>
                    <div class="md:col-span-2">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Adresse</span>
                        <span class="text-sm font-medium text-slate-800 block mt-1">
                            {{ $prospect->adresse ?? 'Non renseignée' }}
                            @if($prospect->ville)
                                <br><span class="text-slate-500">{{ $prospect->ville }}</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Qualification Card -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Détails Commercial / Besoin</h3>
                <div class="space-y-4">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Besoin</span>
                        <p class="text-sm font-medium text-slate-850 bg-slate-50/50 p-4 rounded-xl border border-slate-100/50 mt-1 whitespace-pre-line">
                            {{ $prospect->besoin ?? 'Aucun besoin spécifique formulé pour le moment.' }}
                        </p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Dernier contact</span>
                            <span class="text-sm font-medium text-slate-800 block mt-1">
                                {{ $prospect->date_contact ? $prospect->date_contact->format('d/m/Y H:i') : 'Aucun contact enregistré' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Prochain rappel planifié</span>
                            <span class="text-sm font-medium text-indigo-600 font-semibold block mt-1">
                                {{ $prospect->prochain_rappel ? $prospect->prochain_rappel->format('d/m/Y H:i') : 'Aucun rappel prévu' }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Commentaires additionnels</span>
                        <p class="text-xs text-slate-600 mt-1 italic">
                            {{ $prospect->commentaire ?? 'Pas de commentaire.' }}
                        </p>
                    </div>
                </div>
                </div>
            </div>

            <!-- Documents Card -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pièces Jointes & Documents</h3>
                </div>
                
                <!-- Upload Form -->
                <form action="{{ route('prospects.documents.store', $prospect) }}" method="POST" enctype="multipart/form-data" class="mb-4 flex flex-col md:flex-row md:items-center gap-3 bg-slate-50/50 p-3 rounded-xl border border-slate-100">
                    @csrf
                    <div class="flex-1">
                        <input type="file" name="document" required class="block w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-lg shadow-sm transition-all mt-2 md:mt-0">
                        Uploader
                    </button>
                </form>
                @error('document')
                    <p class="text-xs text-rose-500 mt-1 mb-3">{{ $message }}</p>
                @enderror

                <!-- Documents List -->
                <div class="space-y-2">
                    @forelse($prospect->documents as $doc)
                        <div class="flex items-center justify-between p-3 bg-white border border-slate-100 rounded-xl shadow-sm hover:border-indigo-100 transition-colors group">
                            <div class="flex items-center space-x-3 overflow-hidden">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <div class="truncate">
                                    <a href="{{ Storage::url($doc->chemin_fichier) }}" target="_blank" class="text-xs font-semibold text-slate-700 hover:text-indigo-600 truncate block">
                                        {{ $doc->nom_fichier }}
                                    </a>
                                    <span class="text-[10px] text-slate-400">
                                        {{ round($doc->taille / 1024, 1) }} KB • {{ $doc->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ Storage::url($doc->chemin_fichier) }}" download class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors tooltip" title="Télécharger">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic text-center py-4 bg-slate-50/50 rounded-xl border border-dashed border-slate-200">Aucun document attaché.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Column 3: Sidebar & Timeline -->
        <div class="space-y-6">
            <!-- Provenance Info Card -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Attribution & Provenance</h3>
                <div class="space-y-4">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Filiale</span>
                        <div class="text-sm font-semibold text-slate-800 mt-1">
                            {{ $prospect->filiale->nom }}
                        </div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Commercial assigné</span>
                        <div class="text-sm font-semibold text-slate-800 mt-1">
                            {{ $prospect->commercial ? $prospect->commercial->name : 'Non assigné' }}
                        </div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Source</span>
                        <div class="text-xs font-semibold text-slate-700 mt-1 bg-slate-100 px-2 py-1 rounded inline-block">
                            {{ $prospect->source ? $prospect->source->nom : 'Non définie' }}
                        </div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Campagne</span>
                        <div class="text-xs font-semibold text-slate-700 mt-1 bg-indigo-50/50 text-indigo-700 px-2 py-1 rounded inline-block">
                            {{ $prospect->campagne ? $prospect->campagne->nom : 'Aucune campagne' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline Card -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Historique & Timeline</h3>
                
                <div class="relative pl-6 space-y-6 after:absolute after:inset-y-1 after:left-2 after:w-0.5 after:bg-slate-150">
                    @forelse($prospect->histories->sortByDesc('created_at') as $history)
                        <div class="relative">
                            <!-- Dot indicators -->
                            @php
                                $dotColor = match($history->action) {
                                    'Création' => 'bg-indigo-500 ring-indigo-100',
                                    'Conversion client' => 'bg-emerald-500 ring-emerald-100',
                                    'Changement statut' => 'bg-amber-500 ring-amber-100',
                                    default => 'bg-slate-400 ring-slate-100',
                                };
                            @endphp
                            <span class="absolute -left-6 top-1.5 flex h-2.5 w-2.5 rounded-full {{ $dotColor }} ring-4 z-10"></span>
                            
                            <div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold text-slate-800">{{ $history->action }}</span>
                                    <span class="text-[9px] text-slate-400">{{ $history->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <p class="text-xs text-slate-500 mt-1">{{ $history->description }}</p>
                                
                                @if($history->ancien_statut || $history->nouveau_statut)
                                    <div class="mt-1 flex items-center space-x-1.5 text-[9px] font-medium text-slate-500">
                                        <span class="px-1.5 py-0.5 bg-slate-50 border border-slate-200 rounded">{{ $history->ancien_statut ?? 'Début' }}</span>
                                        <span>➔</span>
                                        <span class="px-1.5 py-0.5 bg-indigo-50 border border-indigo-150 text-indigo-700 rounded">{{ $history->nouveau_statut }}</span>
                                    </div>
                                @endif
                                
                                <div class="text-[9px] text-slate-400 mt-1">
                                    Par : {{ $history->user ? $history->user->name : 'Système' }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic">Aucun historique d'activité disponible.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Email Modal -->
    <div id="emailModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h3 class="text-sm font-bold text-slate-800">Envoyer un Email à {{ $prospect->prenom }} {{ $prospect->nom }}</h3>
                <button type="button" onclick="document.getElementById('emailModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form action="{{ route('prospects.email.send', $prospect) }}" method="POST" class="p-6">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-700 mb-2">Destinataire</label>
                    <input type="email" value="{{ $prospect->email }}" disabled class="w-full bg-slate-100 border border-slate-200 rounded-xl px-4 py-2 text-xs text-slate-500 cursor-not-allowed">
                </div>
                <div class="mb-4">
                    <label for="sujet" class="block text-xs font-semibold text-slate-700 mb-2">Sujet de l'email</label>
                    <input type="text" name="sujet" id="sujet" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" placeholder="Ex: Suite à notre échange">
                </div>
                <div class="mb-6">
                    <label for="message" class="block text-xs font-semibold text-slate-700 mb-2">Message</label>
                    <textarea name="message" id="message" rows="6" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" placeholder="Bonjour {{ $prospect->prenom }}, ..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('emailModal').classList.add('hidden')" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all">Annuler</button>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-md shadow-indigo-600/20 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        Envoyer
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
