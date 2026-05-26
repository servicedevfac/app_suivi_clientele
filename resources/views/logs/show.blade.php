<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Détails du Journal d'Audit</h1>
                <p class="text-sm text-slate-500 mt-1">Détails complets de l'action enregistrée pour l'audit de traçabilité.</p>
            </div>
            <a href="{{ route('logs.index') }}" 
               class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 shadow-sm transition-all duration-150">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour aux journaux
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden p-6 space-y-6">
        @php
            $actionName = strtolower($log->action);
            $badgeColor = 'bg-slate-50 text-slate-700 border-slate-100';
            
            if (str_contains($actionName, 'connexion')) {
                $badgeColor = 'bg-indigo-50 text-indigo-700 border-indigo-100';
            } elseif (str_contains($actionName, 'déconnexion forcée') || str_contains($actionName, 'suppression')) {
                $badgeColor = 'bg-rose-50 text-rose-700 border-rose-100';
            } elseif (str_contains($actionName, 'déconnexion')) {
                $badgeColor = 'bg-slate-100 text-slate-700 border-slate-200';
            } elseif (str_contains($actionName, 'création') || str_contains($actionName, 'ajout')) {
                $badgeColor = 'bg-emerald-50 text-emerald-700 border-emerald-100';
            } elseif (str_contains($actionName, 'modification') || str_contains($actionName, 'mise à jour')) {
                $badgeColor = 'bg-sky-50 text-sky-700 border-sky-100';
            } elseif (str_contains($actionName, 'rôle') || str_contains($actionName, 'permission')) {
                $badgeColor = 'bg-amber-50 text-amber-700 border-amber-100';
            }
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b border-slate-100">
            <div>
                <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Action</span>
                <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-semibold border {{ $badgeColor }}">
                    {{ $log->action }}
                </span>
            </div>
            <div>
                <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Date & Heure</span>
                <p class="text-sm font-semibold text-slate-800">{{ $log->created_at->format('d/m/Y H:i:s') }}</p>
                <p class="text-xs text-slate-400 mt-0.5">Il y a {{ $log->created_at->diffForHumans() }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b border-slate-100">
            <div>
                <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Utilisateur / Acteur</span>
                @if($log->user)
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center font-bold text-slate-600 text-xs border border-slate-200 shadow-inner">
                            {{ strtoupper(substr($log->user->name ?? $log->user->nom, 0, 2)) }}
                        </div>
                        <div>
                            <div class="font-semibold text-slate-800 text-sm">{{ $log->user->prenom }} {{ $log->user->nom }}</div>
                            <div class="text-xs text-indigo-500 font-semibold uppercase tracking-wider leading-none">
                                {{ $log->user->getRoleNames()->first() ?? 'Collaborateur' }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-xl bg-rose-50 flex items-center justify-center font-bold text-rose-600 text-xs border border-rose-100 shadow-inner">
                            SYS
                        </div>
                        <div>
                            <div class="font-semibold text-rose-800 text-sm">Système / Visiteur</div>
                            <div class="text-xs text-rose-400 font-semibold uppercase tracking-wider leading-none">Anonyme</div>
                        </div>
                    </div>
                @endif
            </div>
            <div>
                <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Module</span>
                <p class="text-sm font-semibold text-slate-800 uppercase tracking-wide">{{ $log->module ?? '-' }}</p>
            </div>
        </div>

        <div class="pb-6 border-b border-slate-100">
            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Description</span>
            <div class="text-sm font-medium text-slate-700 bg-slate-50 border border-slate-100 p-4 rounded-xl leading-relaxed">
                {{ $log->description }}
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6">
            <div>
                <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Adresse IP</span>
                <p class="text-sm font-mono text-slate-800 font-semibold">{{ $log->ip_address ?? 'N/A' }}</p>
            </div>
            <div>
                <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Navigateur (User Agent)</span>
                <p class="text-xs font-mono text-slate-700 bg-slate-50 border border-slate-100 p-3 rounded-xl break-all leading-normal">
                    {{ $log->user_agent ?? 'N/A' }}
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
