<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Objectifs Commerciaux') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                
                <form method="GET" action="{{ route('objectifs.index') }}" class="mb-6 flex items-end space-x-4">
                    <div>
                        <x-input-label for="mois" value="Mois" />
                        <x-text-input id="mois" type="month" name="mois" value="{{ $currentMonth }}" class="mt-1" onchange="this.form.submit()" />
                    </div>
                </form>

                <form method="POST" action="{{ route('objectifs.store') }}">
                    @csrf
                    <input type="hidden" name="mois" value="{{ $currentMonth }}">
                    
                    <div class="overflow-x-auto mb-6">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                                <tr>
                                    <th class="px-6 py-3 font-semibold">Commercial</th>
                                    <th class="px-6 py-3 font-semibold">Objectif Mensuel (€)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($commercials as $commercial)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4 font-semibold text-slate-900 flex items-center space-x-3">
                                            @if($commercial->photo)
                                                <img src="{{ Storage::url($commercial->photo) }}" class="h-8 w-8 rounded-full object-cover">
                                            @else
                                                <div class="h-8 w-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-sm">
                                                    {{ substr($commercial->prenom, 0, 1) }}{{ substr($commercial->nom, 0, 1) }}
                                                </div>
                                            @endif
                                            <span>{{ $commercial->name }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <x-text-input type="number" step="0.01" min="0" name="objectifs[{{ $commercial->id }}][montant_cible]" value="{{ isset($objectifs[$commercial->id]) ? $objectifs[$commercial->id]->montant_cible : 0 }}" class="w-48" />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-md transition-all duration-200">
                            Enregistrer les objectifs
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
