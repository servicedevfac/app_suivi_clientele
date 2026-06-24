<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Tableau de bord') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Cartes de statistiques réelles -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Prospects -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center space-x-4">
                    <div class="p-3 bg-blue-50 rounded-xl text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Prospects</p>
                        <p class="text-2xl font-bold text-slate-900">{{ number_format($stats['prospects_count']) }}</p>
                    </div>
                </div>

                <!-- Clients -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center space-x-4">
                    <div class="p-3 bg-indigo-50 rounded-xl text-indigo-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Clients</p>
                        <p class="text-2xl font-bold text-slate-900">{{ number_format($stats['clients_count']) }}</p>
                    </div>
                </div>

                <!-- Ventes -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-center space-y-2">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Ventes du mois</p>
                            <p class="text-2xl font-bold text-slate-900">{{ number_format($stats['ventes_mois_sum'], 0, ',', ' ') }} XOF</p>
                        </div>
                    </div>
                    @if($stats['objectif_mois'])
                        @php
                            $progress = $stats['objectif_mois'] > 0 ? min(100, round(($stats['ventes_mois_sum'] / $stats['objectif_mois']) * 100)) : 0;
                        @endphp
                        <div class="w-full bg-slate-100 rounded-full h-2 mt-2">
                            <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $progress }}%"></div>
                        </div>
                        <p class="text-xs text-slate-500 text-right mt-1">Objectif : {{ number_format($stats['objectif_mois'], 0, ',', ' ') }} XOF ({{ $progress }}%)</p>
                    @endif
                </div>

                <!-- CA Prévisionnel -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center space-x-4">
                    <div class="p-3 bg-purple-50 rounded-xl text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">CA Prévisionnel</p>
                        <p class="text-2xl font-bold text-slate-900">{{ number_format($stats['ca_previsionnel'], 0, ',', ' ') }} XOF</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Graphique des ventes -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800 mb-4">Évolution des Ventes</h3>
                    <div class="h-64">
                        <canvas id="ventesChart"></canvas>
                    </div>
                </div>

                <!-- Graphique des prospects par statut -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800 mb-4">Répartition des Prospects</h3>
                    <div class="h-64">
                        <canvas id="prospectsChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Derniers Prospects -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-slate-800">Derniers Prospects</h3>
                        <a href="{{ route('prospects.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-semibold">Voir tout</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                                <tr>
                                    <th class="px-6 py-3 font-semibold">Nom</th>
                                    <th class="px-6 py-3 font-semibold">Statut</th>
                                    <th class="px-6 py-3 font-semibold">Commercial</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($stats['recent_prospects'] as $prospect)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-bold text-slate-900">{{ $prospect->nom_complet }}</div>
                                            <div class="text-xs text-slate-500">{{ $prospect->email }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 text-[10px] font-bold rounded-full 
                                                @if($prospect->statut == 'Nouveau') bg-blue-100 text-blue-700
                                                @elseif($prospect->statut == 'En cours') bg-amber-100 text-amber-700
                                                @elseif($prospect->statut == 'Converti') bg-emerald-100 text-emerald-700
                                                @else bg-slate-100 text-slate-700 @endif uppercase">
                                                {{ $prospect->statut }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-600">
                                            {{ $prospect->commercial->name ?? 'N/A' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-8 text-center text-slate-500">Aucun prospect récent</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Dernières Ventes -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-slate-800">Dernières Ventes</h3>
                        <a href="{{ route('ventes.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-semibold">Voir tout</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                                <tr>
                                    <th class="px-6 py-3 font-semibold">Client / Produit</th>
                                    <th class="px-6 py-3 font-semibold">Montant</th>
                                    <th class="px-6 py-3 font-semibold">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($stats['recent_ventes'] as $vente)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-bold text-slate-900">{{ $vente->client->nom_complet ?? 'Client Inconnu' }}</div>
                                            <div class="text-xs text-slate-500">{{ $vente->produit->nom ?? 'Produit Inconnu' }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-bold text-emerald-600">
                                            {{ number_format($vente->montant, 0, ',', ' ') }} XOF
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-600">
                                            {{ $vente->date_vente ? $vente->date_vente->format('d/m/Y') : $vente->created_at->format('d/m/Y') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-8 text-center text-slate-500">Aucune vente récente</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Graphique des Ventes
            const ctxVentes = document.getElementById('ventesChart').getContext('2d');
            new Chart(ctxVentes, {
                type: 'line',
                data: {
                    labels: {!! json_encode($stats['chart_ventes_labels']) !!},
                    datasets: [{
                        label: 'Chiffre d\'Affaires (XOF)',
                        data: {!! json_encode($stats['chart_ventes_data']) !!},
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });

            // Graphique des Prospects
            const ctxProspects = document.getElementById('prospectsChart').getContext('2d');
            new Chart(ctxProspects, {
                type: 'pie',
                data: {
                    labels: {!! json_encode($stats['chart_prospects_labels']) !!},
                    datasets: [{
                        data: {!! json_encode($stats['chart_prospects_data']) !!},
                        backgroundColor: ['#3b82f6', '#10b981','#f59e0b', '#64748b', '#ef4444'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    },
                    cutout: '10%'
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
