<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('ventes.index') }}" class="inline-flex items-center justify-center p-2 text-slate-500 hover:text-slate-700 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 shadow-sm transition-all duration-150">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Modifier la vente</h1>
                <p class="text-sm text-slate-500 mt-1">Mettre à jour les informations de la transaction commerciale.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <form method="POST" action="{{ route('ventes.update', $vente) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Section 1 : Informations Principales -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Client & Produit</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Associer la vente à un client et à un produit du catalogue.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Client -->
                    <div>
                        <x-input-label for="client_id" value="Client" class="text-slate-700 font-semibold" />
                        <select id="client_id" name="client_id" required class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                            <option value="">Sélectionnez un client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" 
                                        data-filiale="{{ $client->filiale_id }}"
                                        {{ old('client_id', $vente->client_id) == $client->id ? 'selected' : '' }}>
                                    {{ $client->prenom }} {{ $client->nom }} ({{ $client->entreprise ?? 'Particulier' }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('client_id')" class="mt-2 text-xs" />
                    </div>

                    <!-- Produit -->
                    <div>
                        <x-input-label for="produit_id" value="Produit" class="text-slate-700 font-semibold" />
                        <select id="produit_id" name="produit_id" required class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                            <option value="">Sélectionnez un produit</option>
                            @foreach($produits as $produit)
                                <option value="{{ $produit->id }}" 
                                        data-price="{{ $produit->prix }}"
                                        {{ old('produit_id', $vente->produit_id) == $produit->id ? 'selected' : '' }}>
                                    {{ $produit->nom }} ({{ number_format($produit->prix, 2, ',', ' ') }} xof)
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('produit_id')" class="mt-2 text-xs" />
                    </div>
                </div>
            </div>

            <!-- Section 2 : Tarifs & Quantités -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Tarification & Quantité</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Saisir le volume et les réductions éventuelles.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Quantité -->
                    <div>
                        <x-input-label for="quantite" value="Quantité" class="text-slate-700 font-semibold" />
                        <x-text-input id="quantite" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="number" min="1" name="quantite" :value="old('quantite', $vente->quantite)" required />
                        <x-input-error :messages="$errors->get('quantite')" class="mt-2 text-xs" />
                    </div>

                    <!-- Réduction -->
                    <div>
                        <x-input-label for="reduction" value="Réduction (xof)" class="text-slate-700 font-semibold" />
                        <x-text-input id="reduction" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="number" step="0.01" min="0" name="reduction" :value="old('reduction', $vente->reduction)" />
                        <x-input-error :messages="$errors->get('reduction')" class="mt-2 text-xs" />
                    </div>

                    <!-- Estimated Live Amount -->
                    <div class="md:col-span-2 bg-indigo-50/50 rounded-xl p-4 border border-indigo-100/50 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-indigo-700 block uppercase tracking-wider">Montant total estimé (HT)</span>
                            <span class="text-xs text-slate-500">Calculé en direct (Prix unitaire * Quantité - Réduction)</span>
                        </div>
                        <div id="estimated_amount" class="text-2xl font-extrabold text-indigo-850">
                            {{ number_format($vente->montant, 2, ',', ' ') }} xof
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3 : Attribution & Statut -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Attribution, Date & Statut</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Affectation géographique, auteur de la vente et statut de validation.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Filiale -->
                    <div>
                        <x-input-label for="filiale_id" value="Filiale concernée" class="text-slate-700 font-semibold" />
                        <select id="filiale_id" name="filiale_id" required class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                            <option value="">Sélectionnez une filiale</option>
                            @foreach($filiales as $filiale)
                                <option value="{{ $filiale->id }}" {{ old('filiale_id', $vente->filiale_id) == $filiale->id ? 'selected' : '' }}>{{ $filiale->nom }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('filiale_id')" class="mt-2 text-xs" />
                    </div>

                    <!-- Commercial -->
                    <div>
                        <x-input-label for="commercial_id" value="Commercial affecté" class="text-slate-700 font-semibold" />
                        <select id="commercial_id" name="commercial_id" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                            <option value="">Non assigné</option>
                            @foreach($commercials as $comm)
                                <option value="{{ $comm->id }}" {{ old('commercial_id', $vente->commercial_id) == $comm->id ? 'selected' : '' }}>{{ $comm->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('commercial_id')" class="mt-2 text-xs" />
                    </div>

                    <!-- Date de vente -->
                    <div>
                        <x-input-label for="date_vente" value="Date de vente" class="text-slate-700 font-semibold" />
                        <x-text-input id="date_vente" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" type="datetime-local" name="date_vente" :value="old('date_vente', $vente->date_vente ? $vente->date_vente->format('Y-m-d\TH:i') : '')" required />
                        <x-input-error :messages="$errors->get('date_vente')" class="mt-2 text-xs" />
                    </div>

                    <!-- Statut -->
                    <div>
                        <x-input-label for="statut" value="Statut de la transaction" class="text-slate-700 font-semibold" />
                        <select id="statut" name="statut" required class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                            <option value="En attente" {{ old('statut', $vente->statut) === 'En attente' ? 'selected' : '' }}>En attente</option>
                            <option value="Validée" {{ old('statut', $vente->statut) === 'Validée' ? 'selected' : '' }}>Validée</option>
                            <option value="Annulée" {{ old('statut', $vente->statut) === 'Annulée' ? 'selected' : '' }}>Annulée</option>
                        </select>
                        <x-input-error :messages="$errors->get('statut')" class="mt-2 text-xs" />
                    </div>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="flex items-center justify-end space-x-3 pt-2">
                <a href="{{ route('ventes.index') }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 active:bg-slate-100 rounded-xl border border-slate-200 shadow-sm transition-all duration-150">
                    Annuler
                </a>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl shadow-md shadow-indigo-600/10 hover:shadow-indigo-600/20 transition-all duration-200">
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>

    <!-- JavaScript Auto fill & calculator -->
    <script nonce="{{ Vite::cspNonce() }}">
        function calculateAmount() {
            const productSelect = document.getElementById('produit_id');
            const qtyInput = document.getElementById('quantite');
            const reductionInput = document.getElementById('reduction');
            const amountDisplay = document.getElementById('estimated_amount');
            
            if (productSelect && qtyInput && reductionInput && amountDisplay) {
                const selectedOption = productSelect.options[productSelect.selectedIndex];
                if (selectedOption && selectedOption.value) {
                    const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
                    const qty = parseInt(qtyInput.value) || 1;
                    const reduction = parseFloat(reductionInput.value) || 0;
                    const estAmount = (price * qty) - reduction;
                    amountDisplay.innerText = Math.max(0, estAmount).toLocaleString('fr-FR', { style: 'currency', currency: 'EUR' });
                } else {
                    amountDisplay.innerText = '0,00 xof';
                }
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const productSelect = document.getElementById('produit_id');
            const qtyInput = document.getElementById('quantite');
            const reductionInput = document.getElementById('reduction');
            const clientSelect = document.getElementById('client_id');
            
            if (productSelect) productSelect.addEventListener('change', calculateAmount);
            if (qtyInput) qtyInput.addEventListener('input', calculateAmount);
            if (reductionInput) reductionInput.addEventListener('input', calculateAmount);
            
            if (clientSelect) {
                clientSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption && selectedOption.value) {
                        const filialeId = selectedOption.getAttribute('data-filiale');
                        if (filialeId) {
                            document.getElementById('filiale_id').value = filialeId;
                        }
                    }
                });
            }
            
            calculateAmount();
        });
    </script>
</x-app-layout>
