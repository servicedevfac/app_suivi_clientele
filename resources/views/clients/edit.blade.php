<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('clients.index') }}" class="inline-flex items-center justify-center p-2 text-slate-500 hover:text-slate-700 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 shadow-sm transition-all duration-150">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Modifier le client</h1>
                <p class="text-sm text-slate-500 mt-1">Mettre à jour les informations du client.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <form method="POST" action="{{ route('clients.update', $client) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Section 0 : Liaison Prospect (Optionnelle / Lecture Seule) -->
            @if($client->prospect)
                <div class="bg-slate-50 rounded-2xl border border-slate-100 p-6">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Prospect associé d'origine</span>
                    <div class="text-xs font-semibold text-slate-700 mt-1">
                        {{ $client->prospect->prenom }} {{ $client->prospect->nom }} ({{ $client->prospect->entreprise ?? 'Particulier' }})
                    </div>
                </div>
            @endif

            <!-- Section 1 : Identité & Contact -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Identité du Client</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Informations d'identité et de contact de l'entreprise ou du particulier.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Prenom -->
                    <div>
                        <x-input-label for="prenom" value="Prénom" class="text-slate-700 font-semibold" />
                        <x-text-input id="prenom" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="text" name="prenom" :value="old('prenom', $client->prenom)" />
                        <x-input-error :messages="$errors->get('prenom')" class="mt-2 text-xs" />
                    </div>

                    <!-- Nom -->
                    <div>
                        <x-input-label for="nom" value="Nom" class="text-slate-700 font-semibold" />
                        <x-text-input id="nom" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="text" name="nom" :value="old('nom', $client->nom)" required />
                        <x-input-error :messages="$errors->get('nom')" class="mt-2 text-xs" />
                    </div>

                    <!-- Email -->
                    <div>
                        <x-input-label for="email" value="Email" class="text-slate-700 font-semibold" />
                        <x-text-input id="email" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="email" name="email" :value="old('email', $client->email)" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs" />
                    </div>

                    <!-- Téléphone -->
                    <div>
                        <x-input-label for="telephone" value="Téléphone" class="text-slate-700 font-semibold" />
                        <x-text-input id="telephone" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="text" name="telephone" :value="old('telephone', $client->telephone)" />
                        <x-input-error :messages="$errors->get('telephone')" class="mt-2 text-xs" />
                    </div>

                    <!-- Entreprise -->
                    <div class="md:col-span-2">
                        <x-input-label for="entreprise" value="Nom de l'entreprise" class="text-slate-700 font-semibold" />
                        <x-text-input id="entreprise" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="text" name="entreprise" :value="old('entreprise', $client->entreprise)" />
                        <x-input-error :messages="$errors->get('entreprise')" class="mt-2 text-xs" />
                    </div>
                </div>
            </div>

            <!-- Section 2 : Attribution, Localisation & Statut -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Affectation & Statut</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Filiale affectée, responsable commercial et coordonnées géographiques.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Filiale -->
                    <div>
                        <x-input-label for="filiale_id" value="Filiale concernée" class="text-slate-700 font-semibold" />
                        <select id="filiale_id" name="filiale_id" required class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                            <option value="">Sélectionnez une filiale</option>
                            @foreach($filiales as $filiale)
                                <option value="{{ $filiale->id }}" {{ old('filiale_id', $client->filiale_id) == $filiale->id ? 'selected' : '' }}>{{ $filiale->nom }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('filiale_id')" class="mt-2 text-xs" />
                    </div>

                    <!-- Commercial -->
                    <div>
                        <x-input-label for="commercial_id" value="Commercial affecté" class="text-slate-700 font-semibold" />
                        <select id="commercial_id" name="commercial_id" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                            <option value="">Non assigné</option>
                            @foreach($commercials as $comm)
                                <option value="{{ $comm->id }}" {{ old('commercial_id', $client->commercial_id) == $comm->id ? 'selected' : '' }}>{{ $comm->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('commercial_id')" class="mt-2 text-xs" />
                    </div>

                    <!-- Adresse -->
                    <div class="md:col-span-2">
                        <x-input-label for="adresse" value="Adresse" class="text-slate-700 font-semibold" />
                        <textarea id="adresse" name="adresse" rows="2" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">{{ old('adresse', $client->adresse) }}</textarea>
                        <x-input-error :messages="$errors->get('adresse')" class="mt-2 text-xs" />
                    </div>

                    <!-- Ville -->
                    <div>
                        <x-input-label for="ville" value="Ville" class="text-slate-700 font-semibold" />
                        <x-text-input id="ville" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="text" name="ville" :value="old('ville', $client->ville)" />
                        <x-input-error :messages="$errors->get('ville')" class="mt-2 text-xs" />
                    </div>

                    <!-- Statut -->
                    <div>
                        <x-input-label for="statut" value="Statut" class="text-slate-700 font-semibold" />
                        <select id="statut" name="statut" required class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                            <option value="Actif" {{ old('statut', $client->statut) === 'Actif' ? 'selected' : '' }}>Actif</option>
                            <option value="Inactif" {{ old('statut', $client->statut) === 'Inactif' ? 'selected' : '' }}>Inactif</option>
                        </select>
                        <x-input-error :messages="$errors->get('statut')" class="mt-2 text-xs" />
                    </div>

                    <!-- Date de conversion -->
                    <div class="md:col-span-2">
                        <x-input-label for="date_conversion" value="Date de conversion" class="text-slate-700 font-semibold" />
                        <x-text-input id="date_conversion" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="datetime-local" name="date_conversion" :value="old('date_conversion', $client->date_conversion ? $client->date_conversion->format('Y-m-d\TH:i') : '')" />
                        <x-input-error :messages="$errors->get('date_conversion')" class="mt-2 text-xs" />
                    </div>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="flex items-center justify-end space-x-3 pt-2">
                <a href="{{ route('clients.index') }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 active:bg-slate-100 rounded-xl border border-slate-200 shadow-sm transition-all duration-150">
                    Annuler
                </a>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl shadow-md shadow-indigo-600/10 hover:shadow-indigo-600/20 transition-all duration-200">
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
