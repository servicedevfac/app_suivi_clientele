<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('relances.index') }}" class="inline-flex items-center justify-center p-2 text-slate-500 hover:text-slate-700 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 shadow-sm transition-all duration-150">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Détails de la relance</h1>
                <p class="text-sm text-slate-500 mt-1">Consultez les informations complètes et modifiez le statut de cette relance prospect.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <!-- Header Card Banner -->
            <div class="bg-gradient-to-r from-slate-900 to-indigo-950 p-6 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider bg-white/10 rounded-lg text-indigo-200 border border-white/10">
                        Relance #{{ $relance->id }}
                    </span>
                    <h2 class="text-xl font-bold mt-2 tracking-tight">
                        Relance pour {{ $relance->prospect ? "{$relance->prospect->prenom} {$relance->prospect->nom}" : 'Prospect inconnu' }}
                    </h2>
                </div>
                <div>
                    <!-- Status badge -->
                    @if($relance->statut === 'Réalisée')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            Réalisée
                        </span>
                    @elseif($relance->statut === 'Annulée')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-500/20 text-slate-300 border border-slate-500/30">
                            Annulée
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-500/20 text-yellow-300 border border-yellow-500/30">
                            En attente
                        </span>
                    @endif
                </div>
            </div>

            <!-- Body Details -->
            <div class="p-6 space-y-6">
                <!-- Commentaire / Notes -->
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Commentaire & Objectifs</h3>
                    <div class="mt-2 text-sm text-slate-700 bg-slate-50 rounded-xl p-4 border border-slate-100/80 whitespace-pre-line leading-relaxed">
                        {{ $relance->commentaire ?? 'Aucun commentaire ou objectif n\'a été spécifié.' }}
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                    <!-- Prospect details -->
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Prospect Cible</h4>
                        @if($relance->prospect)
                            <div class="mt-2 space-y-1">
                                <div class="text-sm font-semibold text-slate-800">
                                    <a href="{{ route('prospects.show', $relance->prospect) }}" class="text-indigo-600 hover:text-indigo-900 hover:underline">
                                        {{ $relance->prospect->prenom }} {{ $relance->prospect->nom }}
                                    </a>
                                </div>
                                <div class="text-xs text-slate-500 font-medium">{{ $relance->prospect->entreprise ?? 'Particulier' }}</div>
                                <div class="text-[11px] text-slate-400 mt-1">{{ $relance->prospect->email }} • {{ $relance->prospect->telephone }}</div>
                            </div>
                        @else
                            <div class="text-xs text-slate-400 italic mt-2">Prospect introuvable</div>
                        @endif
                    </div>

                    <!-- Relance details -->
                    <div class="space-y-4">
                        <!-- Date & Time -->
                        <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider font-semibold">Date de contact</h4>
                            <div class="mt-1 text-xs text-slate-700 font-semibold flex items-center space-x-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span>
                                    {{ $relance->date_relance ? $relance->date_relance->format('d/m/Y') : '' }}
                                    à {{ $relance->heure_relance ? \Carbon\Carbon::parse($relance->heure_relance)->format('H:i') : 'Pas d\'heure spécifiée' }}
                                </span>
                                @if($relance->date_relance && $relance->date_relance->isPast() && $relance->statut === 'En attente')
                                    <span class="text-rose-600 font-bold bg-rose-50 px-2 py-0.5 rounded-md border border-rose-100 animate-pulse text-[10px]">EN RETARD</span>
                                @endif
                            </div>
                        </div>

                        <!-- Canal & Commercial -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Canal</h4>
                                <div class="mt-1 text-xs font-bold text-slate-700">
                                    @if($relance->canal === 'Appel')
                                        📞 Appel
                                    @elseif($relance->canal === 'WhatsApp')
                                        💬 WhatsApp
                                    @elseif($relance->canal === 'Email')
                                        ✉️ Email
                                    @elseif($relance->canal === 'SMS')
                                        📱 SMS
                                    @elseif($relance->canal === 'Rendez-vous')
                                        🤝 Rendez-vous
                                    @else
                                        ❓ Autre
                                    @endif
                                </div>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Commercial</h4>
                                <div class="mt-1 text-xs font-bold text-slate-700">
                                    {{ $relance->commercial->name ?? 'Non assigné' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Action Panel -->
            <div class="bg-slate-50 border-t border-slate-100 p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center space-x-2">
                    <a href="{{ route('relances.edit', $relance) }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 shadow-sm transition-all duration-150">
                        <svg class="w-3.5 h-3.5 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Éditer la relance
                    </a>
                    
                    <form action="{{ route('relances.destroy', $relance) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette relance ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-xl border border-rose-100 shadow-sm transition-all duration-150">
                            Supprimer
                        </button>
                    </form>
                </div>

                @if($relance->statut === 'En attente')
                    <form action="{{ route('relances.update', $relance) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="prospect_id" value="{{ $relance->prospect_id }}">
                        <input type="hidden" name="commercial_id" value="{{ $relance->commercial_id }}">
                        <input type="hidden" name="date_relance" value="{{ $relance->date_relance ? $relance->date_relance->format('Y-m-d') : '' }}">
                        <input type="hidden" name="canal" value="{{ $relance->canal }}">
                        <input type="hidden" name="statut" value="Réalisée">
                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 rounded-xl shadow-md shadow-emerald-600/10 hover:shadow-emerald-600/20 transition-all duration-200">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Marquer comme réalisée
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
