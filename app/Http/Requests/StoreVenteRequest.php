<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreVenteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'client_id' => ['required', 'exists:clients,id'],
            'produit_id' => ['required', 'exists:produits,id'],
            'commercial_id' => ['nullable', 'exists:users,id'],
            'filiale_id' => ['required', 'exists:filiales,id'],
            'quantite' => ['required', 'integer', 'min:1'],
            'reduction' => ['nullable', 'numeric', 'min:0'],
            'statut' => ['required', 'string', 'in:En attente,Validée,Annulée'],
            'date_vente' => ['required', 'date'],
            'montant' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
