<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Rapports Avancés') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- KPIs Principaux -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Taux de Conversion Global</p>
                    <p class="text-3xl font-bold text-slate-900 mt-2">{{ number_format($conversionRate, 1) }} %</p>
                    <p class="text-xs text-slate-400 mt-1">{{ $totalClients }} clients / {{ $totalProspects }} prospects</p>
                </div>
                
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Temps Moyen de Conclusion</p>
                    <p class="text-3xl font-bold text-slate-900 mt-2">{{ $avgTimeToConvert }} <span class="text-lg">jours</span></p>
                    <p class="text-xs text-slate-400 mt-1">Délai entre création et conversion</p>
                </div>
                
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Croissance des Ventes</p>
                    @php
                        $growth = $lastMonthSales > 0 ? (($currentMonthSales - $lastMonthSales) / $lastMonthSales) * 100 : 0;
                    @endphp
                    <div class="flex items-center mt-2">
                        <p class="text-3xl font-bold text-slate-900">{{ number_format($currentMonthSales, 0, ',', ' ') }} €</p>
                        <span class="ml-3 px-2 py-1 text-xs font-bold rounded-full {{ $growth >= 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                            {{ $growth > 0 ? '+' : '' }}{{ number_format($growth, 1) }}%
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 mt-1">Par rapport au mois précédent</p>
                </div>
            </div>

            <!-- Conversion par source : Graphique et Table -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Graphique -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col">
                    <h3 class="text-lg font-bold text-slate-800 mb-6">Aperçu Visuel des Conversions</h3>
                    <div class="relative flex-1 w-full min-h-[250px]">
                        <canvas id="conversionChart"></canvas>
                    </div>
                </div>

                <!-- Tableau -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col">
                    <div class="p-6 border-b border-slate-100">
                        <h3 class="text-lg font-bold text-slate-800">Détails par Source</h3>
                    </div>
                    <div class="overflow-x-auto flex-1">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                                <tr>
                                    <th class="px-6 py-3 font-semibold">Source</th>
                                    <th class="px-6 py-3 font-semibold">Prospects</th>
                                    <th class="px-6 py-3 font-semibold">Convertis</th>
                                    <th class="px-6 py-3 font-semibold">Taux</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($conversionBySource as $sourceData)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4 font-semibold text-slate-900">{{ $sourceData['nom'] }}</td>
                                        <td class="px-6 py-4 text-slate-600">{{ $sourceData['prospects_count'] }}</td>
                                        <td class="px-6 py-4 text-slate-600">{{ $sourceData['converted_count'] }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <span class="text-sm font-bold {{ $sourceData['rate'] > 20 ? 'text-emerald-600' : 'text-slate-700' }}">{{ $sourceData['rate'] }}%</span>
                                                <div class="hidden sm:block w-16 bg-slate-100 rounded-full h-1.5 ml-3">
                                                    <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $sourceData['rate'] }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js" nonce="{{ Vite::cspNonce() }}"></script>
    <script nonce="{{ Vite::cspNonce() }}">
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('conversionChart').getContext('2d');
            
            const rawData = @json($conversionBySource);
            const dataArray = Object.values(rawData);
            
            const labels = dataArray.map(item => item.nom);
            const rates = dataArray.map(item => item.rate);
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Taux de Conversion (%)',
                        data: rates,
                        backgroundColor: 'rgba(99, 102, 241, 0.8)', // indigo-500
                        borderColor: 'rgb(79, 70, 229)', // indigo-600
                        borderWidth: 1,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y + '%';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endpush

        </div>
    </div>
</x-app-layout>
