<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Ventes</h1>
                <p class="text-sm text-slate-500 mt-1">Consultez l'historique des ventes enregistrées et mettez à jour leur statut.</p>
            </div>
            <a href="{{ route('ventes.create') }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl shadow-md shadow-indigo-600/10 hover:shadow-indigo-600/20 transition-all duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Enregistrer une vente
            </a>
        </div>
    </x-slot>

    <!-- Session Notifications -->
    @if (session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center space-x-3 shadow-sm">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-xs font-semibold">{{ session('success') }}</span>
        </div>
    @endif
    
    @if (session('error'))
        <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl flex items-center space-x-3 shadow-sm">
            <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <span class="text-xs font-semibold">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Filters Section -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('ventes.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label for="search" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Recherche</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Nom du client..." 
                       class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2 text-xs text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
            </div>

            <!-- Filter Status -->
            <div>
                <label for="statut" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Statut</label>
                <select name="statut" id="statut" 
                        class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                    <option value="">Tous les statuts</option>
                    <option value="En attente" {{ request('statut') == 'En attente' ? 'selected' : '' }}>En attente</option>
                    <option value="Validée" {{ request('statut') == 'Validée' ? 'selected' : '' }}>Validée</option>
                    <option value="Annulée" {{ request('statut') == 'Annulée' ? 'selected' : '' }}>Annulée</option>
                </select>
            </div>

            <!-- Filter Filiale -->
            <div>
                <label for="filiale_id" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Filiale</label>
                <select name="filiale_id" id="filiale_id" 
                        class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                    <option value="">Toutes les filiales</option>
                    @foreach($filiales as $filiale)
                        <option value="{{ $filiale->id }}" {{ request('filiale_id') == $filiale->id ? 'selected' : '' }}>{{ $filiale->nom }}</option>
                    @endforeach
                </select>
            </div>

            <!--Filter Commercial -->
            <div class="flex items-end space-x-2">
                @if(Auth::user()->hasRole('Administrateur|Responsable Commercial|Directeur Général'))
                <div class="flex-1">
                    <label for="commercial_id" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Commercial</label>
                    <select name="commercial_id" id="commercial_id" 
                            class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                        <option value="">Tous les commerciaux</option>
                        @foreach($commercials as $comm)
                            <option value="{{ $comm->id }}" {{ request('commercial_id') == $comm->id ? 'selected' : '' }}>{{ $comm->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-slate-800 hover:bg-slate-900 rounded-xl transition-all h-[38px] flex items-center justify-center">
                    Filtrer
                </button>
                @if(request()->anyFilled(['search', 'statut', 'filiale_id', 'commercial_id']))
                    <a href="{{ route('ventes.index') }}" class="px-3 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all h-[38px] flex items-center justify-center">
                        Effacer
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Container Card -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Client</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Produit</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Quantité / Réduction</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Montant Final</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Filiale / Commercial</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($ventes as $vente)
                        <tr class="hover:bg-slate-50/30 transition-colors duration-150">
                            <!-- Client -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($vente->client)
                                    <div class="flex items-center space-x-3">
                                        <div>
                                            <a href="{{ route('clients.show', $vente->client) }}" class="font-semibold text-indigo-650 hover:text-indigo-800 text-sm">
                                                {{ $vente->client->prenom }} {{ $vente->client->nom }}
                                            </a>
                                            <div class="text-[10px] text-slate-400 font-medium">
                                                {{ $vente->client->entreprise ?? 'Particulier' }}
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-400 text-xs italic">Client inconnu</span>
                                @endif
                            </td>
                            <!-- Produit -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-slate-800 text-sm font-semibold">{{ $vente->produit ? $vente->produit->nom : 'Produit inconnu' }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Date : {{ $vente->date_vente ? $vente->date_vente->format('d/m/Y') : '—' }}</div>
                            </td>
                            <!-- Quantite / Reduction -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-slate-650 text-xs font-medium">Qté : {{ $vente->quantite }}</div>
                                <div class="text-[10px] text-rose-600 mt-0.5 font-medium">Réd. : -{{ number_format($vente->reduction, 2, ',', ' ') }} xof</div>
                            </td>
                            <!-- Montant -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-extrabold text-slate-900">
                                {{ number_format($vente->montant, 2, ',', ' ') }} xof
                            </td>
                            <!-- Filiale / Commercial -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-slate-650 text-xs font-semibold">{{ $vente->filiale->nom }}</div>
                                <div class="text-[10px] text-slate-450 mt-0.5">{{ $vente->commercial ? $vente->commercial->name : 'Non assigné' }}</div>
                            </td>
                            <!-- Statut Badge -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $stClasses = match($vente->statut) {
                                        'Validée' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                        'Annulée' => 'bg-rose-50 text-rose-700 border-rose-100',
                                        default => 'bg-amber-50 text-amber-700 border-amber-100',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $stClasses }}">
                                    {{ $vente->statut }}
                                </span>
                            </td>
                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-semibold space-x-2">
                                @if($vente->statut !== 'Validée' || auth()->user()->hasRole('Administrateur'))
                                    <a href="{{ route('ventes.edit', $vente) }}" class="inline-flex items-center justify-center p-2 text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors" title="Éditer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('ventes.destroy', $vente) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette vente ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center p-2 text-rose-600 hover:text-rose-900 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors" title="Supprimer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-[10px] text-slate-400 font-bold bg-slate-50 border border-slate-200 px-2 py-1 rounded select-none inline-block">
                                        🔒 Validée (Admin seul)
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-sm">
                                <svg class="w-10 h-10 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Aucune vente trouvée.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($ventes->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $ventes->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
