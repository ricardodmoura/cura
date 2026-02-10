<?php

namespace App\Models;

use App\Enums\UserType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
        'password' => 'hashed',
    ];

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
}