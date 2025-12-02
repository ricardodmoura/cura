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
    public function showRegisterForm()
    {
        return view('auth.register');
    }

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

    public function showLoginForm()
    {
        return view('auth.login');
    }

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

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

}
