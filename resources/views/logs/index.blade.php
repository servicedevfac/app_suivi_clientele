<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Journaux d'Audit & Traçabilité</h1>
            <p class="text-sm text-slate-500 mt-1">Historique complet des actions, connexions et événements de sécurité du CRM.</p>
        </div>
    </x-slot>

    <!-- Table Container Card -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden" x-data="{ selectedLog: null }">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Date & Heure</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Utilisateur</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Action</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Module</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Adresse IP</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Détails</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($logs as $log)
                        @php
                            // Attribution des couleurs de badge en fonction de la sévérité de l'action
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
                        <tr class="hover:bg-slate-50/30 transition-colors duration-150">
                            <!-- Date & Heure -->
                            <td class="px-6 py-4 whitespace-nowrap text-slate-600 text-xs font-medium">
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <!-- Utilisateur -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($log->user)
                                    <div class="flex items-center space-x-2.5">
                                        <div class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center font-bold text-slate-600 text-[10px] border border-slate-200 shadow-inner">
                                            {{ strtoupper(substr($log->user->name ?? $log->user->nom, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-slate-800 text-xs">{{ $log->user->prenom }} {{ $log->user->nom }}</div>
                                            <div class="text-[9px] text-indigo-500 font-semibold uppercase tracking-wider leading-none">
                                                {{ $log->user->getRoleNames()->first() ?? 'Collaborateur' }}
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center space-x-2.5">
                                        <div class="w-7 h-7 rounded-lg bg-rose-50 flex items-center justify-center font-bold text-rose-600 text-[10px] border border-rose-100 shadow-inner">
                                            SYS
                                        </div>
                                        <div>
                                            <div class="font-semibold text-rose-800 text-xs">Système / Visiteur</div>
                                            <div class="text-[9px] text-rose-400 font-semibold uppercase tracking-wider leading-none">Anonyme</div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <!-- Action Badge -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-semibold border {{ $badgeColor }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <!-- Module -->
                            <td class="px-6 py-4 whitespace-nowrap text-slate-500 text-xs font-semibold uppercase tracking-wide">
                                {{ $log->module ?? '-' }}
                            </td>
                            <!-- Description -->
                            <td class="px-6 py-4 text-slate-600 text-xs font-medium max-w-xs truncate" title="{{ $log->description }}">
                                {{ $log->description }}
                            </td>
                            <!-- Adresse IP -->
                            <td class="px-6 py-4 whitespace-nowrap text-slate-500 text-xs font-mono">
                                {{ $log->ip_address ?? '127.0.0.1' }}
                            </td>
                            <!-- Détails techniques (expandable) -->
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <button type="button" 
                                        @click="selectedLog = { 
                                            id: '{{ $log->id }}',
                                            date: '{{ $log->created_at->format('d/m/Y H:i:s') }}',
                                            user: '{{ $log->user ? $log->user->prenom . ' ' . $log->user->nom : 'Système/Visiteur' }}',
                                            action: '{{ $log->action }}',
                                            module: '{{ $log->module ?? '-' }}',
                                            description: '{{ addslashes($log->description) }}',
                                            ip: '{{ $log->ip_address ?? '127.0.0.1' }}',
                                            agent: '{{ addslashes($log->user_agent) }}'
                                        }"
                                        class="inline-flex items-center justify-center p-1.5 text-slate-500 hover:text-indigo-600 bg-slate-50 hover:bg-indigo-50 border border-slate-200 hover:border-indigo-100 rounded-lg transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-sm">
                                <svg class="w-10 h-10 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Aucun journal d'activité enregistré pour le moment.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $logs->links() }}
            </div>
        @endif

        <!-- Tiroir de détails (Drawer Panel) -->
        <div x-show="selectedLog" 
             class="fixed inset-y-0 right-0 max-w-full flex pl-10 z-50"
             x-description="Slide-over panel, show/hide based on slide-over state."
             style="display: none;"
             @keydown.window.escape="selectedLog = null">
            
            <!-- Backdrop -->
            <div x-show="selectedLog"
                 x-transition:enter="ease-in-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in-out duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="selectedLog = null"
                 class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity"></div>

            <!-- Drawer Container -->
            <div x-show="selectedLog"
                 x-transition:enter="transform transition ease-in-out duration-300 sm:duration-500"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transform transition ease-in-out duration-300 sm:duration-500"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full"
                 class="w-screen max-w-md relative">
                
                <!-- Close Button -->
                <div class="absolute top-0 left-0 -ml-10 pt-4 pr-2 flex sm:-ml-12 sm:pt-4 sm:pr-4">
                    <button type="button" 
                            @click="selectedLog = null"
                            class="rounded-md text-slate-300 hover:text-white focus:outline-none focus:ring-2 focus:ring-white">
                        <span class="sr-only">Fermer le panneau</span>
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Panel Content -->
                <div class="h-full flex flex-col bg-white shadow-xl overflow-y-auto">
                    <!-- Drawer Header -->
                    <div class="py-6 px-6 bg-slate-900 text-white flex-shrink-0">
                        <span class="text-[9px] uppercase font-bold tracking-widest text-indigo-400 block mb-1">Détails de l'audit</span>
                        <h2 class="text-lg font-bold truncate" x-text="selectedLog ? selectedLog.action : ''"></h2>
                        <p class="text-xs text-slate-400 mt-1" x-text="selectedLog ? selectedLog.date : ''"></p>
                    </div>

                    <!-- Drawer Body -->
                    <div class="flex-1 py-6 px-6 space-y-6 text-xs text-slate-600">
                        <!-- Utilisateur -->
                        <div>
                            <span class="block font-bold text-slate-500 uppercase tracking-wider mb-1">Acteur</span>
                            <p class="text-sm font-semibold text-slate-800" x-text="selectedLog ? selectedLog.user : ''"></p>
                        </div>

                        <!-- Module -->
                        <div>
                            <span class="block font-bold text-slate-500 uppercase tracking-wider mb-1">Module</span>
                            <p class="text-sm font-semibold text-slate-800" x-text="selectedLog ? selectedLog.module : ''"></p>
                        </div>

                        <!-- Description -->
                        <div>
                            <span class="block font-bold text-slate-500 uppercase tracking-wider mb-1">Description</span>
                            <p class="text-sm font-medium text-slate-700 bg-slate-50 border border-slate-100 p-3 rounded-xl leading-relaxed" x-text="selectedLog ? selectedLog.description : ''"></p>
                        </div>

                        <!-- IP -->
                        <div>
                            <span class="block font-bold text-slate-500 uppercase tracking-wider mb-1">Adresse IP</span>
                            <p class="text-sm font-mono text-slate-800" x-text="selectedLog ? selectedLog.ip : ''"></p>
                        </div>

                        <!-- User Agent -->
                        <div>
                            <span class="block font-bold text-slate-500 uppercase tracking-wider mb-1">Navigateur / User Agent</span>
                            <p class="text-xs font-mono text-slate-700 bg-slate-50 border border-slate-100 p-3 rounded-xl break-all leading-normal" x-text="selectedLog ? selectedLog.agent : 'N/A'"></p>
                        </div>
                    </div>

                    <!-- Drawer Footer -->
                    <div class="py-4 px-6 border-t border-slate-100 bg-slate-50 text-right flex-shrink-0">
                        <button type="button" 
                                @click="selectedLog = null" 
                                class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 shadow-sm transition-all duration-150">
                            Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
