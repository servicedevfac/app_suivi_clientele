<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
            <div class="flex items-center space-x-3">
                <a href="{{ route('produits.index') }}" class="inline-flex items-center justify-center p-2 text-slate-500 hover:text-slate-700 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 shadow-sm transition-all duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $produit->nom }}</h1>
                    <p class="text-sm text-slate-500 mt-1">Détails techniques, tarification et statistiques de ventes.</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('produits.edit', $produit) }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl shadow-sm transition-all duration-150">
                    Modifier
                </a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Fiche Produit -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6 lg:col-span-1 h-fit">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-extrabold text-lg border border-indigo-100 shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800">{{ $produit->nom }}</h3>
                    <div class="mt-1">
                        @if($produit->statut === 'actif')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                Actif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                Inactif
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-100 pt-6 space-y-4">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Catégorie / Type</span>
                    <span class="text-xs text-slate-600 font-semibold block mt-0.5">{{ $produit->type ?? 'Non spécifié' }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Filiale de distribution</span>
                    <span class="text-xs text-slate-600 font-semibold block mt-0.5">{{ $produit->filiale->nom ?? 'Toutes' }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Prix unitaire conseillé</span>
                    <span class="text-xs text-slate-800 font-bold block mt-0.5">
                        {{ $produit->prix ? number_format($produit->prix, 2, ',', ' ') . ' xof' : 'Sur devis' }}
                    </span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Description</span>
                    <p class="text-xs text-slate-600 font-medium mt-1 leading-relaxed">{{ $produit->description ?? 'Aucune description fournie.' }}</p>
                </div>
            </div>
        </div>

        <!-- Statistiques et listes associées -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Statistiques de ventes -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex items-center space-x-4">
                    <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Unités vendues</span>
                        <span class="text-xl font-bold text-slate-800 block mt-0.5">{{ $produit->ventes()->sum('quantite') }}</span>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex items-center space-x-4">
                    <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Chiffre d'affaires généré</span>
                        <span class="text-xl font-bold text-slate-800 block mt-0.5">
                            {{ number_format($produit->ventes()->sum('montant') ?? 0, 2, ',', ' ') }} xof
                        </span>
                    </div>
                </div>
            </div>

            <!-- Liste des ventes récentes du produit -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Ventes récentes</h3>
                    <span class="text-xs text-slate-400 font-medium">Historique des transactions</span>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($produit->ventes()->with(['client', 'commercial'])->latest()->take(5)->get() as $vente)
                        <div class="px-6 py-4 flex items-center justify-between hover:bg-slate-50/50 transition-colors duration-150">
                            <div>
                                <span class="font-semibold text-slate-700 text-sm block">Client: {{ $vente->client->nom ?? 'N/A' }}</span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">Commercial: {{ $vente->commercial->name ?? 'N/A' }}</span>
                            </div>
                            <div class="text-right">
                                <span class="font-bold text-slate-800 text-sm block">{{ number_format($vente->montant, 2, ',', ' ') }} xof</span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">Quantité: {{ $vente->quantite }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center text-slate-400 text-xs italic">
                            Aucune vente enregistrée pour ce produit pour le moment.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
