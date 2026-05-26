<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'prospect_id' => 'nullable|exists:prospects,id',
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priorite' => 'required|string|in:Faible,Moyenne,Haute,Urgente',
            'date_limite' => 'nullable|date',
            'statut' => 'required|string|in:À faire,En cours,Terminé',
        ];
    }
}
