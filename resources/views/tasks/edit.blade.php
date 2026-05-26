<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('tasks.index') }}" class="inline-flex items-center justify-center p-2 text-slate-500 hover:text-slate-700 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 shadow-sm transition-all duration-150">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Modifier la tâche</h1>
                <p class="text-sm text-slate-500 mt-1">Mettre à jour les informations et le statut de la tâche commerciale.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <form method="POST" action="{{ route('tasks.update', $task) }}" class="space-y-6">
            @csrf
            @method('PATCH')

            <!-- Section 1 : Détails de la Tâche -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Informations Générales</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Saisissez l'intitulé et la description détaillée de la tâche à accomplir.</p>
                </div>
                <div class="grid grid-cols-1 gap-6">
                    <!-- Titre -->
                    <div>
                        <x-input-label for="titre" value="Titre de la tâche" class="text-slate-700 font-semibold" />
                        <x-text-input id="titre" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="text" name="titre" :value="old('titre', $task->titre)" required />
                        <x-input-error :messages="$errors->get('titre')" class="mt-2 text-xs" />
                    </div>

                    <!-- Description -->
                    <div>
                        <x-input-label for="description" value="Description / Notes" class="text-slate-700 font-semibold" />
                        <textarea id="description" name="description" rows="4" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">{{ old('description', $task->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2 text-xs" />
                    </div>
                </div>
            </div>

            <!-- Section 2 : Planification, Attribution & Priorisation -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Planification & Attribution</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Définissez la priorité de la tâche, sa date d'échéance et attribuez-la.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Prospect lié -->
                    <div>
                        <x-input-label for="prospect_id" value="Associer à un prospect (optionnel)" class="text-slate-700 font-semibold" />
                        <select id="prospect_id" name="prospect_id" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                            <option value="">-- Aucun prospect associé --</option>
                            @foreach($prospects as $prospect)
                                <option value="{{ $prospect->id }}" {{ old('prospect_id', $task->prospect_id) == $prospect->id ? 'selected' : '' }}>
                                    {{ $prospect->prenom }} {{ $prospect->nom }} ({{ $prospect->entreprise ?? 'Particulier' }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('prospect_id')" class="mt-2 text-xs" />
                    </div>

                    <!-- Assigné à (Commercial) -->
                    <div>
                        <x-input-label for="user_id" value="Assigner à" class="text-slate-700 font-semibold" />
                        <select id="user_id" name="user_id" required {{ !auth()->user()->hasRole('Administrateur') ? 'disabled' : '' }} class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm disabled:opacity-50">
                            @if(!auth()->user()->hasRole('Administrateur'))
                                <option value="{{ auth()->id() }}" selected>{{ auth()->user()->name }}</option>
                            @else
                                <option value="">-- Sélectionnez un collaborateur --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id', $task->user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        @if(!auth()->user()->hasRole('Administrateur'))
                            <!-- Hidden field to submit user_id for commercials since select is disabled -->
                            <input type="hidden" name="user_id" value="{{ $task->user_id }}">
                        @endif
                        <x-input-error :messages="$errors->get('user_id')" class="mt-2 text-xs" />
                    </div>

                    <!-- Priorité -->
                    <div>
                        <x-input-label for="priorite" value="Niveau de priorité" class="text-slate-700 font-semibold" />
                        <select id="priorite" name="priorite" required class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                            <option value="Faible" {{ old('priorite', $task->priorite) === 'Faible' ? 'selected' : '' }}>Faible</option>
                            <option value="Moyenne" {{ old('priorite', $task->priorite) === 'Moyenne' ? 'selected' : '' }}>Moyenne</option>
                            <option value="Haute" {{ old('priorite', $task->priorite) === 'Haute' ? 'selected' : '' }}>Haute</option>
                            <option value="Urgente" {{ old('priorite', $task->priorite) === 'Urgente' ? 'selected' : '' }}>Urgente</option>
                        </select>
                        <x-input-error :messages="$errors->get('priorite')" class="mt-2 text-xs" />
                    </div>

                    <!-- Statut -->
                    <div>
                        <x-input-label for="statut" value="Statut de la tâche" class="text-slate-700 font-semibold" />
                        <select id="statut" name="statut" required class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                            <option value="À faire" {{ old('statut', $task->statut) === 'À faire' ? 'selected' : '' }}>À faire</option>
                            <option value="En cours" {{ old('statut', $task->statut) === 'En cours' ? 'selected' : '' }}>En cours</option>
                            <option value="Terminé" {{ old('statut', $task->statut) === 'Terminé' ? 'selected' : '' }}>Terminé</option>
                        </select>
                        <x-input-error :messages="$errors->get('statut')" class="mt-2 text-xs" />
                    </div>

                    <!-- Date Limite -->
                    <div class="md:col-span-2">
                        <x-input-label for="date_limite" value="Date et heure limite" class="text-slate-700 font-semibold" />
                        <x-text-input id="date_limite" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="datetime-local" name="date_limite" :value="old('date_limite', $task->date_limite ? $task->date_limite->format('Y-m-d\TH:i') : '')" />
                        <x-input-error :messages="$errors->get('date_limite')" class="mt-2 text-xs" />
                    </div>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="flex items-center justify-end space-x-3 pt-2">
                <a href="{{ route('tasks.index') }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 active:bg-slate-100 rounded-xl border border-slate-200 shadow-sm transition-all duration-150">
                    Annuler
                </a>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl shadow-md shadow-indigo-600/10 hover:shadow-indigo-600/20 transition-all duration-200">
                    Mettre à jour la tâche
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
