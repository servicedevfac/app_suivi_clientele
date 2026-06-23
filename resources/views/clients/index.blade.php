<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Clients</h1>
                <p class="text-sm text-slate-500 mt-1">Consultez et gérez votre portefeuille de clients actifs et inactifs.</p>
            </div>
            <a href="{{ route('clients.create') }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl shadow-md shadow-indigo-600/10 hover:shadow-indigo-600/20 transition-all duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Créer un client
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

    <!-- Filters Section -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('clients.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label for="search" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Recherche</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Nom, email, entreprise..." 
                       class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2 text-xs text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
            </div>

            <!-- Filter Status -->
            <div>
                <label for="statut" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Statut</label>
                <select name="statut" id="statut" 
                        class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                    <option value="">Tous les statuts</option>
                    <option value="Actif" {{ request('statut') == 'Actif' ? 'selected' : '' }}>Actif</option>
                    <option value="Inactif" {{ request('statut') == 'Inactif' ? 'selected' : '' }}>Inactif</option>
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

            <!-- Filter Commercial -->
            <div class="flex items-end space-x-2">
                @if(Auth::user()->hasRole('Administrateur'))
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
                    <a href="{{ route('clients.index') }}" class="px-3 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all h-[38px] flex items-center justify-center">
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
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Filiale</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Commercial</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Date de conversion</th>                       
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($clients as $client)
                        <tr class="hover:bg-slate-50/30 transition-colors duration-150">
                            <!-- Client identity -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs border border-indigo-100/55 shadow-inner">
                                        {{ strtoupper(substr($client->nom, 0, 1)) }}{{ strtoupper(substr($client->prenom ?? '', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-800 text-sm">
                                            {{ $client->prenom }} {{ $client->nom }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 mt-0.5">
                                            {{ $client->email ?? 'Pas d\'email' }} • {{ $client->telephone ?? 'Pas de tél' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <!-- Filiale -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 text-xs font-medium text-slate-600 bg-slate-100 rounded-lg">
                                    {{ $client->filiale->nom }}
                                </span>
                            </td>
                            <!-- Commercial -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($client->commercial)
                                    <div class="text-slate-700 text-xs font-semibold">{{ $client->commercial->name }}</div>
                                @else
                                    <span class="text-slate-400 text-xs italic">Non assigné</span>
                                @endif
                            </td>
                            <!-- Date Conversion -->
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-600 font-medium">
                                {{ $client->date_conversion ? $client->date_conversion->format('d/m/Y') : 'Création manuelle' }}
                            </td>
                            <!-- Statut Badge -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($client->statut === 'Actif')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                        Actif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400 mr-1.5"></span>
                                        Inactif
                                    </span>
                                @endif
                            </td>
                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-semibold space-x-2">
                                <a href="{{ route('clients.show', $client) }}" class="inline-flex items-center justify-center p-2 text-slate-600 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 rounded-lg transition-colors" title="Détails">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('clients.edit', $client) }}" class="inline-flex items-center justify-center p-2 text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors" title="Éditer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('clients.destroy', $client) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce client ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center p-2 text-rose-600 hover:text-rose-900 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors" title="Supprimer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-sm">
                                <svg class="w-10 h-10 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Aucun client trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($clients->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $clients->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
