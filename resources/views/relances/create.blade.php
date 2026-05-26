<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('relances.index') }}" class="inline-flex items-center justify-center p-2 text-slate-500 hover:text-slate-700 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 shadow-sm transition-all duration-150">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Planifier une relance</h1>
                <p class="text-sm text-slate-500 mt-1">Programmez un rappel de relance téléphonique, WhatsApp, email ou rendez-vous pour un prospect.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <form method="POST" action="{{ route('relances.store') }}" class="space-y-6">
            @csrf

            <!-- Section 1 : Cible & Affectation -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Prospect & Affectation</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Associez cette relance à un prospect et déterminez le commercial en charge.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Prospect lié -->
                    <div>
                        <x-input-label for="prospect_id" value="Sélectionner le prospect" class="text-slate-700 font-semibold" />
                        <select id="prospect_id" name="prospect_id" required class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                            <option value="">-- Sélectionnez un prospect --</option>
                            @foreach($prospects as $prospect)
                                <option value="{{ $prospect->id }}" {{ old('prospect_id', request('prospect_id')) == $prospect->id ? 'selected' : '' }}>
                                    {{ $prospect->prenom }} {{ $prospect->nom }} ({{ $prospect->entreprise ?? 'Particulier' }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('prospect_id')" class="mt-2 text-xs" />
                    </div>

                    <!-- Commercial en charge -->
                    <div>
                        <x-input-label for="commercial_id" value="Commercial en charge" class="text-slate-700 font-semibold" />
                        <select id="commercial_id" name="commercial_id" required {{ !auth()->user()->hasRole('Administrateur') ? 'disabled' : '' }} class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm disabled:opacity-50">
                            @if(!auth()->user()->hasRole('Administrateur'))
                                <option value="{{ auth()->id() }}" selected>{{ auth()->user()->name }}</option>
                            @else
                                <option value="">-- Sélectionnez un commercial --</option>
                                @foreach($commercials as $comm)
                                    <option value="{{ $comm->id }}" {{ old('commercial_id', auth()->id()) == $comm->id ? 'selected' : '' }}>{{ $comm->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        @if(!auth()->user()->hasRole('Administrateur'))
                            <input type="hidden" name="commercial_id" value="{{ auth()->id() }}">
                        @endif
                        <x-input-error :messages="$errors->get('commercial_id')" class="mt-2 text-xs" />
                    </div>
                </div>
            </div>

            <!-- Section 2 : Date, Canal & Commentaire -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Planification & Canal de contact</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Indiquez la date, l'heure, le canal de contact privilégié et les objectifs de la relance.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Date de la relance -->
                    <div>
                        <x-input-label for="date_relance" value="Date de la relance" class="text-slate-700 font-semibold" />
                        <x-text-input id="date_relance" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="date" name="date_relance" :value="old('date_relance', date('Y-m-d'))" required />
                        <x-input-error :messages="$errors->get('date_relance')" class="mt-2 text-xs" />
                    </div>

                    <!-- Heure de la relance -->
                    <div>
                        <x-input-label for="heure_relance" value="Heure de la relance (optionnel)" class="text-slate-700 font-semibold" />
                        <x-text-input id="heure_relance" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="time" name="heure_relance" :value="old('heure_relance')" />
                        <x-input-error :messages="$errors->get('heure_relance')" class="mt-2 text-xs" />
                    </div>

                    <!-- Canal -->
                    <div>
                        <x-input-label for="canal" value="Canal de communication" class="text-slate-700 font-semibold" />
                        <select id="canal" name="canal" required class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                            <option value="Appel" {{ old('canal') === 'Appel' ? 'selected' : '' }}>📞 Appel téléphonique</option>
                            <option value="WhatsApp" {{ old('canal') === 'WhatsApp' ? 'selected' : '' }}>💬 Message WhatsApp</option>
                            <option value="Email" {{ old('canal', 'Email') === 'Email' ? 'selected' : '' }}>✉️ Email personnalisé</option>
                            <option value="SMS" {{ old('canal') === 'SMS' ? 'selected' : '' }}>📱 SMS standard</option>
                            <option value="Rendez-vous" {{ old('canal') === 'Rendez-vous' ? 'selected' : '' }}>🤝 Rendez-vous physique/visio</option>
                        </select>
                        <x-input-error :messages="$errors->get('canal')" class="mt-2 text-xs" />
                    </div>

                    <!-- Statut -->
                    <div>
                        <x-input-label for="statut" value="Statut de la planification" class="text-slate-700 font-semibold" />
                        <select id="statut" name="statut" required class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                            <option value="En attente" {{ old('statut', 'En attente') === 'En attente' ? 'selected' : '' }}>En attente</option>
                            <option value="Réalisée" {{ old('statut') === 'Réalisée' ? 'selected' : '' }}>Réalisée</option>
                            <option value="Annulée" {{ old('statut') === 'Annulée' ? 'selected' : '' }}>Annulée</option>
                        </select>
                        <x-input-error :messages="$errors->get('statut')" class="mt-2 text-xs" />
                    </div>

                    <!-- Commentaire -->
                    <div class="md:col-span-2">
                        <x-input-label for="commentaire" value="Commentaire / Objectif de la relance" class="text-slate-700 font-semibold" />
                        <textarea id="commentaire" name="commentaire" rows="3" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm" placeholder="Ex: Lui présenter la nouvelle grille tarifaire et obtenir son accord de principe...">{{ old('commentaire') }}</textarea>
                        <x-input-error :messages="$errors->get('commentaire')" class="mt-2 text-xs" />
                    </div>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="flex items-center justify-end space-x-3 pt-2">
                <a href="{{ route('relances.index') }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 active:bg-slate-100 rounded-xl border border-slate-200 shadow-sm transition-all duration-150">
                    Annuler
                </a>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl shadow-md shadow-indigo-600/10 hover:shadow-indigo-600/20 transition-all duration-200">
                    Planifier la relance
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
