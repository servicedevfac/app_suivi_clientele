<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('produits.index') }}" class="inline-flex items-center justify-center p-2 text-slate-500 hover:text-slate-700 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 shadow-sm transition-all duration-150">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Ajouter un produit</h1>
                <p class="text-sm text-slate-500 mt-1">Ajouter une nouvelle offre ou référence au catalogue commercial.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <form method="POST" action="{{ route('produits.store') }}" class="space-y-6">
            @csrf

            <!-- Section 1 : Détails du Produit -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Caractéristiques</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Identifiant, type et filiale d'affectation.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nom -->
                    <div>
                        <x-input-label for="nom" value="Nom de l'offre" class="text-slate-700 font-semibold" />
                        <x-text-input id="nom" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="text" name="nom" :value="old('nom')" required autofocus placeholder="Ex: Licence Logiciel Standard, Abonnement Mensuel..." />
                        <x-input-error :messages="$errors->get('nom')" class="mt-2 text-xs" />
                    </div>

                    <!-- Filiale -->
                    <div>
                        <x-input-label for="filiale_id" value="Filiale distributrice" class="text-slate-700 font-semibold" />
                        <select id="filiale_id" name="filiale_id" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm" required>
                            <option value="">Sélectionnez une filiale</option>
                            @foreach($filiales as $filiale)
                                <option value="{{ $filiale->id }}" {{ old('filiale_id') == $filiale->id ? 'selected' : '' }}>{{ $filiale->nom }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('filiale_id')" class="mt-2 text-xs" />
                    </div>

                    <!-- Type de produit -->
                    <div>
                        <x-input-label for="type" value="Type / Catégorie" class="text-slate-700 font-semibold" />
                        <x-text-input id="type" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="text" name="type" :value="old('type')" placeholder="Ex: Logiciel, Matériel, Service..." />
                        <x-input-error :messages="$errors->get('type')" class="mt-2 text-xs" />
                    </div>

                    <!-- Statut -->
                    <div>
                        <x-input-label for="statut" value="Statut dans le catalogue" class="text-slate-700 font-semibold" />
                        <select id="statut" name="statut" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm" required>
                            <option value="actif" {{ old('statut', 'actif') === 'actif' ? 'selected' : '' }}>Actif</option>
                            <option value="inactif" {{ old('statut') === 'inactif' ? 'selected' : '' }}>Inactif</option>
                        </select>
                        <x-input-error :messages="$errors->get('statut')" class="mt-2 text-xs" />
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <x-input-label for="description" value="Description / Spécifications de l'offre" class="text-slate-700 font-semibold" />
                        <textarea id="description" name="description" rows="3" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm" placeholder="Décrivez les fonctionnalités clés ou les modalités de livraison...">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2 text-xs" />
                    </div>
                </div>
            </div>

            <!-- Section 2 : Tarification -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Tarification</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Paramètres financiers.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Prix de base -->
                    <div>
                        <x-input-label for="prix" value="Prix de vente unitaire hors taxes (xof)" class="text-slate-700 font-semibold" />
                        <x-text-input id="prix" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="number" step="0.01" name="prix" :value="old('prix')" placeholder="Ex: 299.90 (laisser vide si sur devis)" />
                        <x-input-error :messages="$errors->get('prix')" class="mt-2 text-xs" />
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex items-center justify-end space-x-3 pt-2">
                <a href="{{ route('produits.index') }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 active:bg-slate-100 rounded-xl border border-slate-200 shadow-sm transition-all duration-150">
                    Annuler
                </a>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl shadow-md shadow-indigo-600/10 hover:shadow-indigo-600/20 transition-all duration-200">
                    Ajouter le produit
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
