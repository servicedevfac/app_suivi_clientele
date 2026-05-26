<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div class="flex items-center space-x-3">
                <a href="{{ route('clients.index') }}" class="inline-flex items-center justify-center p-2 text-slate-500 hover:text-slate-700 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 shadow-sm transition-all duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <div class="flex items-center space-x-2.5">
                        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $client->prenom }} {{ $client->nom }}</h1>
                        @if($client->statut === 'Actif')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                Actif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                Inactif
                            </span>
                        @endif
                    </div>
                    <p class="text-sm text-slate-500 mt-1">{{ $client->entreprise ?? 'Client Particulier' }}</p>
                </div>
            </div>
            
            <div class="flex items-center space-x-2">
                <a href="{{ route('ventes.create', ['client_id' => $client->id]) }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl shadow-md transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Enregistrer une Vente
                </a>
                <a href="{{ route('clients.edit', $client) }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-indigo-750 bg-indigo-50 hover:bg-indigo-100 rounded-xl border border-indigo-200 transition-all">
                    Modifier
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Main Content Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Column 1 & 2: Client Info & Purchases -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Contact Card -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Informations Client</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Email</span>
                        <span class="text-sm font-medium text-slate-800 block mt-1">{{ $client->email ?? 'Non renseigné' }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Téléphone</span>
                        <span class="text-sm font-medium text-slate-800 block mt-1">{{ $client->telephone ?? 'Non renseigné' }}</span>
                    </div>
                    <div class="md:col-span-2">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Adresse</span>
                        <span class="text-sm font-medium text-slate-800 block mt-1">
                            {{ $client->adresse ?? 'Non renseignée' }}
                            @if($client->ville)
                                <br><span class="text-slate-500">{{ $client->ville }}</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Purchases / Sales Table -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Historique d'achats / Ventes</h3>
                    <span class="px-2.5 py-1 text-xs font-bold text-indigo-700 bg-indigo-50 border border-indigo-100 rounded-lg">
                        Total des ventes : {{ number_format($client->ventes->where('statut', 'Validée')->sum('montant'), 2, ',', ' ') }} xof
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="px-6 py-3 text-xs font-bold text-slate-450 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-xs font-bold text-slate-450 uppercase tracking-wider">Produit</th>
                                <th class="px-6 py-3 text-xs font-bold text-slate-450 uppercase tracking-wider">Quantité</th>
                                <th class="px-6 py-3 text-xs font-bold text-slate-450 uppercase tracking-wider">Réduction</th>
                                <th class="px-6 py-3 text-xs font-bold text-slate-450 uppercase tracking-wider">Montant</th>
                                <th class="px-6 py-3 text-xs font-bold text-slate-450 uppercase tracking-wider">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($client->ventes->sortByDesc('date_vente') as $vente)
                                <tr class="hover:bg-slate-50/30 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-600 font-medium">
                                        {{ $vente->date_vente ? $vente->date_vente->format('d/m/Y') : $vente->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-800 font-semibold">
                                        {{ $vente->produit ? $vente->produit->nom : 'Produit supprimé' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-600 font-medium">
                                        {{ $vente->quantite }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-rose-600 font-medium">
                                        -{{ number_format($vente->reduction, 2, ',', ' ') }} xof
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-800 font-bold">
                                        {{ number_format($vente->montant, 2, ',', ' ') }} xof
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $stClasses = match($vente->statut) {
                                                'Validée' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                                'Annulée' => 'bg-rose-50 text-rose-700 border-rose-100',
                                                default => 'bg-amber-50 text-amber-700 border-amber-100',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold border {{ $stClasses }}">
                                            {{ $vente->statut }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-slate-400 text-xs">
                                        Aucun achat ou vente enregistré pour ce client.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Column 3: Sidebar Details -->
        <div class="space-y-6">
            <!-- Attribution & Origin Info Card -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Attribution & Origine</h3>
                <div class="space-y-4">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Filiale rattachée</span>
                        <div class="text-sm font-semibold text-slate-800 mt-1">
                            {{ $client->filiale->nom }}
                        </div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Commercial attitré</span>
                        <div class="text-sm font-semibold text-slate-800 mt-1">
                            {{ $client->commercial ? $client->commercial->name : 'Non assigné' }}
                        </div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Date de conversion</span>
                        <div class="text-xs font-semibold text-slate-700 mt-1">
                            {{ $client->date_conversion ? $client->date_conversion->format('d/m/Y à H:i') : 'Création directe' }}
                        </div>
                    </div>
                    @if($client->prospect)
                        <div class="pt-4 border-t border-slate-100">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Fiche Prospect d'Origine</span>
                            <a href="{{ route('prospects.show', $client->prospect_id) }}" class="text-xs text-indigo-650 hover:text-indigo-800 font-semibold block mt-1.5 flex items-center">
                                Voir le prospect d'origine
                                <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
