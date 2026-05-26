<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
            <div class="flex items-center space-x-3">
                <a href="{{ route('users.index') }}" class="p-2 -ml-2 text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Détails de l'utilisateur</h1>
                    <p class="text-sm text-slate-500 mt-1">Consultez les informations et statistiques de cet utilisateur.</p>
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:text-indigo-600 rounded-xl shadow-sm transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Modifier
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Informations Générales -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-800">Profil de {{ $user->prenom }} {{ $user->nom }}</h3>
                @if($user->is_active)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                        Actif
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-50 text-rose-700 border border-rose-100">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span>
                        Inactif
                    </span>
                @endif
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Nom complet</span>
                    <span class="text-sm font-medium text-slate-800">{{ $user->prenom }} {{ $user->nom }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Email</span>
                    <a href="mailto:{{ $user->email }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">{{ $user->email }}</a>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Téléphone</span>
                    <span class="text-sm font-medium text-slate-800">{{ $user->telephone ?? 'Non renseigné' }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Date de création</span>
                    <span class="text-sm font-medium text-slate-800">{{ $user->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="md:col-span-2">
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Rôles assignés</span>
                    <div class="flex flex-wrap gap-2">
                        @forelse($user->roles as $role)
                            @php
                                $roleColors = [
                                    'Administrateur' => 'bg-violet-50 text-violet-700 border-violet-100',
                                    'Directeur Général' => 'bg-sky-50 text-sky-700 border-sky-100',
                                    'Responsable Commercial' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    'Commercial' => 'bg-amber-50 text-amber-700 border-amber-100',
                                ];
                                $color = $roleColors[$role->name] ?? 'bg-slate-50 text-slate-700 border-slate-100';
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold border {{ $color }}">
                                {{ $role->name }}
                            </span>
                        @empty
                            <span class="text-sm text-slate-500 italic">Aucun rôle</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques d'activité -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex flex-col items-center justify-center">
                <span class="text-3xl font-bold text-indigo-600 mb-2">{{ $user->prospects()->count() }}</span>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Prospects gérés</span>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex flex-col items-center justify-center">
                <span class="text-3xl font-bold text-amber-500 mb-2">{{ $user->relances()->count() }}</span>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Relances planifiées</span>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex flex-col items-center justify-center">
                <span class="text-3xl font-bold text-emerald-500 mb-2">{{ $user->tasks()->count() }}</span>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tâches assignées</span>
            </div>
        </div>
    </div>
</x-app-layout>
