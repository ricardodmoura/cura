<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Enums\UserType;

class AuthController extends Controller
{
    /**
     * Exibe o formulário de registo de novos utilizadores.
     *
     * @return \Illuminate\View\View
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Processa o registo de um novo utilizador no sistema.
     *
     * Este método utiliza uma transação de base de dados (DB::transaction)
     * para garantir a integridade dos dados. Cria sequencialmente:
     * 1. O registo na tabela 'users'.
     * 2. O perfil na tabela 'profiles' (com foto e tipo de utilizador).
     * 3. A informação médica (apenas se for Utente).
     * 4. As qualificações (apenas se for Profissional de Saúde).
     *
     * Se algum passo falhar, todas as alterações são revertidas (rollback).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception Se ocorrer erro na transação.
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            // User
            'user.name' => 'required|string|max:255',
            'user.email' => 'required|string|email|max:255|unique:users,email',
            'user.password' => 'required|string|min:8|confirmed',

            // Profile
            'profile.phone' => 'nullable|string|max:20',
            'profile.profile_picture' => 'nullable|image|max:2048',
            'profile.user_type' => 'required|in:patient,companion,medical_assistant,nurse,doctor',
            'profile.birth_date' => 'nullable|date',
            'profile.address' => 'nullable|string|max:255',
            'profile.tax_id' => 'nullable|string|max:50',
            'profile.social_security_number' => 'nullable|string|max:50',

            // Medical Info
            'medical_info.blood_type' => 'nullable|string|max:3',
            'medical_info.allergies' => 'nullable|string|max:500',
            'medical_info.medical_conditions' => 'nullable|string|max:500',
            'medical_info.current_medications' => 'nullable|string|max:500',
            'medical_info.emergency_contact' => 'nullable|string|max:255',

            // Qualifications
            'qualifications.description' => 'nullable|string|max:1000',
            'qualifications.document' => 'nullable|file|max:5120',
        ]);

        $user = DB::transaction(function () use ($data) {
            
            $user = User::create([
                'name' => $data['user']['name'],
                'email' => $data['user']['email'],
                'password' => Hash::make($data['user']['password']),
            ]);

            $profilePhotoPath = null;
            if (isset($data['profile']['profile_picture'])) {
                $profilePhotoPath = $data['profile']['profile_picture']->store('profiles', 'public');
            }
            $user->profile()->create([
                'phone' => $data['profile']['phone'] ?? null, 
                'profile_photo' => $profilePhotoPath,
                'user_type' => $data['profile']['user_type'],
                'birth_date' => $data['profile']['birth_date'] ?? null,
                'address' => $data['profile']['address'] ?? null,
                'tax_id' => $data['profile']['tax_id'] ?? null,
                'social_security_number' => $data['profile']['social_security_number'] ?? null,
            ]);


            $userType = UserType::tryFrom($data['profile']['user_type']);
            if ($userType === UserType::PATIENT) {
                $user->medicalInfo()->create([
                    'blood_type' => $data['medical_info']['blood_type'] ?? null,
                    'allergies' => $data['medical_info']['allergies'] ?? null,
                    'medical_conditions' => $data['medical_info']['medical_conditions'] ?? null,
                    'current_medications' => $data['medical_info']['current_medications'] ?? null,
                    'emergency_contact' => $data['medical_info']['emergency_contact'] ?? null,
                ]);
            }

            $professionals = [UserType::MEDICAL_ASSISTANT, UserType::NURSE, UserType::DOCTOR];
            
            if (in_array($userType, $professionals)) {
                $docPath = null;
                if (isset($data['qualifications']['document'])) {
                    $docPath = $data['qualifications']['document']->store('qualifications', 'public');
                }

                if (!empty($data['qualifications']['description']) || $docPath) {
                    $user->qualifications()->create([
                        'description' => $data['qualifications']['description'] ?? null,
                        'document' => $docPath,
                    ]);
                }
            }

            return $user;
        });

        Auth::login($user);

        return redirect()->route('app.index');
    }

    /**
     * Exibe o formulário de login.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Processa a tentativa de autenticação do utilizador.
     *
     * Valida as credenciais (email e password). Se forem válidas,
     * regenera a sessão para prevenir ataques de fixação e redireciona
     * para a dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->route('app.index');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Termina a sessão do utilizador (Logout).
     *
     * Invalida a sessão atual e regenera o token CSRF por segurança.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

}
