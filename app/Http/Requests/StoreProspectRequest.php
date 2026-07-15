<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProspectRequest extends FormRequest
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
            'commercial_id' => ['nullable', 'exists:users,id'],
            'source_id' => ['nullable', 'exists:sources,id'],
            'campagne_id' => ['nullable', 'exists:campagnes,id'],
            'publication_id' => ['nullable', 'exists:publications,id'],
            'filiale_id' => ['required', 'exists:filiales,id'],
            'nom' => ['nullable', 'string', 'max:255'],
            'prenom' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'telephone' => ['required', 'string', 'max:50'],
            'entreprise' => ['nullable', 'string', 'max:255'],
            'profession' => ['nullable', 'string', 'max:255'],
            'adresse' => ['nullable', 'string'],
            'ville' => ['nullable', 'string', 'max:255'],
            'statut' => ['required', 'string', 'in:Nouveau,Contacté,Qualifié,En négociation,Gagné,Perdu'],
            'besoin' => ['nullable', 'string'],
            'montant_estime' => ['nullable', 'numeric', 'min:0'],
            'probabilite' => ['nullable', 'integer', 'min:0', 'max:100'],
            'commentaire' => ['nullable', 'string'],
            'date_contact' => ['nullable', 'date'],
            'prochain_rappel' => ['nullable', 'date'],
            'tags' => ['nullable', 'string'],
        ];
    }
}
