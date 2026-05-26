<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('users.index') }}" class="inline-flex items-center justify-center p-2 text-slate-500 hover:text-slate-700 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 shadow-sm transition-all duration-150">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Créer un utilisateur</h1>
                <p class="text-sm text-slate-500 mt-1">Ajouter un nouveau collaborateur au CRM.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
            @csrf

            <!-- Section 1 : Informations Générales -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Informations Générales</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Identité et coordonnées du collaborateur.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nom -->
                    <div>
                        <x-input-label for="nom" value="Nom" class="text-slate-700 font-semibold" />
                        <x-text-input id="nom" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="text" name="nom" :value="old('nom')" required autofocus autocomplete="family-name" />
                        <x-input-error :messages="$errors->get('nom')" class="mt-2 text-xs" />
                    </div>

                    <!-- Prénom -->
                    <div>
                        <x-input-label for="prenom" value="Prénom" class="text-slate-700 font-semibold" />
                        <x-text-input id="prenom" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="text" name="prenom" :value="old('prenom')" autocomplete="given-name" />
                        <x-input-error :messages="$errors->get('prenom')" class="mt-2 text-xs" />
                    </div>

                    <!-- Email -->
                    <div>
                        <x-input-label for="email" value="Email" class="text-slate-700 font-semibold" />
                        <x-text-input id="email" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="email" name="email" :value="old('email')" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs" />
                    </div>

                    <!-- Téléphone -->
                    <div>
                        <x-input-label for="telephone" value="Téléphone" class="text-slate-700 font-semibold" />
                        <x-text-input id="telephone" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="tel" name="telephone" :value="old('telephone')" autocomplete="tel" />
                        <x-input-error :messages="$errors->get('telephone')" class="mt-2 text-xs" />
                    </div>
                </div>
            </div>

            <!-- Section 2 : Sécurité & Rôles -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Sécurité & Accès</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Mot de passe de connexion et habilitations.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Mot de passe -->
                    <div>
                        <x-input-label for="password" value="Mot de passe" class="text-slate-700 font-semibold" />
                        <x-text-input id="password" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="password" name="password" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs" />
                    </div>

                    <!-- Confirmation Mot de passe -->
                    <div>
                        <x-input-label for="password_confirmation" value="Confirmer Mot de passe" class="text-slate-700 font-semibold" />
                        <x-text-input id="password_confirmation" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="password" name="password_confirmation" required autocomplete="new-password" />
                    </div>

                    <!-- Rôles -->
                    <div class="md:col-span-2">
                        <x-input-label value="Rôles" class="text-slate-700 font-semibold" />
                        <p class="text-[11px] text-slate-400 mt-0.5">Attribuez un ou plusieurs rôles à ce collaborateur.</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-3">
                            @foreach($roles as $role)
                                <label class="relative flex items-start p-4 rounded-xl border border-slate-200 hover:bg-slate-50/50 cursor-pointer select-none transition-all duration-150">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" name="roles[]" value="{{ $role->name }}" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    </div>
                                    <div class="ml-3 text-xs">
                                        <span class="font-semibold text-slate-800">{{ $role->name }}</span>
                                        @if($role->name === 'Administrateur')
                                            <span class="block text-[10px] text-slate-400 mt-0.5">Accès total à toutes les configurations du système.</span>
                                        @elseif($role->name === 'Directeur Général')
                                            <span class="block text-[10px] text-slate-400 mt-0.5">Lecture globale et rapports de performances du CRM.</span>
                                        @elseif($role->name === 'Responsable Commercial')
                                            <span class="block text-[10px] text-slate-400 mt-0.5">Gestion de l'équipe commerciale et du catalogue.</span>
                                        @elseif($role->name === 'Commercial')
                                            <span class="block text-[10px] text-slate-400 mt-0.5">Gestion de ses prospects et ventes.</span>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <x-input-error :messages="$errors->get('roles')" class="mt-2 text-xs" />
                    </div>

                    <!-- Statut du Compte (Actif) -->
                    <div class="md:col-span-2 border-t border-slate-100 pt-6">
                        <label class="relative flex items-start p-4 rounded-xl bg-emerald-50/50 border border-emerald-100 hover:bg-emerald-50 cursor-pointer select-none transition-all duration-150">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500" checked>
                            </div>
                            <div class="ml-3 text-xs">
                                <span class="font-semibold text-emerald-800">Activer le compte immédiatement</span>
                                <span class="block text-[10px] text-emerald-600/80 mt-0.5">L'utilisateur pourra se connecter à l'aide de ses identifiants.</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex items-center justify-end space-x-3 pt-2">
                <a href="{{ route('users.index') }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 active:bg-slate-100 rounded-xl border border-slate-200 shadow-sm transition-all duration-150">
                    Annuler
                </a>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl shadow-md shadow-indigo-600/10 hover:shadow-indigo-600/20 transition-all duration-200">
                    Créer l'utilisateur
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
