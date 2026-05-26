<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Tableau de bord</h1>
                <p class="text-sm text-slate-500 mt-1">Aperçu en temps réel des performances de votre CRM commercial.</p>
            </div>
            <!-- Quick Actions Buttons -->
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('prospects.create') }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl shadow-md shadow-indigo-600/10 hover:shadow-indigo-600/20 transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Nouveau Prospect
                </a>
                <a href="{{ route('ventes.create') }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 active:bg-slate-100 rounded-xl border border-slate-200 shadow-sm transition-all duration-150">
                    <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Nouvelle Vente
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Card 1: Prospects -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition-all duration-200 flex items-center space-x-5">
            <div class="p-3.5 rounded-xl bg-indigo-50 text-indigo-600 shadow-sm shadow-indigo-100/50">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Prospects</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($stats['prospects_count']) }}</h3>
                <p class="text-xs text-slate-400 mt-0.5">Opportunités actives</p>
            </div>
        </div>

        <!-- Card 2: Clients -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition-all duration-200 flex items-center space-x-5">
            <div class="p-3.5 rounded-xl bg-emerald-50 text-emerald-600 shadow-sm shadow-emerald-100/50">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Clients</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($stats['clients_count']) }}</h3>
                <p class="text-xs text-slate-400 mt-0.5">Comptes convertis</p>
            </div>
        </div>

        <!-- Card 3: Chiffre d'Affaires -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition-all duration-200 flex items-center space-x-5">
            <div class="p-3.5 rounded-xl bg-amber-50 text-amber-600 shadow-sm shadow-amber-100/50">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Chiffre d'Affaires</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($stats['ventes_sum'], 0, ',', ' ') }} €</h3>
                <p class="text-xs text-slate-400 mt-0.5">Total des ventes</p>
            </div>
        </div>

        <!-- Card 4: Produits -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition-all duration-200 flex items-center space-x-5">
            <div class="p-3.5 rounded-xl bg-sky-50 text-sky-600 shadow-sm shadow-sky-100/50">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Catalogue</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($stats['produits_count']) }}</h3>
                <p class="text-xs text-slate-400 mt-0.5">Produits actifs</p>
            </div>
        </div>
    </div>

    <!-- Main Content Tables Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Prospects Récents Table Card -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div>
                    <h2 class="text-md font-bold text-slate-900">Prospects Récents</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Dernières opportunités créées.</p>
                </div>
                <a href="{{ route('prospects.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 transition-colors">Voir tout</a>
            </div>
            <div class="flex-1 overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Prospect</th>
                            <th class="px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Entreprise</th>
                            <th class="px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Commercial</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($stats['recent_prospects'] as $prospect)
                            <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-800 text-sm">{{ $prospect->prenom }} {{ $prospect->nom }}</div>
                                    <div class="text-xs text-slate-400">{{ $prospect->email }}</div>
                                </td>
                                <td class="px-6 py-4 text-slate-600 text-sm font-medium">{{ $prospect->entreprise ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusColors = [
                                            'nouveau' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                                            'contacte' => 'bg-amber-50 text-amber-700 border-amber-100',
                                            'qualifie' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                            'perdu' => 'bg-rose-50 text-rose-700 border-rose-100',
                                        ];
                                        $color = $statusColors[strtolower($prospect->statut)] ?? 'bg-slate-50 text-slate-700 border-slate-100';
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold border {{ $color }}">
                                        {{ ucfirst($prospect->statut) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs font-semibold text-slate-600">
                                    {{ $prospect->commercial?->name ?? 'Non assigné' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-slate-400 text-sm">
                                    <svg class="w-8.5 h-8.5 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    Aucun prospect pour le moment.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Ventes Récentes Table Card -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div>
                    <h2 class="text-md font-bold text-slate-900">Ventes Récentes</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Dernières transactions enregistrées.</p>
                </div>
                <a href="{{ route('ventes.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 transition-colors">Voir tout</a>
            </div>
            <div class="flex-1 overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Produit</th>
                            <th class="px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Montant</th>
                            <th class="px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($stats['recent_ventes'] as $vente)
                            <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-800 text-sm">
                                        {{ $vente->client?->entreprise ?? ($vente->client?->prenom . ' ' . $vente->client?->nom) }}
                                    </div>
                                    <div class="text-xs text-slate-400">{{ $vente->commercial?->name ?? 'CRM' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-slate-700 text-sm font-semibold">{{ $vente->produit?->nom ?? 'Produit Supprimé' }}</div>
                                    <div class="text-xs text-slate-400">Qté: {{ $vente->quantite }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-bold text-slate-900 text-sm">
                                        {{ number_format($vente->montant, 0, ',', ' ') }} €
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-500">
                                    {{ $vente->date_vente ? $vente->date_vente->format('d/m/Y') : $vente->created_at->format('d/m/Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-slate-400 text-sm">
                                    <svg class="w-8.5 h-8.5 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Aucune vente pour le moment.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
