<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
// --- IMPORTS NECESSÁRIOS ---
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'professional_id',
        'service_type',
        'report',
        'date',
        'time',
        'location',
        'price',
        'status',
    ];

    /**
     * Define os casts para atributos específicos.
     */
    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i', // Cast para Carbon, formatado
        'price' => 'decimal:2',
    ];

    /**
     * Um Serviço pertence a um Paciente (User).
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Um Serviço pertence a um Profissional (User).
     */
    public function professional(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professional_id');
    }

    /**
     * Um Serviço pode ter várias Avaliações (Reviews).
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // =========================================================
    // MÉTODOS DE LÓGICA DE NEGÓCIO 
    // =========================================================

    /**
     * Acessor para obter a Data e Hora completas num único objeto Carbon.
     */
    public function getDateTimeAttribute()
    {
        // Junta a data (Y-m-d) com a hora (H:i)
        return Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->time->format('H:i'));
    }

    /**
     * Verifica se o serviço pode ser cancelado pelo utilizador atual.
     */
    public function canBeCancelled()
    {
        $user = Auth::user();
        
        // Se o serviço já foi concluído ou cancelado, não se pode mexer
        if (in_array(strtolower($this->status), ['completed', 'canceled'])) {
            return false;
        }

        $serviceDate = $this->dateTime; // Usa o acessor que criámos acima
        $now = now();

        // Regra para Profissionais: 72h de antecedência
        if ($user->isProfessional()) {
            return $serviceDate->diffInHours($now, false) < -72;
        }

        // Regra para Utentes: 48h de antecedência
        if ($user->isPatientOrCompanion()) {
            return $serviceDate->diffInHours($now, false) < -48;
        }

        return false;
    }

    /**
     * Verifica se o serviço pode ser reagendado.
     * Regra: Apenas utentes podem reagendar, e com mais de 48h.
     */
    public function canBeRescheduled()
    {
        $user = Auth::user();
        
        // Se já passou ou foi cancelado, nada feito
        if (in_array(strtolower($this->status), ['completed', 'canceled'])) {
            return false;
        }

        // Apenas utentes podem reagendar
        if (!$user->isPatientOrCompanion()) {
            return false;
        }

        $serviceDate = $this->dateTime;
        $now = now();

        // Regra: Mais de 48h de antecedência
        return $serviceDate->diffInHours($now, false) < -48;
    }
}