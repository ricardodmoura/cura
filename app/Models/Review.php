<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'user_id',
        'rating',
        'comment',
    ];

    public function service(): BelongsTo {
        return $this->belongsTo(Service::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    /**
     * Retorna o utilizador que está a ser avaliado (a outra parte do serviço).
     * Requer que `service.patient` e `service.professional` estejam carregados.
     */
    public function getRateeAttribute(): ?User
    {
        if (!$this->service) {
            return null;
        }
        return $this->user_id === $this->service->patient_id
            ? $this->service->professional
            : $this->service->patient;
    }
}