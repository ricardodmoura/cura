<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Log extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'details',
    ];

    /**
     * Um Log pertence a um Utilizador (User).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Regista uma ação de auditoria.
     *
     * Why: GDPR Art. 30/32 — registo do tratamento e log de acessos a dados de
     * categoria especial. Mantém o callsite simples (sem ter de passar o user).
     */
    public static function record(string $action, ?string $details = null, ?int $userId = null): void
    {
        static::create([
            'user_id' => $userId ?? Auth::id(),
            'action' => $action,
            'details' => $details,
        ]);
    }
}