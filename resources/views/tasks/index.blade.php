<x-app-layout>
    @php
        $currentView = request('view', 'list');
    @endphp

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Tâches Commerciales</h1>
                <p class="text-sm text-slate-500 mt-1">Gérez et suivez le statut des tâches quotidiennes et les relances prospects.</p>
            </div>
            
            <div class="flex items-center space-x-4">
                <!-- Toggler Kanban / List -->
                <div class="bg-slate-200/60 p-0.5 rounded-xl flex items-center shadow-inner border border-slate-100">
                    <button id="btn-view-list" 
                            class="px-3.5 py-1.5 rounded-lg text-xs font-bold flex items-center transition-all duration-200 {{ $currentView === 'list' ? 'bg-white text-indigo-600 shadow-md border border-slate-100/10' : 'text-slate-500 hover:text-slate-800' }}" 
                            onclick="switchView('list')">
                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        Liste
                    </button>
                    <button id="btn-view-kanban" 
                            class="px-3.5 py-1.5 rounded-lg text-xs font-bold flex items-center transition-all duration-200 {{ $currentView === 'kanban' ? 'bg-white text-indigo-600 shadow-md border border-slate-100/10' : 'text-slate-500 hover:text-slate-800' }}" 
                            onclick="switchView('kanban')">
                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path>
                        </svg>
                        Kanban
                    </button>
                </div>

                <a href="{{ route('tasks.create') }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl shadow-md shadow-indigo-600/10 hover:shadow-indigo-600/20 transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Créer une tâche
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Session Notifications -->
    @if (session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center space-x-3 shadow-sm">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-xs font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Statistics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
        <!-- Card Total -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Total Tâches</p>
                <h3 class="text-xl font-bold text-slate-800 mt-0.5">
                    {{ $currentView === 'list' ? $tasks->total() : $tasks->count() }}
                </h3>
            </div>
        </div>

        <!-- Card Pending -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">En attente / En cours</p>
                <h3 class="text-xl font-bold text-slate-800 mt-0.5">
                    {{ \App\Models\Task::whereIn('statut', ['À faire', 'En cours'])->when(!auth()->user()->hasRole('Administrateur'), function($q) { $q->where('user_id', auth()->id()); })->count() }}
                </h3>
            </div>
        </div>

        <!-- Card Completed -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Terminées</p>
                <h3 class="text-xl font-bold text-slate-800 mt-0.5">
                    {{ \App\Models\Task::where('statut', 'Terminé')->when(!auth()->user()->hasRole('Administrateur'), function($q) { $q->where('user_id', auth()->id()); })->count() }}
                </h3>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('tasks.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Conserver la vue courante -->
            <input type="hidden" name="view" value="{{ $currentView }}">

            <!-- Filter Statut -->
            <div>
                <label for="statut" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Statut</label>
                <select name="statut" id="statut" 
                        class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                    <option value="">Tous les statuts</option>
                    <option value="À faire" {{ request('statut') == 'À faire' ? 'selected' : '' }}>À faire</option>
                    <option value="En cours" {{ request('statut') == 'En cours' ? 'selected' : '' }}>En cours</option>
                    <option value="Terminé" {{ request('statut') == 'Terminé' ? 'selected' : '' }}>Terminé</option>
                </select>
            </div>

            <!-- Filter Priorité -->
            <div>
                <label for="priorite" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Priorité</label>
                <select name="priorite" id="priorite" 
                        class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                    <option value="">Toutes les priorités</option>
                    <option value="Faible" {{ request('priorite') == 'Faible' ? 'selected' : '' }}>Faible</option>
                    <option value="Moyenne" {{ request('priorite') == 'Moyenne' ? 'selected' : '' }}>Moyenne</option>
                    <option value="Haute" {{ request('priorite') == 'Haute' ? 'selected' : '' }}>Haute</option>
                    <option value="Urgente" {{ request('priorite') == 'Urgente' ? 'selected' : '' }}>Urgente</option>
                </select>
            </div>

            <!-- Filter Prospect -->
            <div>
                <label for="prospect_id" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Prospect associé</label>
                <select name="prospect_id" id="prospect_id" 
                        class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                    <option value="">Tous les prospects</option>
                    @foreach($prospects as $prospect)
                        <option value="{{ $prospect->id }}" {{ request('prospect_id') == $prospect->id ? 'selected' : '' }}>
                            {{ $prospect->nom }} {{ $prospect->prenom }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter User (Admin only) -->
            <div class="flex items-end space-x-2">
                <div class="flex-1">
                    <label for="user_id" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Assigné à</label>
                    <select name="user_id" id="user_id" 
                            {{ !auth()->user()->hasRole('Administrateur') ? 'disabled' : '' }}
                            class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all disabled:opacity-50">
                        @if(!auth()->user()->hasRole('Administrateur'))
                            <option value="{{ auth()->id() }}" selected>{{ auth()->user()->name }}</option>
                        @else
                            <option value="">Tous les collaborateurs</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-slate-800 hover:bg-slate-900 rounded-xl transition-all h-[38px] flex items-center justify-center">
                    Filtrer
                </button>
                @if(request()->anyFilled(['statut', 'priorite', 'prospect_id', 'user_id']))
                    <a href="{{ route('tasks.index', ['view' => $currentView]) }}" class="px-3 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all h-[38px] flex items-center justify-center">
                        Effacer
                    </a>
                @endif
            </div>
        </form>
    </div>

    @if($currentView === 'list')
        <!-- LIST VIEW -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Tâche</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Prospect lié</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Assigné à</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Priorité</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Date Limite</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($tasks as $task)
                            <tr class="hover:bg-slate-50/30 transition-colors duration-150">
                                <td class="px-6 py-4 max-w-xs">
                                    <div>
                                        <div class="font-semibold text-slate-800 text-sm truncate" title="{{ $task->titre }}">
                                            {{ $task->titre }}
                                        </div>
                                        @if($task->description)
                                            <div class="text-[11px] text-slate-400 mt-0.5 line-clamp-1">
                                                {{ $task->description }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($task->prospect)
                                        <a href="{{ route('prospects.show', $task->prospect) }}" class="text-indigo-600 hover:text-indigo-900 text-xs font-semibold">
                                            {{ $task->prospect->nom }} {{ $task->prospect->prenom }}
                                        </a>
                                    @else
                                        <span class="text-slate-400 text-xs">Aucun</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-slate-700 text-xs font-semibold">{{ $task->user->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($task->priorite === 'Urgente')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-50 text-rose-700 border border-rose-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5 animate-pulse"></span>
                                            Urgente
                                        </span>
                                    @elseif($task->priorite === 'Haute')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                            Haute
                                        </span>
                                    @elseif($task->priorite === 'Moyenne')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1.5"></span>
                                            Moyenne
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5"></span>
                                            Faible
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs">
                                    @if($task->date_limite)
                                        <span class="font-medium @if($task->date_limite->isPast() && $task->statut !== 'Terminé') text-rose-600 font-bold @else text-slate-600 @endif">
                                            {{ $task->date_limite->format('d/m/Y H:i') }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($task->statut === 'Terminé')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                            Terminé
                                        </span>
                                    @elseif($task->statut === 'En cours')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-sky-50 text-sky-700 border border-sky-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-sky-500 mr-1.5"></span>
                                            En cours
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400 mr-1.5"></span>
                                            À faire
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-semibold space-x-1">
                                    @if($task->statut !== 'Terminé')
                                        <form action="{{ route('tasks.update', $task) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="titre" value="{{ $task->titre }}">
                                            <input type="hidden" name="user_id" value="{{ $task->user_id }}">
                                            <input type="hidden" name="priorite" value="{{ $task->priorite }}">
                                            <input type="hidden" name="statut" value="Terminé">
                                            <button type="submit" class="inline-flex items-center justify-center p-2 text-emerald-600 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors" title="Marquer comme terminée">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('tasks.show', $task) }}" class="inline-flex items-center justify-center p-2 text-slate-600 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 rounded-lg transition-colors" title="Détails">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('tasks.edit', $task) }}" class="inline-flex items-center justify-center p-2 text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors" title="Éditer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center p-2 text-rose-600 hover:text-rose-900 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors" title="Supprimer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-sm">
                                    <svg class="w-10 h-10 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    Aucune tâche trouvée.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            @if($tasks->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $tasks->links() }}
                </div>
            @endif
        </div>
    @else
        <!-- KANBAN VIEW (Drag & Drop) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            <!-- Column 1: À faire -->
            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/60 flex flex-col min-h-[600px] shadow-sm">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-200">
                    <div class="flex items-center space-x-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-400 shadow-sm"></span>
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">À faire</h3>
                    </div>
                    <span class="px-2.5 py-0.5 text-xs font-bold bg-slate-200 text-slate-700 rounded-lg shadow-sm border border-slate-300/10">
                        {{ $tasks->where('statut', 'À faire')->count() }}
                    </span>
                </div>
                
                <div class="flex-1 space-y-3.5 kanban-column py-2 transition-colors duration-250 rounded-xl" 
                     data-status="À faire" 
                     ondragover="onDragOver(event)" 
                     ondragleave="onDragLeave(event)" 
                     ondrop="onDrop(event)">
                    @forelse($tasks->where('statut', 'À faire') as $task)
                        @include('tasks.kanban-card', ['task' => $task])
                    @empty
                        <div class="py-16 text-center text-slate-400 text-xs font-medium empty-placeholder bg-white/40 border border-dashed border-slate-200 rounded-xl">
                            Aucune tâche en attente
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Column 2: En cours -->
            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/60 flex flex-col min-h-[600px] shadow-sm">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-200">
                    <div class="flex items-center space-x-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 shadow-sm shadow-indigo-200"></span>
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">En cours</h3>
                    </div>
                    <span class="px-2.5 py-0.5 text-xs font-bold bg-indigo-50 text-indigo-700 rounded-lg shadow-sm border border-indigo-100/50">
                        {{ $tasks->where('statut', 'En cours')->count() }}
                    </span>
                </div>
                
                <div class="flex-1 space-y-3.5 kanban-column py-2 transition-colors duration-250 rounded-xl" 
                     data-status="En cours" 
                     ondragover="onDragOver(event)" 
                     ondragleave="onDragLeave(event)" 
                     ondrop="onDrop(event)">
                    @forelse($tasks->where('statut', 'En cours') as $task)
                        @include('tasks.kanban-card', ['task' => $task])
                    @empty
                        <div class="py-16 text-center text-slate-400 text-xs font-medium empty-placeholder bg-white/40 border border-dashed border-slate-200 rounded-xl">
                            Aucune tâche en cours
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Column 3: Terminé -->
            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/60 flex flex-col min-h-[600px] shadow-sm">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-200">
                    <div class="flex items-center space-x-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-sm shadow-emerald-200"></span>
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Terminé</h3>
                    </div>
                    <span class="px-2.5 py-0.5 text-xs font-bold bg-emerald-50 text-emerald-700 rounded-lg shadow-sm border border-emerald-100/50">
                        {{ $tasks->where('statut', 'Terminé')->count() }}
                    </span>
                </div>
                
                <div class="flex-1 space-y-3.5 kanban-column py-2 transition-colors duration-250 rounded-xl" 
                     data-status="Terminé" 
                     ondragover="onDragOver(event)" 
                     ondragleave="onDragLeave(event)" 
                     ondrop="onDrop(event)">
                    @forelse($tasks->where('statut', 'Terminé') as $task)
                        @include('tasks.kanban-card', ['task' => $task])
                    @empty
                        <div class="py-16 text-center text-slate-400 text-xs font-medium empty-placeholder bg-white/40 border border-dashed border-slate-200 rounded-xl">
                            Aucune tâche terminée
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    <!-- Dynamic JavaScript scripts -->
    <script>
        // Synchroniser localStorage avec l'URL au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const currentView = urlParams.get('view') || 'list';
            localStorage.setItem('tasks_view', currentView);
        });

        // Fonction pour changer de vue (Kanban / Liste)
        function switchView(view) {
            localStorage.setItem('tasks_view', view);
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('view', view);
            window.location.search = urlParams.toString();
        }

        /* ----------------------------------------------------
           Drag & Drop API handlers
           ---------------------------------------------------- */
        function onDragStart(event) {
            event.dataTransfer.setData("text/plain", event.currentTarget.dataset.taskId);
            event.dataTransfer.effectAllowed = "move";
            
            // Effet d'opacité au drag
            const element = event.currentTarget;
            setTimeout(() => {
                element.classList.add('opacity-45', 'scale-[0.98]');
            }, 0);
        }

        document.addEventListener("dragend", function(event) {
            const dragElements = document.querySelectorAll('.opacity-45');
            dragElements.forEach(el => {
                el.classList.remove('opacity-45', 'scale-[0.98]');
            });
        });

        function onDragOver(event) {
            event.preventDefault();
            event.dataTransfer.dropEffect = "move";
            
            const column = event.currentTarget;
            if (column.classList.contains('kanban-column')) {
                column.classList.add('bg-indigo-50/40', 'border-2', 'border-dashed', 'border-indigo-300');
            }
        }

        function onDragLeave(event) {
            const column = event.currentTarget;
            if (column.classList.contains('kanban-column')) {
                column.classList.remove('bg-indigo-50/40', 'border-2', 'border-dashed', 'border-indigo-300');
            }
        }

        function onDrop(event) {
            event.preventDefault();
            const column = event.currentTarget;
            column.classList.remove('bg-indigo-50/40', 'border-2', 'border-dashed', 'border-indigo-300');
            
            const taskId = event.dataTransfer.getData("text/plain");
            const newStatus = column.dataset.status;
            const card = document.getElementById("task-card-" + taskId);
            
            if (!card) return;
            
            // Si la carte est déposée dans la même colonne qu'au départ, on ne fait rien
            if (card.parentElement === column) return;
            
            const oldColumn = card.parentElement;
            
            // Optimistic UI : Déplacer visuellement l'élément tout de suite
            column.appendChild(card);
            
            // Gérer le placeholder de la colonne de dépôt
            const targetPlaceholder = column.querySelector('.empty-placeholder');
            if (targetPlaceholder) {
                targetPlaceholder.classList.add('hidden');
            }
            
            // Gérer le placeholder de la colonne de départ si elle se retrouve vide
            const remainingCards = oldColumn.querySelectorAll('[id^="task-card-"]');
            if (remainingCards.length === 0) {
                const oldPlaceholder = oldColumn.querySelector('.empty-placeholder');
                if (oldPlaceholder) {
                    oldPlaceholder.classList.remove('hidden');
                }
            }
            
            // Mettre à jour immédiatement les badges de compteurs de colonnes
            updateColumnCounters();
            
            // Envoyer la requête Ajax PATCH au serveur
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            fetch(`/tasks/${taskId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ statut: newStatus })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errData => {
                        throw new Error(errData.error || 'Erreur lors du déplacement');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showToast('Succès', 'Statut de la tâche "' + data.task.titre + '" mis à jour.', 'success');
                } else {
                    throw new Error(data.error || 'Erreur lors du traitement.');
                }
            })
            .catch(error => {
                // Revenir en arrière en cas d'erreur
                oldColumn.appendChild(card);
                
                // Mettre à jour les placeholders
                if (column.querySelectorAll('[id^="task-card-"]').length === 0) {
                    const targetPlaceholder = column.querySelector('.empty-placeholder');
                    if (targetPlaceholder) targetPlaceholder.classList.remove('hidden');
                }
                const oldPlaceholder = oldColumn.querySelector('.empty-placeholder');
                if (oldPlaceholder) {
                    oldPlaceholder.classList.add('hidden');
                }
                
                updateColumnCounters();
                
                showToast('Erreur', error.message || 'Action non autorisée ou erreur réseau.', 'error');
            });
        }

        function updateColumnCounters() {
            document.querySelectorAll('.kanban-column').forEach(column => {
                const count = column.querySelectorAll('[id^="task-card-"]').length;
                const header = column.previousElementSibling;
                if (header) {
                    const badge = header.querySelector('span');
                    if (badge) {
                        badge.textContent = count;
                    }
                }
            });
        }

        /* ----------------------------------------------------
           Custom Beautiful Toasts Notification Builder
           ---------------------------------------------------- */
        function showToast(title, message, type = 'success') {
            let container = document.getElementById('toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toast-container';
                container.className = 'fixed bottom-6 right-6 z-55 space-y-2.5 max-w-sm w-full';
                document.body.appendChild(container);
            }
            
            const toast = document.createElement('div');
            const bgClass = type === 'success' 
                ? 'bg-emerald-50 border-emerald-250 text-emerald-900 shadow-emerald-100/50' 
                : 'bg-rose-50 border-rose-250 text-rose-900 shadow-rose-100/50';
            const iconColor = type === 'success' ? 'text-emerald-600' : 'text-rose-600';
            const iconSvg = type === 'success' 
                ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>' 
                : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>';
            
            toast.className = `flex items-start p-4 border rounded-2xl shadow-xl transition-all duration-300 transform translate-y-6 opacity-0 ${bgClass}`;
            toast.innerHTML = `
                <svg class="w-5.5 h-5.5 mr-3 flex-shrink-0 ${iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">${iconSvg}</svg>
                <div class="flex-1">
                    <div class="font-extrabold text-xs tracking-wide uppercase">${title}</div>
                    <div class="text-[11px] font-semibold mt-1 opacity-80 leading-relaxed">${message}</div>
                </div>
            `;
            
            container.appendChild(toast);
            
            // Animation Entrée
            setTimeout(() => {
                toast.classList.remove('translate-y-6', 'opacity-0');
            }, 10);
            
            // Animation Sortie & Suppression
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 4500);
        }
    </script>
</x-app-layout>

