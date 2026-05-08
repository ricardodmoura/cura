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

    /**
     * Profissionais que dispensaram este serviço (não querem ver no pool).
     */
    public function dismissals(): HasMany
    {
        return $this->hasMany(ServiceDismissal::class);
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
        if (!$user) {
            return false;
        }

        if (in_array(strtolower($this->status), ['completed', 'canceled'])) {
            return false;
        }

        // Utente pode cancelar livremente enquanto nenhum profissional aceitou.
        if ($user->isPatientOrCompanion() && $this->professional_id === null) {
            return $user->id === $this->patient_id;
        }

        // Caso contrário, regra de antecedência: 72h prof, 48h utente.
        $hoursAhead = now()->diffInHours($this->dateTime, false);

        if ($user->isProfessional() && $user->id === $this->professional_id) {
            return $hoursAhead >= 72;
        }
        if ($user->isPatientOrCompanion() && $user->id === $this->patient_id) {
            return $hoursAhead >= 48;
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
        if (!$user || !$user->isPatientOrCompanion() || $user->id !== $this->patient_id) {
            return false;
        }
        if (in_array(strtolower($this->status), ['completed', 'canceled'])) {
            return false;
        }
        return now()->diffInHours($this->dateTime, false) >= 48;
    }
}