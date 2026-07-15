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
                    <p class="text-sm text-slate-500 mt-1">Détails, publications et analyse de rentabilité par canal de communication.</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('campagnes.edit', $campagne) }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl shadow-sm transition-all duration-150">
                    Modifier la campagne
                </a>
            </div>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center space-x-3 text-emerald-800 text-sm font-semibold shadow-sm">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-100 text-rose-800 text-sm font-semibold shadow-sm space-y-1">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Erreur lors de l'enregistrement de la publication :</span>
            </div>
            <ul class="list-disc list-inside text-xs font-normal pl-7">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div x-data="{ openCreateModal: false, openEditModal: false, editPub: { id: '', titre: '', canal: '', budget: '', date_publication: '', url_support: '', statut: 'active' } }" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
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
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Budget global de la campagne</span>
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

        <!-- Statistiques, Analyses & Gestion des Publications -->
        <div class="lg:col-span-2 space-y-6">
            <!-- 4 Cartes de statistiques Premium -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center space-x-3.5">
                    <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Prospects total</span>
                        <span class="text-xl font-bold text-slate-800 block mt-0.5">{{ $campagne->prospects->count() }}</span>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center space-x-3.5">
                    <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Ventes générées</span>
                        <span class="text-xl font-bold text-slate-800 block mt-0.5">
                            {{ $campagne->prospects->filter(fn($p) => $p->client !== null && $p->client->ventes->count() > 0)->count() }}
                        </span>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl border border-amber-200/60 shadow-sm p-5 flex items-center space-x-3.5 relative overflow-hidden">
                    <div class="p-3 bg-amber-500/10 text-amber-600 rounded-xl">
                        <span class="text-xl">🏆</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <span class="text-[10px] font-bold text-amber-700/80 uppercase tracking-wider block truncate">Top Canal (Leads)</span>
                        <span class="text-base font-extrabold text-amber-900 block mt-0.5 truncate">
                            {{ $meilleurCanalProspects ? $meilleurCanalProspects->canal : 'N/A' }}
                        </span>
                        <span class="text-[10px] font-semibold text-amber-700 block">
                            {{ $meilleurCanalProspects ? $meilleurCanalProspects->prospects_count . ' prospect(s)' : '-' }}
                        </span>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl border border-indigo-200/60 shadow-sm p-5 flex items-center space-x-3.5 relative overflow-hidden">
                    <div class="p-3 bg-indigo-500/10 text-indigo-600 rounded-xl">
                        <span class="text-xl">💎</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <span class="text-[10px] font-bold text-indigo-700/80 uppercase tracking-wider block truncate">Top Rentabilité (CA)</span>
                        <span class="text-base font-extrabold text-indigo-900 block mt-0.5 truncate">
                            {{ $meilleurCanalCA ? $meilleurCanalCA->canal : 'N/A' }}
                        </span>
                        <span class="text-[10px] font-semibold text-indigo-700 block truncate">
                            {{ $meilleurCanalCA ? number_format($meilleurCanalCA->chiffre_affaires, 0, ',', ' ') . ' xof' : '-' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Analyse comparative d'efficacité par Moyen / Canal -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center space-x-2">
                            <span>📊 Analyse d'efficacité par Moyen / Canal</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Comparatif détaillé du rendement et de la conversion pour chaque canal de communication.</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                <th class="py-3.5 px-6">Canal / Moyen</th>
                                <th class="py-3.5 px-4 text-center">Publications</th>
                                <th class="py-3.5 px-4 text-center">Prospects</th>
                                <th class="py-3.5 px-4 text-center">Conversions</th>
                                <th class="py-3.5 px-4 text-right">Budget dépensé</th>
                                <th class="py-3.5 px-4 text-right">CA Généré</th>
                                <th class="py-3.5 px-6 text-center">Efficacité</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                            @forelse($statsParCanal as $stat)
                                <tr class="hover:bg-slate-50/60 transition-colors duration-150">
                                    <td class="py-3.5 px-6 font-bold text-slate-900 flex items-center space-x-2">
                                        <span class="w-2 h-2 rounded-full {{ $stat->prospects_count > 0 ? 'bg-indigo-600' : 'bg-slate-300' }}"></span>
                                        <span>{{ $stat->canal }}</span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                            {{ $stat->nombre_publications }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-bold text-slate-800">
                                        {{ $stat->prospects_count }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="font-bold text-emerald-600">{{ $stat->conversions_count }}</span>
                                        <span class="text-[10px] text-slate-400 font-normal block">({{ $stat->taux_conversion }}%)</span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right text-slate-600">
                                        {{ $stat->budget > 0 ? number_format($stat->budget, 0, ',', ' ') . ' xof' : '-' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-bold text-slate-900">
                                        {{ number_format($stat->chiffre_affaires, 0, ',', ' ') }} xof
                                    </td>
                                    <td class="py-3.5 px-6 text-center">
                                        @if($meilleurCanalProspects && $meilleurCanalProspects->canal === $stat->canal && $stat->prospects_count > 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                                🏆 Top Leads
                                            </span>
                                        @elseif($meilleurCanalCA && $meilleurCanalCA->canal === $stat->canal && $stat->chiffre_affaires > 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                                                💎 Top Rentabilité
                                            </span>
                                        @elseif($stat->prospects_count > 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600">
                                                Actif
                                            </span>
                                        @else
                                            <span class="text-[10px] text-slate-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-slate-400 italic">
                                        Aucune donnée statistique disponible pour le moment.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Publications & Actions Marketing de la campagne -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/30">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center space-x-2">
                            <span>📢 Publications & Moyens de communication</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Gérez les différentes actions ou supports publicitaires liés à cette campagne.</p>
                    </div>
                    <button type="button" @click="openCreateModal = true" class="inline-flex items-center justify-center px-3.5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition-all duration-150 flex-shrink-0">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        + Nouvelle publication
                    </button>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($campagne->publications as $pub)
                        <div class="p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:bg-slate-50/50 transition-colors duration-150">
                            <div class="space-y-1.5 flex-1">
                                <div class="flex items-center space-x-2.5 flex-wrap gap-y-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        {{ $pub->canal }}
                                    </span>
                                    <h4 class="font-bold text-slate-900 text-sm">{{ $pub->titre }}</h4>
                                    @if($pub->statut !== 'active')
                                        <span class="px-2 py-0.2 rounded text-[10px] bg-slate-200 text-slate-600 font-semibold uppercase">{{ $pub->statut }}</span>
                                    @endif
                                </div>
                                <div class="flex items-center space-x-4 text-xs text-slate-500 flex-wrap gap-y-1">
                                    @if($pub->date_publication)
                                        <span class="flex items-center">
                                            <svg class="w-3.5 h-3.5 mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            {{ $pub->date_publication->format('d/m/Y') }}
                                        </span>
                                    @endif
                                    @if($pub->budget)
                                        <span class="flex items-center font-medium text-slate-700">
                                            💰 Budget: {{ number_format($pub->budget, 0, ',', ' ') }} xof
                                        </span>
                                    @endif
                                    @if($pub->url_support)
                                        <a href="{{ $pub->url_support }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 font-semibold inline-flex items-center underline">
                                            Lien support
                                            <svg class="w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center justify-between md:justify-end space-x-6 pt-3 md:pt-0 border-t md:border-t-0 border-slate-100">
                                <div class="text-right">
                                    <div class="text-xs font-bold text-slate-800">
                                        {{ $pub->prospects->count() }} prospect(s) · <span class="text-emerald-600">{{ $pub->conversions_count }} vente(s)</span>
                                    </div>
                                    <div class="text-[11px] font-semibold text-slate-500">
                                        CA: {{ number_format($pub->chiffre_affaires, 0, ',', ' ') }} xof
                                    </div>
                                </div>

                                <div class="flex items-center space-x-2">
                                    <button type="button" 
                                            @click="editPub = { id: {{ $pub->id }}, titre: '{{ addslashes($pub->titre) }}', canal: '{{ addslashes($pub->canal) }}', budget: '{{ $pub->budget }}', date_publication: '{{ $pub->date_publication ? $pub->date_publication->format('Y-m-d') : '' }}', url_support: '{{ addslashes($pub->url_support ?? '') }}', statut: '{{ $pub->statut }}' }; openEditModal = true"
                                            class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Modifier">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <form action="{{ route('campagnes.publications.destroy', [$campagne, $pub]) }}" method="POST" onsubmit="return confirm('Supprimer définitivement cette publication ? Les prospects associés resteront dans la campagne mais ne seront plus liés à cette publication.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Supprimer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center space-y-3">
                            <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto text-slate-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                            </div>
                            <div class="text-xs text-slate-500 font-medium">
                                Aucune publication ni moyen spécifique enregistré pour cette campagne.
                            </div>
                            <button type="button" @click="openCreateModal = true" class="inline-flex items-center px-3.5 py-2 text-xs font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition-colors">
                                + Ajouter le premier moyen / publication
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Liste des prospects issus de la campagne avec badge Publication -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Prospects acquis ({{ $campagne->prospects->count() }})</h3>
                        <p class="text-xs text-slate-400 font-medium">Liste complète des prospects rattachés à cette campagne et leur moyen d'origine.</p>
                    </div>
                </div>
                <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                    @forelse($campagne->prospects as $prospect)
                        <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50/50 transition-colors duration-150">
                            <div>
                                <div class="flex items-center space-x-2.5">
                                    <a href="{{ route('prospects.show', $prospect) }}" class="font-bold text-slate-800 hover:text-indigo-600 text-sm">
                                        {{ $prospect->nom }} {{ $prospect->prenom }}
                                    </a>
                                    @if($prospect->publication)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                            📌 {{ $prospect->publication->canal }} : {{ Str::limit($prospect->publication->titre, 25) }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium bg-slate-100 text-slate-600">
                                            Direct / Non attribué
                                        </span>
                                    @endif
                                </div>
                                <span class="text-xs text-slate-400 block mt-0.5">
                                    Société: <strong class="text-slate-600">{{ $prospect->entreprise ?? 'N/A' }}</strong> · Tél: {{ $prospect->telephone ?? 'N/A' }}
                                </span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-50 text-slate-700 border border-slate-200">
                                    {{ $prospect->statut }}
                                </span>
                                <a href="{{ route('prospects.edit', $prospect) }}" class="text-slate-400 hover:text-slate-600 p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </a>
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

        <!-- MODALE CREATION DE PUBLICATION (Alpine.js) -->
        <div x-show="openCreateModal" 
             x-transition:enter="transition ease-out duration-200" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="transition ease-in duration-150" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" 
             style="display: none;">
            <div @click.away="openCreateModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 space-y-5">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Nouvelle publication / moyen</h3>
                        <p class="text-xs text-slate-500">Ajouter une action marketing liée à {{ $campagne->nom }}</p>
                    </div>
                    <button @click="openCreateModal = false" type="button" class="text-slate-400 hover:text-slate-600 p-1 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form action="{{ route('campagnes.publications.store', $campagne) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Titre / Description courte *</label>
                        <input type="text" name="titre" required placeholder="ex: Post LinkedIn #1 - Offre Spéciale" class="block w-full text-sm rounded-xl border-slate-200 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Canal / Moyen *</label>
                            <input type="text" name="canal" list="canaux-list" required placeholder="ex: LinkedIn, Emailing..." class="block w-full text-sm rounded-xl border-slate-200 focus:ring-indigo-500 focus:border-indigo-500">
                            <datalist id="canaux-list">
                                <option value="LinkedIn">
                                <option value="Facebook">
                                <option value="Instagram">
                                <option value="Google Ads">
                                <option value="Emailing">
                                <option value="Affichage physique">
                                <option value="Salon / Événement">
                                <option value="Presse / Radio">
                                <option value="Autre">
                            </datalist>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Budget spécifique (XOF)</label>
                            <input type="number" step="0.01" name="budget" placeholder="ex: 50000" class="block w-full text-sm rounded-xl border-slate-200 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Date de publication</label>
                            <input type="date" name="date_publication" class="block w-full text-sm rounded-xl border-slate-200 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Statut</label>
                            <select name="statut" class="block w-full text-sm rounded-xl border-slate-200 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">URL / Lien support (Optionnel)</label>
                        <input type="url" name="url_support" placeholder="https://..." class="block w-full text-sm rounded-xl border-slate-200 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="openCreateModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                            Annuler
                        </button>
                        <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition-all">
                            Enregistrer la publication
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODALE MODIFICATION DE PUBLICATION (Alpine.js) -->
        <div x-show="openEditModal" 
             x-transition:enter="transition ease-out duration-200" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="transition ease-in duration-150" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" 
             style="display: none;">
            <div @click.away="openEditModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 space-y-5">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Modifier la publication</h3>
                        <p class="text-xs text-slate-500">Mettre à jour les informations du support</p>
                    </div>
                    <button @click="openEditModal = false" type="button" class="text-slate-400 hover:text-slate-600 p-1 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form :action="`{{ url('campagnes/' . $campagne->id . '/publications') }}/${editPub.id}`" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Titre / Description courte *</label>
                        <input type="text" name="titre" x-model="editPub.titre" required class="block w-full text-sm rounded-xl border-slate-200 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Canal / Moyen *</label>
                            <input type="text" name="canal" x-model="editPub.canal" list="canaux-list-edit" required class="block w-full text-sm rounded-xl border-slate-200 focus:ring-indigo-500 focus:border-indigo-500">
                            <datalist id="canaux-list-edit">
                                <option value="LinkedIn">
                                <option value="Facebook">
                                <option value="Instagram">
                                <option value="Google Ads">
                                <option value="Emailing">
                                <option value="Affichage physique">
                                <option value="Salon / Événement">
                                <option value="Presse / Radio">
                                <option value="Autre">
                            </datalist>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Budget spécifique (XOF)</label>
                            <input type="number" step="0.01" name="budget" x-model="editPub.budget" class="block w-full text-sm rounded-xl border-slate-200 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Date de publication</label>
                            <input type="date" name="date_publication" x-model="editPub.date_publication" class="block w-full text-sm rounded-xl border-slate-200 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Statut</label>
                            <select name="statut" x-model="editPub.statut" class="block w-full text-sm rounded-xl border-slate-200 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">URL / Lien support (Optionnel)</label>
                        <input type="url" name="url_support" x-model="editPub.url_support" class="block w-full text-sm rounded-xl border-slate-200 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="openEditModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                            Annuler
                        </button>
                        <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition-all">
                            Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
