<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                    Bonjour, {{ Auth::user()->prenom ?? Auth::user()->nom }} 👋
                </h2>
                <p class="text-sm text-slate-500 mt-1">Voici le résumé de vos performances — <span class="font-semibold text-indigo-600">{{ now()->translatedFormat('F Y') }}</span></p>
            </div>
        </div>
    </x-slot>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 1: KPI Cards                                       --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div class="mb-8 space-y-4">
        {{-- KPI 1: Chiffre d'affaires du mois (Pleine largeur) --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold text-slate-700">CA du mois</p>
                <div class="flex items-baseline gap-2 mt-2">
                    <span class="text-3xl font-extrabold text-slate-900 tabular-nums">{{ number_format($stats['ca_mois'], 0, ',', ' ') }}</span>
                    <span class="text-sm font-semibold text-slate-700">XOF</span>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    @if($stats['ventes_conclues'] > 0)
                        {{ $stats['ventes_conclues'] }} {{ Str::plural('vente conclue', $stats['ventes_conclues']) }} ce mois-ci
                    @else
                        Aucune vente conclue ce mois-ci
                    @endif
                </p>
            </div>
            <div class="w-12 h-12 bg-emerald-100/60 rounded-2xl flex items-center justify-center text-emerald-600 flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            </div>
        </div>

        {{-- Grille 4 colonnes de 8 cartes --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Card 1: Prospects gérés --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                <div class="flex items-center gap-2.5 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <span class="text-sm font-semibold text-slate-700">Prospects gérés</span>
                </div>
                <p class="text-2xl font-bold text-slate-900">{{ number_format($stats['prospects_count'], 0, ',', ' ') }}</p>
                <p class="text-xs text-slate-500 mt-1">dont <span class="font-bold text-blue-600">{{ $stats['prospects_mois'] }}</span> ce mois</p>
            </div>

            {{-- Card 2: Taux de conversion --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                <div class="flex items-center gap-2.5 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    </div>
                    <span class="text-sm font-semibold text-slate-700">Taux de conversion</span>
                </div>
                <p class="text-2xl font-bold text-slate-900">{{ $stats['taux_conversion'] }}%</p>
                <p class="text-xs text-slate-500 mt-1">
                    @if($stats['conversion_tendance'] != 0)
                        <span class="{{ $stats['conversion_tendance'] > 0 ? 'text-emerald-600 font-semibold' : 'text-rose-600 font-semibold' }}">{{ $stats['conversion_tendance'] > 0 ? '+' : '' }}{{ $stats['conversion_tendance'] }}%</span> vs mois préc.
                    @else
                        stable
                    @endif
                </p>
            </div>

            {{-- Card 3: Appels effectués --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                <div class="flex items-center gap-2.5 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    </div>
                    <span class="text-sm font-semibold text-slate-700">Appels effectués</span>
                </div>
                <p class="text-2xl font-bold text-slate-900">{{ number_format($stats['appels_mois'], 0, ',', ' ') }}</p>
                <p class="text-xs text-slate-500 mt-1">ce mois-ci</p>
            </div>

            {{-- Card 4: RDV réalisés --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                <div class="flex items-center gap-2.5 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <span class="text-sm font-semibold text-slate-700">RDV réalisés</span>
                </div>
                <p class="text-2xl font-bold text-slate-900">{{ number_format($stats['rdv_mois'], 0, ',', ' ') }}</p>
                <p class="text-xs text-slate-500 mt-1">ce mois-ci</p>
            </div>

            {{-- Card 5: Devis envoyés --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                <div class="flex items-center gap-2.5 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <span class="text-sm font-semibold text-slate-700">Devis envoyés</span>
                </div>
                <p class="text-2xl font-bold text-slate-900">{{ number_format($stats['devis_mois'], 0, ',', ' ') }}</p>
                <p class="text-xs text-slate-500 mt-1">en négociation</p>
            </div>

            {{-- Card 6: Ventes conclues --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                <div class="flex items-center gap-2.5 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-sm font-semibold text-slate-700">Ventes conclues</span>
                </div>
                <p class="text-2xl font-bold text-slate-900">{{ number_format($stats['ventes_conclues'], 0, ',', ' ') }}</p>
                <p class="text-xs text-slate-500 mt-1">ce mois-ci</p>
            </div>

            {{-- Card 7: Rétention clients --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                <div class="flex items-center gap-2.5 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-pink-50 text-pink-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                    </div>
                    <span class="text-sm font-semibold text-slate-700">Rétention clients</span>
                </div>
                <p class="text-2xl font-bold text-slate-900">{{ $stats['satisfaction'] }}%</p>
                <p class="text-xs text-slate-500 mt-1">{{ $stats['clients_actifs'] }} actifs / {{ $stats['clients_total'] }} total</p>
            </div>

            {{-- Card 8: Objectif (Pointillée avec bouton) --}}
            <div class="border border-dashed border-slate-200 rounded-2xl p-5 flex flex-col items-center justify-center text-center bg-white shadow-sm">
                <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center mb-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"></circle><circle cx="12" cy="12" r="6" stroke-width="2"></circle><circle cx="12" cy="12" r="2" stroke-width="2"></circle></svg>
                </div>
                @if($stats['objectif_montant'] > 0)
                    <p class="text-xs font-semibold text-slate-600 mb-1">Objectif : {{ number_format($stats['objectif_montant'], 0, ',', ' ') }} XOF</p>
                    <p class="text-[11px] font-bold text-emerald-600 mb-2">{{ $stats['objectif_pct'] }}% atteint</p>
                    <a href="{{ route('objectifs.index') }}" class="w-full px-3 py-1.5 text-xs font-semibold text-slate-800 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl shadow-sm flex items-center justify-center gap-1 transition-all">
                        Modifier l'objectif
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17L17 7M17 7H7M17 7v10"></path></svg>
                    </a>
                @else
                    <p class="text-xs font-semibold text-slate-600 mb-2">Aucun objectif défini</p>
                    <a href="{{ route('objectifs.index') }}" class="w-full px-3 py-1.5 text-xs font-semibold text-slate-800 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl shadow-sm flex items-center justify-center gap-1 transition-all">
                        Définir un objectif
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17L17 7M17 7H7M17 7v10"></path></svg>
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 2: Graphiques (2 colonnes)                         --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Graphique Évolution mensuelle (2/3) --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">📊 Évolution Mensuelle</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Performances sur les 6 derniers mois</p>
                </div>
                <div class="flex items-center gap-2 text-[10px] font-semibold">
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>CA</span>
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-blue-400"></span>Prospects</span>
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>Conversions</span>
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>Appels</span>
                </div>
            </div>
            <div class="h-72">
                <canvas id="evolutionChart"></canvas>
            </div>
        </div>

        {{-- Graphique Répartition Prospects (1/3) --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <div class="mb-6">
                <h3 class="text-lg font-bold text-slate-800">👥 Répartition Prospects</h3>
                <p class="text-xs text-slate-400 mt-0.5">Par statut actuel</p>
            </div>
            <div class="h-72">
                <canvas id="prospectsChart"></canvas>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 3: Classement des commerciaux                      --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @if(count($stats['classement']) > 0)
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-8">
        <div class="p-6 border-b border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">🏆 Classement des Commerciaux</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Basé sur la qualité du suivi — Score pondéré sur 5 critères</p>
                </div>
                <div class="hidden sm:flex items-center gap-4 text-[10px] font-semibold text-slate-500">
                    <span>Relances 30%</span>
                    <span>Conversion 25%</span>
                    <span>Tâches 20%</span>
                    <span>Interactions 15%</span>
                    <span>Complétude 10%</span>
                </div>
            </div>
        </div>

        {{-- Podium Top 3 --}}
        @if(count($stats['classement']) >= 3)
        <div class="px-6 py-8 bg-gradient-to-br from-slate-50 to-white border-b border-slate-100">
            <div class="flex items-end justify-center gap-4 sm:gap-8 max-w-lg mx-auto">
                {{-- 2ème place --}}
                <div class="flex flex-col items-center flex-1">
                    <div class="text-2xl mb-1">🥈</div>
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-slate-300 to-slate-400 text-white flex items-center justify-center font-bold text-sm shadow-md mb-2">
                        {{ strtoupper(substr($stats['classement'][1]['user']->name, 0, 2)) }}
                    </div>
                    <p class="text-xs font-bold text-slate-700 text-center truncate w-full">{{ $stats['classement'][1]['user']->name }}</p>
                    <p class="text-lg font-extrabold text-slate-500">{{ $stats['classement'][1]['score_global'] }}</p>
                    <div class="w-full bg-slate-200 rounded-full h-1.5 mt-1">
                        <div class="bg-slate-400 h-1.5 rounded-full" style="width: {{ $stats['classement'][1]['score_global'] }}%"></div>
                    </div>
                </div>

                {{-- 1ère place --}}
                <div class="flex flex-col items-center flex-1 -mb-2">
                    <div class="text-3xl mb-1 animate-bounce">🥇</div>
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 text-white flex items-center justify-center font-bold text-base shadow-lg shadow-amber-500/30 mb-2 ring-2 ring-amber-300/50">
                        {{ strtoupper(substr($stats['classement'][0]['user']->name, 0, 2)) }}
                    </div>
                    <p class="text-sm font-extrabold text-slate-800 text-center truncate w-full">{{ $stats['classement'][0]['user']->name }}</p>
                    <p class="text-xl font-extrabold bg-gradient-to-r from-amber-500 to-amber-700 bg-clip-text text-transparent">{{ $stats['classement'][0]['score_global'] }}</p>
                    <div class="w-full bg-amber-200 rounded-full h-2 mt-1">
                        <div class="bg-gradient-to-r from-amber-400 to-amber-600 h-2 rounded-full" style="width: {{ $stats['classement'][0]['score_global'] }}%"></div>
                    </div>
                </div>

                {{-- 3ème place --}}
                <div class="flex flex-col items-center flex-1">
                    <div class="text-2xl mb-1">🥉</div>
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-orange-300 to-orange-500 text-white flex items-center justify-center font-bold text-sm shadow-md mb-2">
                        {{ strtoupper(substr($stats['classement'][2]['user']->name, 0, 2)) }}
                    </div>
                    <p class="text-xs font-bold text-slate-700 text-center truncate w-full">{{ $stats['classement'][2]['user']->name }}</p>
                    <p class="text-lg font-extrabold text-orange-500">{{ $stats['classement'][2]['score_global'] }}</p>
                    <div class="w-full bg-orange-200 rounded-full h-1.5 mt-1">
                        <div class="bg-orange-400 h-1.5 rounded-full" style="width: {{ $stats['classement'][2]['score_global'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Tableau détaillé --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-slate-500 text-[10px] uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3 font-semibold">#</th>
                        <th class="px-6 py-3 font-semibold">Commercial</th>
                        <th class="px-6 py-3 font-semibold text-center">Score Global</th>
                        <th class="px-6 py-3 font-semibold text-center">Relances</th>
                        <th class="px-6 py-3 font-semibold text-center">Conversion</th>
                        <th class="px-6 py-3 font-semibold text-center">Tâches</th>
                        <th class="px-6 py-3 font-semibold text-center">Interactions</th>
                        <th class="px-6 py-3 font-semibold text-center">Complétude</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($stats['classement'] as $index => $entry)
                        <tr class="hover:bg-slate-50/80 transition-colors {{ $entry['user']->id === Auth::id() ? 'bg-indigo-50/50 ring-1 ring-inset ring-indigo-200/50' : '' }}">
                            <td class="px-6 py-3.5">
                                <span class="text-sm font-bold {{ $index === 0 ? 'text-amber-500' : ($index === 1 ? 'text-slate-400' : ($index === 2 ? 'text-orange-400' : 'text-slate-300')) }}">
                                    {{ $index + 1 }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg {{ $entry['user']->id === Auth::id() ? 'bg-indigo-600' : 'bg-slate-700' }} text-white flex items-center justify-center font-bold text-[10px] shadow-sm">
                                        {{ strtoupper(substr($entry['user']->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800">{{ $entry['user']->name }}</p>
                                        @if($entry['user']->id === Auth::id())
                                            <p class="text-[10px] text-indigo-500 font-semibold">Vous</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3.5 text-center">
                                <div class="flex flex-col items-center gap-1">
                                    <span class="text-sm font-extrabold {{ $entry['score_global'] >= 70 ? 'text-emerald-600' : ($entry['score_global'] >= 40 ? 'text-amber-600' : 'text-rose-600') }}">
                                        {{ $entry['score_global'] }}
                                    </span>
                                    <div class="w-16 bg-slate-100 rounded-full h-1">
                                        <div class="h-1 rounded-full {{ $entry['score_global'] >= 70 ? 'bg-emerald-500' : ($entry['score_global'] >= 40 ? 'bg-amber-500' : 'bg-rose-500') }}" style="width: {{ $entry['score_global'] }}%"></div>
                                    </div>
                                </div>
                            </td>
                            @foreach(['relance_score', 'conversion_score', 'task_score', 'interaction_score', 'completude_score'] as $scoreKey)
                                <td class="px-6 py-3.5 text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="text-xs font-bold text-slate-600">{{ $entry[$scoreKey] }}</span>
                                        <div class="w-12 bg-slate-100 rounded-full h-1">
                                            <div class="bg-indigo-400 h-1 rounded-full" style="width: {{ min($entry[$scoreKey], 100) }}%"></div>
                                        </div>
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 4: Derniers prospects & ventes                     --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Derniers Prospects --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center">
                <div>
                    <h3 class="text-base font-bold text-slate-800">Derniers Prospects</h3>
                    <p class="text-[11px] text-slate-400">Les 5 plus récents</p>
                </div>
                <a href="{{ route('prospects.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-bold bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition-all">
                    Voir tout →
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[10px] uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-2.5 font-semibold">Nom</th>
                            <th class="px-5 py-2.5 font-semibold">Statut</th>
                            <th class="px-5 py-2.5 font-semibold">Commercial</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($stats['recent_prospects'] as $prospect)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-5 py-3">
                                    <div class="text-sm font-bold text-slate-800">{{ $prospect->nom_complet }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $prospect->email }}</div>
                                </td>
                                <td class="px-5 py-3">
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full 
                                        @if($prospect->statut == 'Nouveau') bg-blue-100 text-blue-700
                                        @elseif($prospect->statut == 'Contacté') bg-sky-100 text-sky-700
                                        @elseif($prospect->statut == 'Qualifié') bg-violet-100 text-violet-700
                                        @elseif($prospect->statut == 'En négociation') bg-amber-100 text-amber-700
                                        @elseif($prospect->statut == 'Gagné') bg-emerald-100 text-emerald-700
                                        @elseif($prospect->statut == 'Perdu') bg-rose-100 text-rose-700
                                        @else bg-slate-100 text-slate-700 @endif uppercase">
                                        {{ $prospect->statut }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-xs text-slate-500 font-medium">
                                    {{ $prospect->commercial->name ?? 'N/A' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-8 text-center text-sm text-slate-400">Aucun prospect récent</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Dernières Ventes --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center">
                <div>
                    <h3 class="text-base font-bold text-slate-800">Dernières Ventes</h3>
                    <p class="text-[11px] text-slate-400">Les 5 plus récentes</p>
                </div>
                <a href="{{ route('ventes.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-bold bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition-all">
                    Voir tout →
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[10px] uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-2.5 font-semibold">Client / Produit</th>
                            <th class="px-5 py-2.5 font-semibold">Montant</th>
                            <th class="px-5 py-2.5 font-semibold">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($stats['recent_ventes'] as $vente)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-5 py-3">
                                    <div class="text-sm font-bold text-slate-800">{{ $vente->client->nom_complet ?? 'Client Inconnu' }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $vente->produit->nom ?? 'Produit Inconnu' }}</div>
                                </td>
                                <td class="px-5 py-3 text-sm font-bold text-emerald-600">
                                    {{ number_format($vente->montant, 0, ',', ' ') }} XOF
                                </td>
                                <td class="px-5 py-3 text-xs text-slate-500">
                                    {{ $vente->date_vente ? $vente->date_vente->format('d/m/Y') : $vente->created_at->format('d/m/Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-8 text-center text-sm text-slate-400">Aucune vente récente</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- Chart.js Scripts                                           --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js" nonce="{{ Vite::cspNonce() }}"></script>
    <script nonce="{{ Vite::cspNonce() }}">
        document.addEventListener('DOMContentLoaded', function() {

            // ──────── Graphique Évolution Mensuelle ────────
            const ctxEvolution = document.getElementById('evolutionChart').getContext('2d');
            
            // Gradient pour CA
            const caGradient = ctxEvolution.createLinearGradient(0, 0, 0, 280);
            caGradient.addColorStop(0, 'rgba(99, 102, 241, 0.15)');
            caGradient.addColorStop(1, 'rgba(99, 102, 241, 0.01)');

            new Chart(ctxEvolution, {
                type: 'line',
                data: {
                    labels: {!! json_encode($stats['evolution_labels']) !!},
                    datasets: [
                        {
                            label: 'Chiffre d\'Affaires (XOF)',
                            data: {!! json_encode($stats['evolution_ca']) !!},
                            borderColor: '#6366f1',
                            backgroundColor: caGradient,
                            fill: true,
                            tension: 0.4,
                            borderWidth: 2.5,
                            pointRadius: 4,
                            pointBackgroundColor: '#6366f1',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointHoverRadius: 6,
                            yAxisID: 'y',
                        },
                        {
                            label: 'Prospects',
                            data: {!! json_encode($stats['evolution_prospects']) !!},
                            borderColor: '#60a5fa',
                            backgroundColor: 'rgba(96, 165, 250, 0.05)',
                            fill: false,
                            tension: 0.4,
                            borderWidth: 2,
                            pointRadius: 3,
                            pointBackgroundColor: '#60a5fa',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            yAxisID: 'y1',
                        },
                        {
                            label: 'Conversions',
                            data: {!! json_encode($stats['evolution_conversions']) !!},
                            borderColor: '#34d399',
                            backgroundColor: 'rgba(52, 211, 153, 0.05)',
                            fill: false,
                            tension: 0.4,
                            borderWidth: 2,
                            pointRadius: 3,
                            pointBackgroundColor: '#34d399',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            yAxisID: 'y1',
                        },
                        {
                            label: 'Appels',
                            data: {!! json_encode($stats['evolution_appels']) !!},
                            borderColor: '#fbbf24',
                            backgroundColor: 'rgba(251, 191, 36, 0.05)',
                            fill: false,
                            tension: 0.4,
                            borderWidth: 2,
                            borderDash: [5, 5],
                            pointRadius: 3,
                            pointBackgroundColor: '#fbbf24',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            yAxisID: 'y1',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            titleColor: '#f8fafc',
                            bodyColor: '#cbd5e1',
                            borderColor: '#334155',
                            borderWidth: 1,
                            cornerRadius: 12,
                            padding: 12,
                            titleFont: { weight: 'bold', size: 13 },
                            bodyFont: { size: 12 },
                            boxPadding: 4,
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            beginAtZero: true,
                            grid: { color: 'rgba(148, 163, 184, 0.08)' },
                            ticks: {
                                font: { size: 10, weight: '600' },
                                color: '#94a3b8',
                                callback: function(value) {
                                    if (value >= 1000000) return (value / 1000000).toFixed(1) + 'M';
                                    if (value >= 1000) return (value / 1000).toFixed(0) + 'K';
                                    return value;
                                }
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            beginAtZero: true,
                            grid: { drawOnChartArea: false },
                            ticks: {
                                font: { size: 10, weight: '600' },
                                color: '#94a3b8',
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { size: 10, weight: '600' },
                                color: '#94a3b8',
                            }
                        }
                    }
                }
            });

            // ──────── Graphique Répartition Prospects ────────
            const ctxProspects = document.getElementById('prospectsChart').getContext('2d');
            new Chart(ctxProspects, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($stats['chart_prospects_labels']) !!},
                    datasets: [{
                        data: {!! json_encode($stats['chart_prospects_data']) !!},
                        backgroundColor: [
                            '#3b82f6', // Nouveau - blue
                            '#06b6d4', // Contacté - cyan
                            '#8b5cf6', // Qualifié - violet
                            '#f59e0b', // En négociation - amber
                            '#10b981', // Gagné - emerald
                            '#ef4444', // Perdu - red
                        ],
                        borderWidth: 0,
                        hoverOffset: 8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 12,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                font: { size: 11, weight: '600' },
                                color: '#64748b',
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            titleColor: '#f8fafc',
                            bodyColor: '#cbd5e1',
                            borderColor: '#334155',
                            borderWidth: 1,
                            cornerRadius: 12,
                            padding: 12,
                        }
                    }
                }
            });

            // ──────── Animations d'entrée (fade in) ────────
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.group').forEach((el, i) => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                el.style.transition = `all 0.5s cubic-bezier(0.4, 0, 0.2, 1) ${i * 80}ms`;
                observer.observe(el);
            });
        });
    </script>
    @endpush
</x-app-layout>
