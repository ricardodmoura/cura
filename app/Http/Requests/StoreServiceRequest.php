<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'service_type' => 'required|string', // A chave do array (enfermagem, medica...)
            'date'         => 'required|date|after_or_equal:today',
            'time'         => 'required',
            'location'     => 'required|string|min:5',
            'notes'        => 'nullable|string' // Isto vai para o campo 'report'
        ];
    }
}
