<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            //User
            'user.name' => 'required|string|max:255',
            'user.email' => 'required|string|email|max:255',
            'user.password' => 'nullable|string|min:8|confirmed',

            //Profile
            'profile.phone' => 'nullable|string|max:20',
            'profile.profile_picture' => 'nullable|image|max:2048',
            'profile.user_type' => 'required|in:patient,companion,medical_assistant,nurse,doctor',
            'profile.birth_date' => 'nullable|date',
            'profile.address' => 'nullable|string|max:255',
            'profile.tax_id' => 'nullable|string|max:50',
            'profile.social_security_number' => 'nullable|string|max:50',

            //Medical Info
            'medical_info.blood_type' => 'nullable|string|max:3',
            'medical_info.allergies' => 'nullable|string|max:500',
            'medical_info.medical_conditions' => 'nullable|string|max:500',
            'medical_info.current_medications' => 'nullable|string|max:500',
            'medical_info.emergency_contact' => 'nullable|string|max:255',

            //Qualifications
            'qualifications.description' => 'nullable|string|max:1000',
            'qualifications.document' => 'nullable|file|max:5120',
        ];
    }
}
