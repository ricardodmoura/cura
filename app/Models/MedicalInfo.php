<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'blood_type',
        'allergies',
        'medical_conditions',
        'current_medications',
        'emergency_contact',
    ];

    // GDPR Art. 9: dados de saúde são categoria especial, encriptados em coluna.
    protected $casts = [
        'blood_type' => 'encrypted',
        'allergies' => 'encrypted',
        'medical_conditions' => 'encrypted',
        'current_medications' => 'encrypted',
        'emergency_contact' => 'encrypted',
    ];

    /**
     * A informação médica pertence a um Utilizador (User).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}