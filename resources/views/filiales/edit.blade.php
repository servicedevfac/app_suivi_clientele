<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('filiales.index') }}" class="inline-flex items-center justify-center p-2 text-slate-500 hover:text-slate-700 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 shadow-sm transition-all duration-150">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Modifier la filiale</h1>
                <p class="text-sm text-slate-500 mt-1">Mettre à jour les informations de {{ $filiale->nom }}.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <form method="POST" action="{{ route('filiales.update', $filiale) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Section 1 : Informations Générales -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Informations Générales</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Identité et coordonnées de la filiale.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nom -->
                    <div class="md:col-span-2">
                        <x-input-label for="nom" value="Nom de la filiale" class="text-slate-700 font-semibold" />
                        <x-text-input id="nom" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="text" name="nom" :value="old('nom', $filiale->nom)" required autofocus />
                        <x-input-error :messages="$errors->get('nom')" class="mt-2 text-xs" />
                    </div>

                    <!-- Email -->
                    <div>
                        <x-input-label for="email" value="Email de contact" class="text-slate-700 font-semibold" />
                        <x-text-input id="email" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="email" name="email" :value="old('email', $filiale->email)" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs" />
                    </div>

                    <!-- Téléphone -->
                    <div>
                        <x-input-label for="telephone" value="Téléphone de contact" class="text-slate-700 font-semibold" />
                        <x-text-input id="telephone" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="text" name="telephone" :value="old('telephone', $filiale->telephone)" />
                        <x-input-error :messages="$errors->get('telephone')" class="mt-2 text-xs" />
                    </div>
                </div>
            </div>

            <!-- Section 2 : Localisation & Statut -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Localisation & Statut</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Adresse et état de fonctionnement.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Adresse -->
                    <div class="md:col-span-2">
                        <x-input-label for="adresse" value="Adresse" class="text-slate-700 font-semibold" />
                        <textarea id="adresse" name="adresse" rows="3" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">{{ old('adresse', $filiale->adresse) }}</textarea>
                        <x-input-error :messages="$errors->get('adresse')" class="mt-2 text-xs" />
                    </div>

                    <!-- Ville -->
                    <div>
                        <x-input-label for="ville" value="Ville" class="text-slate-700 font-semibold" />
                        <x-text-input id="ville" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="text" name="ville" :value="old('ville', $filiale->ville)" />
                        <x-input-error :messages="$errors->get('ville')" class="mt-2 text-xs" />
                    </div>

                    <!-- Pays -->
                    <div>
                        <x-input-label for="pays" value="Pays" class="text-slate-700 font-semibold" />
                        <x-text-input id="pays" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="text" name="pays" :value="old('pays', $filiale->pays)" />
                        <x-input-error :messages="$errors->get('pays')" class="mt-2 text-xs" />
                    </div>

                    <!-- Statut -->
                    <div class="md:col-span-2">
                        <x-input-label for="statut" value="Statut" class="text-slate-700 font-semibold" />
                        <select id="statut" name="statut" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                            <option value="actif" {{ old('statut', $filiale->statut) === 'actif' ? 'selected' : '' }}>Actif</option>
                            <option value="inactif" {{ old('statut', $filiale->statut) === 'inactif' ? 'selected' : '' }}>Inactif</option>
                        </select>
                        <x-input-error :messages="$errors->get('statut')" class="mt-2 text-xs" />
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex items-center justify-end space-x-3 pt-2">
                <a href="{{ route('filiales.index') }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 active:bg-slate-100 rounded-xl border border-slate-200 shadow-sm transition-all duration-150">
                    Annuler
                </a>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl shadow-md shadow-indigo-600/10 hover:shadow-indigo-600/20 transition-all duration-200">
                    Mettre à jour la filiale
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
