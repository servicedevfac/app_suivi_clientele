<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Prospects</h1>
                <p class="text-sm text-slate-500 mt-1">Gérez le pipeline de vos opportunités commerciales et convertissez-les en clients.</p>
            </div>
            <div class="flex items-center space-x-3">
                <div class="flex bg-slate-100 p-1 rounded-xl h-[38px]">
                    <a href="{{ request()->fullUrlWithQuery(['view' => 'list']) }}" class="px-3 py-1 text-xs font-semibold rounded-lg flex items-center {{ request('view', 'list') === 'list' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                        Liste
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['view' => 'kanban']) }}" class="px-3 py-1 text-xs font-semibold rounded-lg flex items-center {{ request('view') === 'kanban' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
                        Kanban
                    </a>
                </div>
                <a href="{{ route('prospects.export', request()->query()) }}" class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl shadow-sm transition-all duration-200">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Exporter
                </a>
                <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')" class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl shadow-sm transition-all duration-200">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Importer
                </button>
                <a href="{{ route('prospects.create') }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl shadow-md shadow-indigo-600/10 hover:shadow-indigo-600/20 transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Créer un prospect
                </a>
            </div>
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
        <form method="GET" action="{{ route('prospects.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label for="search" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Recherche</label>
                <div class="relative">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Nom, email, entreprise..." 
                           class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2 text-xs text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                </div>
            </div>

            <!-- Filter Status -->
            <div>
                <label for="statut" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Statut</label>
                <select name="statut" id="statut" 
                        class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                    <option value="">Tous les statuts</option>
                    @foreach(['Nouveau', 'Contacté', 'Qualifié', 'En négociation', 'Gagné', 'Perdu'] as $st)
                        <option value="{{ $st }}" {{ request('statut') == $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
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
                    <a href="{{ route('prospects.index') }}" class="px-3 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all h-[38px] flex items-center justify-center">
                        Effacer
                    </a>
                @endif
            </div>
        </form>
    </div>

    @if(request('view') === 'kanban')
        <!-- Kanban Container -->
        @php
            $statuses = ['Nouveau', 'Contacté', 'Qualifié', 'En négociation', 'Gagné', 'Perdu'];
            $groupedProspects = $prospects->groupBy('statut');
        @endphp
        <div class="flex space-x-4 overflow-x-auto pb-6 -mx-4 px-4 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8 kanban-container">
            @foreach($statuses as $status)
                <div class="flex-shrink-0 w-80 bg-slate-100/50 border border-slate-200/60 rounded-2xl p-4 flex flex-col kanban-column" data-status="{{ $status }}">
                    <div class="flex items-center justify-between mb-4 px-1">
                        <h3 class="font-bold text-slate-700 text-sm">{{ $status }}</h3>
                        <span class="bg-white border border-slate-200 text-slate-500 text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">{{ isset($groupedProspects[$status]) ? count($groupedProspects[$status]) : 0 }}</span>
                    </div>
                    
                    <div class="flex-1 space-y-3 min-h-[150px] sortable-list" data-status="{{ $status }}">
                        @if(isset($groupedProspects[$status]))
                            @foreach($groupedProspects[$status] as $prospect)
                                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm cursor-grab active:cursor-grabbing hover:border-indigo-300 hover:shadow-md transition-all group" data-id="{{ $prospect->id }}">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-6 h-6 rounded-md bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-[10px] border border-indigo-100/55">
                                                {{ strtoupper(substr($prospect->nom, 0, 1)) }}{{ strtoupper(substr($prospect->prenom ?? '', 0, 1)) }}
                                            </div>
                                            <a href="{{ route('prospects.show', $prospect) }}" class="font-bold text-slate-800 text-sm hover:text-indigo-600 flex items-center">
                                                <span class="truncate max-w-[120px]">{{ $prospect->prenom }} {{ $prospect->nom }}</span>
                                                @php
                                                    $scoreColor = 'bg-slate-100 text-slate-600';
                                                    if($prospect->score >= 50) $scoreColor = 'bg-emerald-100 text-emerald-700 border border-emerald-200';
                                                    elseif($prospect->score >= 20) $scoreColor = 'bg-amber-100 text-amber-700 border border-amber-200';
                                                    elseif($prospect->score > 0) $scoreColor = 'bg-rose-100 text-rose-700 border border-rose-200';
                                                @endphp
                                                <span class="inline-flex items-center justify-center px-1.5 py-0.5 ml-2 text-[9px] font-bold rounded-full {{ $scoreColor }}" title="Lead Score">
                                                    ⭐ {{ $prospect->score }}
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="text-xs text-slate-500 font-medium mb-3">
                                        {{ $prospect->entreprise ?? '—' }}
                                    </div>
                                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-50">
                                        <div class="text-[10px] text-slate-400 flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            {{ $prospect->created_at->format('d/m/Y') }}
                                        </div>
                                        @if($prospect->commercial)
                                            <div class="w-5 h-5 rounded-full bg-slate-200 flex items-center justify-center text-[9px] font-bold text-slate-600 tooltip" title="{{ $prospect->commercial->name }}">
                                                {{ strtoupper(substr($prospect->commercial->name, 0, 2)) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js" nonce="{{ Vite::cspNonce() }}"></script>
        <script nonce="{{ Vite::cspNonce() }}">
            document.addEventListener('DOMContentLoaded', function () {
                const lists = document.querySelectorAll('.sortable-list');
                
                lists.forEach(list => {
                    new Sortable(list, {
                        group: 'kanban',
                        animation: 150,
                        ghostClass: 'opacity-50',
                        dragClass: 'cursor-grabbing',
                        onEnd: function (evt) {
                            const itemEl = evt.item;
                            const toList = evt.to;
                            
                            const prospectId = itemEl.getAttribute('data-id');
                            const newStatus = toList.getAttribute('data-status');
                            const oldStatus = evt.from.getAttribute('data-status');

                            if (newStatus !== oldStatus) {
                                const oldCounter = evt.from.previousElementSibling.querySelector('span');
                                const newCounter = toList.previousElementSibling.querySelector('span');
                                oldCounter.textContent = parseInt(oldCounter.textContent) - 1;
                                newCounter.textContent = parseInt(newCounter.textContent) + 1;

                                fetch(`/prospects/${prospectId}/status`, {
                                    method: 'PATCH',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({ statut: newStatus })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if(data.error) {
                                        alert(data.error);
                                        evt.from.insertBefore(itemEl, evt.from.children[evt.oldIndex]);
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('Une erreur est survenue lors de la mise à jour.');
                                    evt.from.insertBefore(itemEl, evt.from.children[evt.oldIndex]);
                                });
                            }
                        },
                    });
                });
            });
        </script>
        @endpush

    @else
    <!-- Table Container Card -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Prospect</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Filiale</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Commercial</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Commentaire</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($prospects as $prospect)
                        <tr class="hover:bg-slate-50/30 transition-colors duration-150">
                            <!-- Prospect identity -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs border border-indigo-100/55 shadow-inner">
                                        {{ strtoupper(substr($prospect->nom, 0, 1)) }}{{ strtoupper(substr($prospect->prenom ?? '', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-800 text-sm flex items-center">
                                            {{ $prospect->prenom }} {{ $prospect->nom }}
                                            @php
                                                $scoreColor = 'bg-slate-100 text-slate-600';
                                                if($prospect->score >= 50) $scoreColor = 'bg-emerald-100 text-emerald-700 border border-emerald-200';
                                                elseif($prospect->score >= 20) $scoreColor = 'bg-amber-100 text-amber-700 border border-amber-200';
                                                elseif($prospect->score > 0) $scoreColor = 'bg-rose-100 text-rose-700 border border-rose-200';
                                            @endphp
                                            <span class="inline-flex items-center justify-center px-1.5 py-0.5 ml-2 text-[10px] font-bold rounded-full {{ $scoreColor }}" title="Lead Score : {{ $prospect->score }} points">
                                                ⭐ {{ $prospect->score }}
                                            </span>
                                        </div>
                                        <div class="text-[10px] text-slate-400 mt-0.5">
                                            {{ $prospect->email ?? 'Pas d\'email' }} • {{ $prospect->telephone ?? 'Pas de tél' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <!-- Entreprise -->
                         
                            <!-- Filiale -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 text-xs font-medium text-slate-600 bg-slate-100 rounded-lg">
                                    {{ $prospect->filiale->nom }}
                                </span>
                            </td>
                            <!-- Commercial -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($prospect->commercial)
                                    <div class="text-slate-700 text-xs font-semibold">{{ $prospect->commercial->name }}</div>
                                @else
                                    <span class="text-slate-400 text-xs italic">Non assigné</span>
                                @endif
                            </td>
                            <!-- Commentaire -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        <span class="text-slate-600 text-xs">{{ $prospect->commentaire }}</span>
                                    </div>
                               
                            </td>
                            <!-- Statut Badge -->
                            <td class="px-6 py-4 whitespace-nowrap">
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
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $badgeClasses }}">
                                    {{ $prospect->statut }}
                                </span>
                            </td>
                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-semibold space-x-2">
                                <!-- Convert to client button -->
                                @if(!$prospect->client()->exists() && $prospect->statut !== 'Gagné')
                                    <form action="{{ route('prospects.convert', $prospect) }}" method="POST" class="inline-block" onsubmit="return confirm('Voulez-vous vraiment convertir ce prospect en client ?');">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center justify-center px-2.5 py-1.5 text-[10px] font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-lg transition-colors" title="Convertir en client">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            Convertir
                                        </button>
                                    </form>
                                @else
                                    <span class="inline-flex items-center text-[10px] font-bold text-slate-400 bg-slate-50 border border-slate-200 px-2.5 py-1.5 rounded-lg select-none">
                                        Client converti
                                    </span>
                                @endif

                                <a href="{{ route('prospects.show', $prospect) }}" class="inline-flex items-center justify-center p-2 text-slate-600 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 rounded-lg transition-colors" title="Détails">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('prospects.edit', $prospect) }}" class="inline-flex items-center justify-center p-2 text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors" title="Éditer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('prospects.destroy', $prospect) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce prospect ?');">
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
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm">
                                <svg class="w-10 h-10 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Aucun prospect trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($prospects instanceof \Illuminate\Pagination\LengthAwarePaginator && $prospects->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $prospects->links() }}
            </div>
        @endif
    </div>
    @endif

    <!-- Import Modal -->
    <div id="importModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h3 class="text-sm font-bold text-slate-800">Importer des prospects</h3>
                <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form action="{{ route('prospects.import') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-700 mb-2">Fichier CSV</label>
                    <input type="file" name="csv_file" required accept=".csv" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer border border-slate-200 rounded-xl">
                    <p class="text-[10px] text-slate-500 mt-2">Format attendu: Nom, Prénom, Email, Téléphone, Entreprise. Séparateur: point-virgule (;).</p>
                </div>
                <div class="mb-6">
                    <label for="import_filiale_id" class="block text-xs font-semibold text-slate-700 mb-2">Filiale par défaut</label>
                    <select name="filiale_id" id="import_filiale_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                        <option value="">Sélectionner une filiale</option>
                        @foreach($filiales as $filiale)
                            <option value="{{ $filiale->id }}">{{ $filiale->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all">Annuler</button>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-md shadow-indigo-600/20">Importer</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
