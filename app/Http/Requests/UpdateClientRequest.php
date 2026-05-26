<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
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
            'prospect_id' => ['nullable', 'exists:prospects,id'],
            'commercial_id' => ['nullable', 'exists:users,id'],
            'filiale_id' => ['required', 'exists:filiales,id'],
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'adresse' => ['nullable', 'string'],
            'ville' => ['nullable', 'string', 'max:255'],
            'entreprise' => ['nullable', 'string', 'max:255'],
            'statut' => ['required', 'string', 'in:Actif,Inactif'],
            'date_conversion' => ['nullable', 'date'],
        ];
    }
}
