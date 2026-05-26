<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
            <div class="flex items-center space-x-3">
                <a href="{{ route('campagnes.index') }}" class="inline-flex items-center justify-center p-2 text-slate-500 hover:text-slate-700 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 shadow-sm transition-all duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $campagne->nom }}</h1>
                    <p class="text-sm text-slate-500 mt-1">Détails et indicateurs de performance de la campagne.</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('campagnes.edit', $campagne) }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl shadow-sm transition-all duration-150">
                    Modifier
                </a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations Générales de la Campagne -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6 lg:col-span-1 h-fit">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-extrabold text-lg border border-indigo-100 shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800">{{ $campagne->nom }}</h3>
                    <div class="mt-1">
                        @if($campagne->statut === 'actif')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                Inactive
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-100 pt-6 space-y-4">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Filiale</span>
                    <span class="text-xs text-slate-600 font-semibold block mt-0.5">{{ $campagne->filiale->nom ?? 'Non définie' }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Budget de la campagne</span>
                    <span class="text-xs text-slate-700 font-bold block mt-0.5">
                        {{ $campagne->budget ? number_format($campagne->budget, 2, ',', ' ') . ' xof' : 'Non défini' }}
                    </span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Dates d'activation</span>
                    <span class="text-xs text-slate-600 font-medium block mt-0.5">
                        Du {{ $campagne->date_debut ? $campagne->date_debut->format('d/m/Y') : 'Non définie' }} 
                        au {{ $campagne->date_fin ? $campagne->date_fin->format('d/m/Y') : 'Non définie' }}
                    </span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Description</span>
                    <p class="text-xs text-slate-600 font-medium mt-1 leading-relaxed">{{ $campagne->description ?? 'Aucune description fournie.' }}</p>
                </div>
            </div>
        </div>

        <!-- Statistiques et listes associées -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Statistiques de conversion de la campagne -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex items-center space-x-4">
                    <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Prospects acquis</span>
                        <span class="text-xl font-bold text-slate-800 block mt-0.5">{{ $campagne->prospects()->count() }}</span>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex items-center space-x-4">
                    <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Ventes générées</span>
                        <span class="text-xl font-bold text-slate-800 block mt-0.5">
                            {{ $campagne->prospects()->whereHas('client.ventes')->count() }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Liste des prospects issus de la campagne -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Prospects acquis</h3>
                    <span class="text-xs text-slate-400 font-medium">Acquisitions récentes</span>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($campagne->prospects()->latest()->take(5)->get() as $prospect)
                        <div class="px-6 py-4 flex items-center justify-between hover:bg-slate-50/50 transition-colors duration-150">
                            <div>
                                <span class="font-semibold text-slate-700 text-sm block">{{ $prospect->nom }} {{ $prospect->prenom }}</span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">Société: {{ $prospect->entreprise ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-50 text-slate-700 border border-slate-100">
                                    {{ $prospect->statut }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center text-slate-400 text-xs italic">
                            Aucun prospect généré par cette campagne pour le moment.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
