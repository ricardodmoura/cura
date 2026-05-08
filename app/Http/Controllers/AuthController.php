<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Qualification;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
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
            'qualifications.cedula_number' => 'nullable|string|max:64',
            'qualifications.document' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png',

            // RGPD: consentimento explícito (Art. 7).
            'consent' => 'accepted',
        ]);

        // Reforço: profissionais TÊM de fornecer cédula + documento.
        $isProfessional = in_array($data['profile']['user_type'], ['medical_assistant', 'nurse', 'doctor']);
        if ($isProfessional && (empty($data['qualifications']['cedula_number']) || empty($data['qualifications']['document']))) {
            return back()->withErrors([
                'qualifications.cedula_number' => 'Cédula e documento são obrigatórios para profissionais.',
            ])->withInput();
        }

        $user = DB::transaction(function () use ($data) {
            
            $user = User::create([
                'name' => $data['user']['name'],
                'email' => $data['user']['email'],
                'password' => Hash::make($data['user']['password']),
                'consented_at' => now(),
                'consent_version' => User::CURRENT_CONSENT_VERSION,
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
                    // Disco "local" (privado) — documentos só servidos por endpoint autenticado.
                    $docPath = $data['qualifications']['document']->store('qualifications', 'local');
                }

                if (!empty($data['qualifications']['description']) || $docPath) {
                    $user->qualifications()->create([
                        'description' => $data['qualifications']['description'] ?? null,
                        'cedula_number' => $data['qualifications']['cedula_number'] ?? null,
                        'document' => $docPath,
                        'verification_status' => Qualification::STATUS_PENDING,
                    ]);
                }
            }

            return $user;
        });

        Auth::login($user);

        // Dispara o email de verificação (Laravel automatiza via MustVerifyEmail).
        $user->sendEmailVerificationNotification();

        Log::record('auth.register', "User #{$user->id} registered as {$data['profile']['user_type']}", $user->id);

        return redirect()->route('verification.notice');
    }

    /**
     * Página informativa para utilizadores ainda não verificados.
     */
    public function showVerifyNotice()
    {
        if (Auth::user()?->hasVerifiedEmail()) {
            return redirect()->route('app.index');
        }
        return view('auth.verify-email');
    }

    /**
     * Confirma o email a partir do link enviado.
     */
    public function verifyEmail(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('app.index')->with('status', 'Email já estava verificado.');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
            Log::record('auth.email.verified', null, $request->user()->id);
        }

        return redirect()->route('app.index')->with('status', 'Email verificado com sucesso.');
    }

    /**
     * Reenvia o email de verificação.
     */
    public function resendVerification(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('app.index');
        }
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'Email de verificação reenviado.');
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

            Log::record('auth.login', null, Auth::id());

            return redirect()->route('app.index');
        }

        Log::record('auth.login.failed', "Email: {$credentials['email']}", null);

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
        $userId = Auth::id();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::record('auth.logout', null, $userId);

        return redirect()->route('login');
    }

    /**
     * Recuperação de palavra-passe — mostra o formulário de pedido.
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Envia o link de reset por email. A resposta é igual independentemente
     * de o email existir, para evitar enumeração de utilizadores.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        Log::record('auth.reset.request', "Reset attempt for {$request->email} ({$status})");

        return back()->with('status', __('Se o email existir, receberá um link para redefinir a palavra-passe.'));
    }

    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            $resetUser = User::where('email', $request->email)->first();
            Log::record('auth.reset.complete', "Password reset for user #{$resetUser?->id}", $resetUser?->id);
            return redirect()->route('login')->with('status', __('Palavra-passe redefinida com sucesso. Pode iniciar sessão.'));
        }

        return back()->withErrors(['email' => __($status)])->onlyInput('email');
    }
}
