<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Autorizado para todos os logados (o controller verifica a propriedade)
    }

    public function rules(): array
    {
        return [
            'service_type' => 'required|string',
            'date'         => 'required|date|after:today', // Removemos 'after_or_equal:today' para permitir editar agendamentos de hoje sem erro
            'time'         => 'required',
            'location'     => 'required|string|min:5',
            'notes'        => 'nullable|string',
            'status'       => 'required|in:pending,confirmed,completed,canceled', // Validação do novo campo
        ];
    }
}