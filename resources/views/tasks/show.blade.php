<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('tasks.index') }}" class="inline-flex items-center justify-center p-2 text-slate-500 hover:text-slate-700 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 shadow-sm transition-all duration-150">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Détails de la tâche</h1>
                <p class="text-sm text-slate-500 mt-1">Consultez les informations complètes et gérez l'état de cette tâche.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <!-- Header Card Banner -->
            <div class="bg-gradient-to-r from-indigo-900 to-slate-900 p-6 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider bg-white/10 rounded-lg text-indigo-200 border border-white/10">
                        Tâche #{{ $task->id }}
                    </span>
                    <h2 class="text-xl font-bold mt-2 tracking-tight">{{ $task->titre }}</h2>
                </div>
                <div class="flex items-center space-x-2">
                    <!-- Status badge -->
                    @if($task->statut === 'Terminé')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            Terminé
                        </span>
                    @elseif($task->statut === 'En cours')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-sky-500/20 text-sky-300 border border-sky-500/30">
                            En cours
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/10 text-slate-200 border border-white/20">
                            À faire
                        </span>
                    @endif
                </div>
            </div>

            <!-- Body Details -->
            <div class="p-6 space-y-6">
                <!-- Description -->
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Description / Instructions</h3>
                    <div class="mt-2 text-sm text-slate-700 bg-slate-50 rounded-xl p-4 border border-slate-100/80 whitespace-pre-line leading-relaxed">
                        {{ $task->description ?? 'Aucune description fournie.' }}
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                    <!-- Assignment Info -->
                    <div class="space-y-4">
                        <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Assigné à</h4>
                            <div class="flex items-center space-x-2.5 mt-2">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs border border-indigo-100/50">
                                    {{ strtoupper(substr($task->user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="text-xs font-semibold text-slate-800">{{ $task->user->name }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $task->user->email }}</div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Priorité</h4>
                            <div class="mt-2">
                                @if($task->priorite === 'Urgente')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-2 animate-pulse"></span>
                                        Priorité Urgente
                                    </span>
                                @elseif($task->priorite === 'Haute')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-2"></span>
                                        Priorité Haute
                                    </span>
                                @elseif($task->priorite === 'Moyenne')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-yellow-50 text-yellow-700 border border-yellow-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-2"></span>
                                        Priorité Moyenne
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-2"></span>
                                        Priorité Faible
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Scheduling Info -->
                    <div class="space-y-4">
                        <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Échéance</h4>
                            <div class="mt-2 text-xs font-semibold text-slate-700 flex items-center space-x-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span>
                                    {{ $task->date_limite ? $task->date_limite->format('d/m/Y à H:i') : 'Aucune date limite' }}
                                </span>
                                @if($task->date_limite && $task->date_limite->isPast() && $task->statut !== 'Terminé')
                                    <span class="text-rose-600 font-bold bg-rose-50 px-2 py-0.5 rounded-md border border-rose-100 animate-pulse text-[10px]">EN RETARD</span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Prospect Associé</h4>
                            <div class="mt-2">
                                @if($task->prospect)
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <a href="{{ route('prospects.show', $task->prospect) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-850 hover:underline">
                                            {{ $task->prospect->nom }} {{ $task->prospect->prenom }}
                                        </a>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">Aucun prospect associé à cette tâche</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Action Panel -->
            <div class="bg-slate-50 border-t border-slate-100 p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center space-x-2">
                    <a href="{{ route('tasks.edit', $task) }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 shadow-sm transition-all duration-150">
                        <svg class="w-3.5 h-3.5 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Éditer la tâche
                    </a>
                    
                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-xl border border-rose-100 shadow-sm transition-all duration-150">
                            Supprimer
                        </button>
                    </form>
                </div>

                @if($task->statut !== 'Terminé')
                    <form action="{{ route('tasks.update', $task) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="titre" value="{{ $task->titre }}">
                        <input type="hidden" name="user_id" value="{{ $task->user_id }}">
                        <input type="hidden" name="priorite" value="{{ $task->priorite }}">
                        <input type="hidden" name="statut" value="Terminé">
                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 rounded-xl shadow-md shadow-emerald-600/10 hover:shadow-emerald-600/20 transition-all duration-200">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Marquer comme terminée
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
