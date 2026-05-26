<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRelanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'prospect_id' => 'required|exists:prospects,id',
            'commercial_id' => 'required|exists:users,id',
            'date_relance' => 'required|date',
            'heure_relance' => 'nullable',
            'canal' => 'nullable|string|in:Appel,WhatsApp,Email,SMS,Rendez-vous',
            'commentaire' => 'nullable|string',
            'statut' => 'required|string|in:En attente,Réalisée,Annulée',
        ];
    }
}
