<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('prospects.index') }}" class="inline-flex items-center justify-center p-2 text-slate-500 hover:text-slate-700 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 shadow-sm transition-all duration-150">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Modifier le prospect</h1>
                <p class="text-sm text-slate-500 mt-1">Mettre à jour les informations de l'opportunité commerciale.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <form method="POST" action="{{ route('prospects.update', $prospect) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Section 1 : Identité & Contact -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Identité & Contact</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Informations personnelles et coordonnées du prospect.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Prenom -->
                    <div>
                        <x-input-label for="prenom" value="Prénom" class="text-slate-700 font-semibold" />
                        <x-text-input id="prenom" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="text" name="prenom" :value="old('prenom', $prospect->prenom)" />
                        <x-input-error :messages="$errors->get('prenom')" class="mt-2 text-xs" />
                    </div>

                    <!-- Nom -->
                    <div>
                        <x-input-label for="nom" value="Nom" class="text-slate-700 font-semibold" />
                        <x-text-input id="nom" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="text" name="nom" :value="old('nom', $prospect->nom)" />
                        <x-input-error :messages="$errors->get('nom')" class="mt-2 text-xs" />
                    </div>

                    <!-- Email -->
                    <div>
                        <x-input-label for="email" value="Email" class="text-slate-700 font-semibold" />
                        <x-text-input id="email" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="email" name="email" :value="old('email', $prospect->email)" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs" />
                    </div>

                    <!-- Téléphone -->
                    <div>
                        <x-input-label for="telephone" value="Téléphone *" class="text-slate-700 font-semibold" />
                        <x-text-input id="telephone" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="text" name="telephone" :value="old('telephone', $prospect->telephone)" required />
                        <x-input-error :messages="$errors->get('telephone')" class="mt-2 text-xs" />
                    </div>

                    <!-- Entreprise -->
                    <div>
                        <x-input-label for="entreprise" value="Entreprise" class="text-slate-700 font-semibold" />
                        <x-text-input id="entreprise" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="text" name="entreprise" :value="old('entreprise', $prospect->entreprise)" />
                        <x-input-error :messages="$errors->get('entreprise')" class="mt-2 text-xs" />
                    </div>

                    <!-- Profession -->
                    <div>
                        <x-input-label for="profession" value="Profession / Poste" class="text-slate-700 font-semibold" />
                        <x-text-input id="profession" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="text" name="profession" :value="old('profession', $prospect->profession)" />
                        <x-input-error :messages="$errors->get('profession')" class="mt-2 text-xs" />
                    </div>
                </div>
            </div>

            <!-- Section 2 : Attribution & Marketing -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Provenance & Rôles</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Affectation géographique, commerciale et origine du prospect.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6" x-data="{ selectedCampagne: '{{ old('campagne_id', $prospect->campagne_id ?? '') }}' }">
                    <!-- Filiale -->
                    <div>
                        <x-input-label for="filiale_id" value="Filiale concernée" class="text-slate-700 font-semibold" />
                        <select id="filiale_id" name="filiale_id" required class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                            <option value="">Sélectionnez une filiale</option>
                            @foreach($filiales as $filiale)
                                <option value="{{ $filiale->id }}" {{ old('filiale_id', $prospect->filiale_id) == $filiale->id ? 'selected' : '' }}>{{ $filiale->nom }}</option>
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
                                <option value="{{ $comm->id }}" {{ old('commercial_id', $prospect->commercial_id) == $comm->id ? 'selected' : '' }}>{{ $comm->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('commercial_id')" class="mt-2 text-xs" />
                    </div>

                    <!-- Source -->
                    <div>
                        <x-input-label for="source_id" value="Source d'acquisition" class="text-slate-700 font-semibold" />
                        <select id="source_id" name="source_id" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                            <option value="">Sélectionnez une source</option>
                            @foreach($sources as $source)
                                <option value="{{ $source->id }}" {{ old('source_id', $prospect->source_id) == $source->id ? 'selected' : '' }}>{{ $source->nom }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('source_id')" class="mt-2 text-xs" />
                    </div>

                    <!-- Campagne -->
                    <div>
                        <x-input-label for="campagne_id" value="Campagne Marketing" class="text-slate-700 font-semibold" />
                        <select id="campagne_id" name="campagne_id" x-model="selectedCampagne" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                            <option value="">Sélectionnez une campagne</option>
                            @foreach($campagnes as $campagne)
                                <option value="{{ $campagne->id }}" {{ old('campagne_id', $prospect->campagne_id) == $campagne->id ? 'selected' : '' }}>{{ $campagne->nom }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('campagne_id')" class="mt-2 text-xs" />
                    </div>

                    <!-- Publication / Moyen -->
                    <div>
                        <x-input-label for="publication_id" value="Publication / Moyen exact d'acquisition" class="text-slate-700 font-semibold" />
                        <select id="publication_id" name="publication_id" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                            <option value="">Direct / Non spécifié</option>
                            @if(isset($publications))
                                @foreach($publications as $pub)
                                    <option value="{{ $pub->id }}" x-show="!selectedCampagne || selectedCampagne == '{{ $pub->campagne_id }}'" {{ old('publication_id', $prospect->publication_id) == $pub->id ? 'selected' : '' }}>
                                        [{{ $pub->canal }}] {{ Str::limit($pub->titre, 40) }} ({{ $pub->campagne->nom ?? '' }})
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <p class="text-[11px] text-slate-400 mt-1">Sélectionnez le support ou le canal qui a attiré ce prospect.</p>
                        <x-input-error :messages="$errors->get('publication_id')" class="mt-2 text-xs" />
                    </div>

                    <!-- Statut -->
                    <div class="md:col-span-2">
                        <x-input-label for="statut" value="Statut" class="text-slate-700 font-semibold" />
                        <select id="statut" name="statut" required class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm" {{ in_array($prospect->statut, ['Gagné', 'Perdu']) ? 'disabled' : '' }}>
                            @foreach(['Nouveau', 'Contacté', 'Qualifié', 'En négociation', 'Gagné', 'Perdu'] as $st)
                                <option value="{{ $st }}" {{ old('statut', $prospect->statut) === $st ? 'selected' : '' }}>{{ $st }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('statut')" class="mt-2 text-xs" />
                    </div>
                </div>
            </div>

            <!-- Section 3 : Localisation & Détails -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Localisation & Détails du Besoin</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Adresse et description du besoin client.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Adresse -->
                    <div class="md:col-span-2">
                        <x-input-label for="adresse" value="Adresse" class="text-slate-700 font-semibold" />
                        <textarea id="adresse" name="adresse" rows="2" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">{{ old('adresse', $prospect->adresse) }}</textarea>
                        <x-input-error :messages="$errors->get('adresse')" class="mt-2 text-xs" />
                    </div>

                    <!-- Ville -->
                    <div>
                        <x-input-label for="ville" value="Ville" class="text-slate-700 font-semibold" />
                        <x-text-input id="ville" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="text" name="ville" :value="old('ville', $prospect->ville)" />
                        <x-input-error :messages="$errors->get('ville')" class="mt-2 text-xs" />
                    </div>

                    <!-- Date de contact -->
                    <div>
                        <x-input-label for="date_contact" value="Date de contact" class="text-slate-700 font-semibold" />
                        <x-text-input id="date_contact" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="datetime-local" name="date_contact" :value="old('date_contact', $prospect->date_contact ? $prospect->date_contact->format('Y-m-d\TH:i') : '')" />
                        <x-input-error :messages="$errors->get('date_contact')" class="mt-2 text-xs" />
                    </div>

                    <!-- Montant Estimé -->
                    <div>
                        <x-input-label for="montant_estime" value="Montant estimé (€)" class="text-slate-700 font-semibold" />
                        <x-text-input id="montant_estime" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="number" step="0.01" name="montant_estime" :value="old('montant_estime', $prospect->montant_estime)" />
                        <x-input-error :messages="$errors->get('montant_estime')" class="mt-2 text-xs" />
                    </div>

                    <!-- Probabilité -->
                    <div>
                        <x-input-label for="probabilite" value="Probabilité (%)" class="text-slate-700 font-semibold" />
                        <x-text-input id="probabilite" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="number" min="0" max="100" name="probabilite" :value="old('probabilite', $prospect->probabilite)" />
                        <x-input-error :messages="$errors->get('probabilite')" class="mt-2 text-xs" />
                    </div>

                    <!-- Besoin -->
                    <div class="md:col-span-2">
                        <x-input-label for="besoin" value="Besoin qualifié" class="text-slate-700 font-semibold" />
                        <textarea id="besoin" name="besoin" rows="3" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">{{ old('besoin', $prospect->besoin) }}</textarea>
                        <x-input-error :messages="$errors->get('besoin')" class="mt-2 text-xs" />
                    </div>

                    <!-- Commentaire -->
                    <div class="md:col-span-2">
                        <x-input-label for="commentaire" value="Commentaire / Notes" class="text-slate-700 font-semibold" />
                        <textarea id="commentaire" name="commentaire" rows="2" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">{{ old('commentaire', $prospect->commentaire) }}</textarea>
                        <x-input-error :messages="$errors->get('commentaire')" class="mt-2 text-xs" />
                    </div>

                    <!-- Tags -->
                    <div class="md:col-span-2">
                        <x-input-label for="tags" value="Tags (séparés par des virgules)" class="text-slate-700 font-semibold" />
                        <x-text-input id="tags" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm" type="text" name="tags" :value="old('tags', is_array($prospect->tags) ? implode(', ', $prospect->tags) : '')" placeholder="ex: VIP, Urgent, Secteur IT" />
                        <p class="text-[10px] text-slate-500 mt-1">Séparez chaque tag par une virgule.</p>
                        <x-input-error :messages="$errors->get('tags')" class="mt-2 text-xs" />
                    </div>

                    <!-- Prochain Rappel -->
                    <div class="md:col-span-2">
                        <x-input-label for="prochain_rappel" value="Date du prochain rappel" class="text-slate-700 font-semibold" />
                        <x-text-input id="prochain_rappel" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="datetime-local" name="prochain_rappel" :value="old('prochain_rappel', $prospect->prochain_rappel ? $prospect->prochain_rappel->format('Y-m-d\TH:i') : '')" />
                        <x-input-error :messages="$errors->get('prochain_rappel')" class="mt-2 text-xs" />
                    </div>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="flex items-center justify-end space-x-3 pt-2">
                <a href="{{ route('prospects.index') }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 active:bg-slate-100 rounded-xl border border-slate-200 shadow-sm transition-all duration-150">
                    Annuler
                </a>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl shadow-md shadow-indigo-600/10 hover:shadow-indigo-600/20 transition-all duration-200">
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
