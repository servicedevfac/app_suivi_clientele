<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Rappels & Relances</h1>
                <p class="text-sm text-slate-500 mt-1">Planifiez et suivez vos relances téléphoniques, WhatsApp, emails et rendez-vous.</p>
            </div>
            <a href="{{ route('relances.create') }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl shadow-md shadow-indigo-600/10 hover:shadow-indigo-600/20 transition-all duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Planifier une relance
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

    <!-- Relance stats & Quick tabs -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-6 mb-6">
        <!-- Quick Link Tab: Toutes -->
        <a href="{{ route('relances.index') }}" class="bg-white p-5 rounded-2xl border @if(!request()->has('filter')) border-indigo-200 bg-indigo-50/10 shadow-indigo-100/5 @else border-slate-100 @endif shadow-sm hover:border-indigo-300 transition-all duration-150 flex items-center space-x-4">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Toutes</p>
                <h3 class="text-lg font-bold text-slate-800 mt-0.5">
                    {{ \App\Models\Relance::when(!auth()->user()->hasRole('Administrateur'), function($q) { $q->where('commercial_id', auth()->id()); })->count() }}
                </h3>
            </div>
        </a>

        <!-- Quick Link Tab: Aujourd'hui -->
        <a href="{{ route('relances.index', ['filter' => 'today']) }}" class="bg-white p-5 rounded-2xl border @if(request('filter') === 'today') border-indigo-200 bg-indigo-50/10 shadow-indigo-100/5 @else border-slate-100 @endif shadow-sm hover:border-indigo-300 transition-all duration-150 flex items-center space-x-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Aujourd'hui</p>
                <h3 class="text-lg font-bold text-slate-800 mt-0.5">
                    {{ \App\Models\Relance::whereDate('date_relance', today())->when(!auth()->user()->hasRole('Administrateur'), function($q) { $q->where('commercial_id', auth()->id()); })->count() }}
                </h3>
            </div>
        </a>

        <!-- Quick Link Tab: À venir -->
        <a href="{{ route('relances.index', ['filter' => 'upcoming']) }}" class="bg-white p-5 rounded-2xl border @if(request('filter') === 'upcoming') border-indigo-200 bg-indigo-50/10 shadow-indigo-100/5 @else border-slate-100 @endif shadow-sm hover:border-indigo-300 transition-all duration-150 flex items-center space-x-4">
            <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">À venir</p>
                <h3 class="text-lg font-bold text-slate-800 mt-0.5">
                    {{ \App\Models\Relance::whereDate('date_relance', '>', today())->when(!auth()->user()->hasRole('Administrateur'), function($q) { $q->where('commercial_id', auth()->id()); })->count() }}
                </h3>
            </div>
        </a>

        <!-- Quick Link Tab: En retard -->
        <a href="{{ route('relances.index', ['filter' => 'overdue']) }}" class="bg-white p-5 rounded-2xl border @if(request('filter') === 'overdue') border-indigo-200 bg-indigo-50/10 shadow-indigo-100/5 @else border-slate-100 @endif shadow-sm hover:border-indigo-300 transition-all duration-150 flex items-center space-x-4">
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">En retard</p>
                <h3 class="text-lg font-bold text-slate-800 mt-0.5">
                    {{ \App\Models\Relance::whereDate('date_relance', '<', today())->where('statut', 'En attente')->when(!auth()->user()->hasRole('Administrateur'), function($q) { $q->where('commercial_id', auth()->id()); })->count() }}
                </h3>
            </div>
        </a>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('relances.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @if(request()->has('filter'))
                <input type="hidden" name="filter" value="{{ request('filter') }}">
            @endif

            <!-- Filter Statut -->
            <div>
                <label for="statut" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Statut</label>
                <select name="statut" id="statut" 
                        class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                    <option value="">Tous les statuts</option>
                    <option value="En attente" {{ request('statut') == 'En attente' ? 'selected' : '' }}>En attente</option>
                    <option value="Réalisée" {{ request('statut') == 'Réalisée' ? 'selected' : '' }}>Réalisée</option>
                    <option value="Annulée" {{ request('statut') == 'Annulée' ? 'selected' : '' }}>Annulée</option>
                </select>
            </div>

            <!-- Filter Canal -->
            <div>
                <label for="canal" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Canal</label>
                <select name="canal" id="canal" 
                        class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                    <option value="">Tous les canaux</option>
                    <option value="Appel" {{ request('canal') == 'Appel' ? 'selected' : '' }}>Appel</option>
                    <option value="WhatsApp" {{ request('canal') == 'WhatsApp' ? 'selected' : '' }}>WhatsApp</option>
                    <option value="Email" {{ request('canal') == 'Email' ? 'selected' : '' }}>Email</option>
                    <option value="SMS" {{ request('canal') == 'SMS' ? 'selected' : '' }}>SMS</option>
                    <option value="Rendez-vous" {{ request('canal') == 'Rendez-vous' ? 'selected' : '' }}>Rendez-vous</option>
                </select>
            </div>

            <!-- Filter Prospect -->
            <div>
                <label for="prospect_id" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Prospect associé</label>
                <select name="prospect_id" id="prospect_id" 
                        class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                    <option value="">Tous les prospects</option>
                    @foreach($prospects as $prospect)
                        <option value="{{ $prospect->id }}" {{ request('prospect_id') == $prospect->id ? 'selected' : '' }}>
                            {{ $prospect->nom }} {{ $prospect->prenom }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Commercial (Admin only) -->
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
                @if(request()->anyFilled(['statut', 'canal', 'prospect_id', 'commercial_id']))
                    <a href="{{ route('relances.index', request()->only('filter')) }}" class="px-3 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all h-[38px] flex items-center justify-center">
                        Effacer
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Relances Schedule Table -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Prospect</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Commercial responsable</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Date & Heure</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Canal</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Commentaire</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($relances as $relance)
                        <tr class="hover:bg-slate-50/30 transition-colors duration-150">
                            <!-- Prospect identity -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($relance->prospect)
                                    <div>
                                        <a href="{{ route('prospects.show', $relance->prospect) }}" class="font-semibold text-slate-800 text-sm hover:underline">
                                            {{ $relance->prospect->prenom }} {{ $relance->prospect->nom }}
                                        </a>
                                        <div class="text-[10px] text-slate-400 mt-0.5">
                                            {{ $relance->prospect->entreprise ?? 'Particulier' }}
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-400 text-xs italic">Prospect supprimé</span>
                                @endif
                            </td>
                            <!-- Commercial -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-slate-700 text-xs font-semibold">{{ $relance->commercial->name ?? 'Non assigné' }}</div>
                            </td>
                            <!-- Date / Heure -->
                            <td class="px-6 py-4 whitespace-nowrap text-xs">
                                <div class="font-semibold text-slate-800">
                                    {{ $relance->date_relance ? $relance->date_relance->format('d/m/Y') : '' }}
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5 font-medium">
                                    {{ $relance->heure_relance ? \Carbon\Carbon::parse($relance->heure_relance)->format('H:i') : 'Pas d\'heure' }}
                                </div>
                            </td>
                            <!-- Canal Badge -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($relance->canal === 'Appel')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                                        📞 Appel
                                    </span>
                                @elseif($relance->canal === 'WhatsApp')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        💬 WhatsApp
                                    </span>
                                @elseif($relance->canal === 'Email')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        ✉️ Email
                                    </span>
                                @elseif($relance->canal === 'SMS')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-violet-50 text-violet-700 border border-violet-100">
                                        📱 SMS
                                    </span>
                                @elseif($relance->canal === 'Rendez-vous')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-100">
                                        🤝 RDV
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-slate-50 text-slate-700 border border-slate-200">
                                        ❓ Autre
                                    </span>
                                @endif
                            </td>
                            <!-- Commentaire -->
                            <td class="px-6 py-4 max-w-xs">
                                <div class="text-xs text-slate-500 line-clamp-1" title="{{ $relance->commentaire }}">
                                    {{ $relance->commentaire ?? '—' }}
                                </div>
                            </td>
                            <!-- Statut Badge -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($relance->statut === 'Réalisée')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                        Réalisée
                                    </span>
                                @elseif($relance->statut === 'Annulée')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400 mr-1.5"></span>
                                        Annulée
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-50 text-yellow-750 border border-yellow-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1.5"></span>
                                        En attente
                                    </span>
                                @endif
                            </td>
                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-semibold space-x-1">
                                @if($relance->statut === 'En attente')
                                    <form action="{{ route('relances.update', $relance) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="prospect_id" value="{{ $relance->prospect_id }}">
                                        <input type="hidden" name="commercial_id" value="{{ $relance->commercial_id }}">
                                        <input type="hidden" name="date_relance" value="{{ $relance->date_relance ? $relance->date_relance->format('Y-m-d') : '' }}">
                                        <input type="hidden" name="canal" value="{{ $relance->canal }}">
                                        <input type="hidden" name="statut" value="Réalisée">
                                        <button type="submit" class="inline-flex items-center justify-center p-2 text-emerald-600 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors" title="Marquer comme réalisée">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('relances.show', $relance) }}" class="inline-flex items-center justify-center p-2 text-slate-600 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 rounded-lg transition-colors" title="Détails">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('relances.edit', $relance) }}" class="inline-flex items-center justify-center p-2 text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors" title="Éditer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('relances.destroy', $relance) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette planification de relance ?');">
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Aucune relance programmée.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        @if($relances->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $relances->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
