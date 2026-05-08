<?php

namespace App\Models;

use App\Enums\UserType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    public const CURRENT_CONSENT_VERSION = '2026-05-08';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'consented_at',
        'consent_version',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'consented_at' => 'datetime',
        'is_admin' => 'boolean',
        'password' => 'hashed',
    ];

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    /**
     * Um Utilizador tem um Perfil (Profile).
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Um Utilizador tem uma Ficha Médica (MedicalInfo).
     */
    public function medicalInfo(): HasOne
    {
        return $this->hasOne(MedicalInfo::class);
    }

    /**
     * Um Utilizador (Profissional) tem várias Qualificações.
     */
    public function qualifications(): HasMany
    {
        return $this->hasMany(Qualification::class);
    }

    /**
     * Um Utilizador (Paciente) tem vários Serviços agendados.
     */
    public function servicesAsPatient(): HasMany
    {
        // Define explicitamente a chave estrangeira 'patient_id'
        return $this->hasMany(Service::class, 'patient_id');
    }

    /**
     * Um Utilizador (Profissional) tem vários Serviços atribuídos.
     */
    public function servicesAsProfessional(): HasMany
    {
        // Define explicitamente a chave estrangeira 'professional_id'
        return $this->hasMany(Service::class, 'professional_id');
    }

    /**
     * Um Utilizador (Paciente ou Profissional) pode escrever várias Avaliações (Reviews).
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Um Utilizador tem várias Notificações.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Um Utilizador está associado a vários Logs.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(Log::class);
    }

    /**
     * Verifica se o utilizador é um profissional de saúde.
     */
    public function isProfessional(): bool
    {
        $professions = [
            UserType::DOCTOR->value,
            UserType::NURSE->value,
            UserType::MEDICAL_ASSISTANT->value,
        ];

        return $this->profile && in_array($this->profile->user_type, $professions);
    }

    /**
     * Verifica se o utilizador é um utente ou acompanhante.
     */
    public function isPatientOrCompanion(): bool
    {
        return $this->profile && in_array($this->profile->user_type, [
            UserType::PATIENT->value,
            UserType::COMPANION->value,
        ]);
    }

    /**
     * Média das avaliações recebidas (em ambas as direções: como utente e como profissional).
     * O avaliador é a OUTRA parte do serviço — nunca o próprio.
     */
    public function averageRatingReceived(): ?float
    {
        $avg = \DB::table('reviews')
            ->join('services', 'reviews.service_id', '=', 'services.id')
            ->where(function ($q) {
                // Avaliação que recebi como utente: prof avalia o serviço onde sou paciente.
                $q->where(function ($qq) {
                    $qq->where('services.patient_id', $this->id)
                       ->whereColumn('reviews.user_id', 'services.professional_id');
                })
                // Avaliação que recebi como profissional: utente avalia o serviço onde sou prof.
                ->orWhere(function ($qq) {
                    $qq->where('services.professional_id', $this->id)
                       ->whereColumn('reviews.user_id', 'services.patient_id');
                });
            })
            ->whereNotNull('reviews.rating')
            ->avg('reviews.rating');

        return $avg !== null ? round((float) $avg, 1) : null;
    }
}