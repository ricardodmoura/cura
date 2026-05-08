<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone',
        'profile_photo',
        'user_type',
        'birth_date',
        'address',
        'tax_id',
        'social_security_number',
        'notification_preferences',
    ];

    /**
     * Define os casts para atributos específicos.
     */
    protected $casts = [
        'birth_date' => 'date',
        // NIF e NISS são identificadores fiscais sensíveis — encriptados em coluna.
        'tax_id' => 'encrypted',
        'social_security_number' => 'encrypted',
        'notification_preferences' => 'array',
    ];

    /**
     * Defaults para as preferências de notificação.
     * O utilizador recebe tudo até desativar explicitamente.
     */
    public const NOTIFICATION_DEFAULTS = [
        'service_updates' => true,   // serviço aceite, reagendado, cancelado, concluído
        'review_received' => true,   // alguém deixou avaliação
        'marketing' => false,        // novidades / promoções (opt-in)
    ];

    /**
     * Verifica se o utilizador quer receber notificações de um determinado tipo.
     * Defaults aplicam-se quando o tipo nunca foi configurado.
     */
    public function wantsNotification(string $type): bool
    {
        $prefs = $this->notification_preferences ?? [];
        return $prefs[$type] ?? (self::NOTIFICATION_DEFAULTS[$type] ?? true);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}