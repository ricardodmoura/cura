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
            'date'         => 'required|date|after:today',
            'time'         => 'required',
            'location'     => 'required|string|min:5',
            'notes'        => 'nullable|string',
            // status NÃO é editável pelo paciente — ver ServiceController::accept/markCompleted/destroy.
        ];
    }
}
