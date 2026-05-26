<div id="task-card-{{ $task->id }}" 
     class="bg-white p-4 rounded-xl border border-slate-200/85 hover:border-indigo-400/80 hover:shadow-md transition-all duration-200 cursor-grab active:cursor-grabbing flex flex-col space-y-3.5 relative group" 
     draggable="true" 
     ondragstart="onDragStart(event)" 
     data-task-id="{{ $task->id }}">
    
    <!-- Card Header: Priority & Date Limit -->
    <div class="flex items-center justify-between">
        <!-- Priority Badge -->
        <div>
            @if($task->priorite === 'Urgente')
                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1 animate-pulse"></span>
                    Urgente
                </span>
            @elseif($task->priorite === 'Haute')
                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1"></span>
                    Haute
                </span>
            @elseif($task->priorite === 'Moyenne')
                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-yellow-50 text-yellow-750 border border-yellow-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1"></span>
                    Moyenne
                </span>
            @else
                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1"></span>
                    Faible
                </span>
            @endif
        </div>

        <!-- Date Limit -->
        <div class="text-[10px] flex items-center space-x-1">
            @if($task->date_limite)
                <span class="font-semibold flex items-center @if($task->date_limite->isPast() && $task->statut !== 'Terminé') text-rose-600 font-bold bg-rose-50 px-1.5 py-0.5 rounded border border-rose-100/50 animate-pulse @else text-slate-500 @endif" title="Date limite">
                    <svg class="w-3 h-3 mr-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    {{ $task->date_limite->format('d/m H:i') }}
                </span>
            @else
                <span class="text-slate-400">—</span>
            @endif
        </div>
    </div>

    <!-- Card Body: Title & Description -->
    <div>
        <h4 class="font-bold text-slate-800 text-xs sm:text-sm leading-snug group-hover:text-indigo-900 transition-colors line-clamp-2" title="{{ $task->titre }}">
            {{ $task->titre }}
        </h4>
        @if($task->description)
            <p class="text-[11px] text-slate-400 mt-1 line-clamp-2 leading-relaxed">
                {{ $task->description }}
            </p>
        @endif
    </div>

    <!-- Associated Prospect badge -->
    @if($task->prospect)
        <div class="pt-1.5 border-t border-slate-100 flex items-center">
            <span class="inline-flex items-center text-[10px] font-semibold text-indigo-600 hover:text-indigo-900 transition-colors">
                <svg class="w-3.5 h-3.5 mr-1 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <a href="{{ route('prospects.show', $task->prospect) }}" class="hover:underline">
                    {{ $task->prospect->nom }} {{ $task->prospect->prenom }}
                </a>
            </span>
        </div>
    @endif

    <!-- Card Footer: Assignee & Action Buttons -->
    <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
        <!-- Assignee -->
        <div class="flex items-center space-x-1.5">
            <div class="w-6 h-6 rounded-md bg-indigo-50 text-indigo-600 border border-indigo-100/50 flex items-center justify-center font-bold text-[9px]" title="{{ $task->user->name }}">
                {{ strtoupper(substr($task->user->name, 0, 2)) }}
            </div>
            <span class="text-[10px] text-slate-500 font-semibold truncate max-w-[80px]" title="{{ $task->user->name }}">
                {{ $task->user->name }}
            </span>
        </div>

        <!-- Action buttons -->
        <div class="flex items-center space-x-1">
            @if($task->statut !== 'Terminé')
                <form action="{{ route('tasks.update', $task) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="titre" value="{{ $task->titre }}">
                    <input type="hidden" name="user_id" value="{{ $task->user_id }}">
                    <input type="hidden" name="priorite" value="{{ $task->priorite }}">
                    <input type="hidden" name="statut" value="Terminé">
                    <button type="submit" class="p-1 text-emerald-600 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 rounded-md transition-colors" title="Terminer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </button>
                </form>
            @endif
            
            <a href="{{ route('tasks.show', $task) }}" class="p-1 text-slate-600 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 rounded-md transition-colors" title="Détails">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
            </a>
            
            <a href="{{ route('tasks.edit', $task) }}" class="p-1 text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 rounded-md transition-colors" title="Éditer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </a>

            <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette tâche ?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="p-1 text-rose-600 hover:text-rose-900 bg-rose-50 hover:bg-rose-100 rounded-md transition-colors" title="Supprimer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>
