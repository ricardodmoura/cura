@extends('app.layout.app')

@section('title', 'Perfil de ' . $user->name)

@section('content')

@php
    // Mapa de tradução para os tipos de utilizador
    $roles = [
        'patient' => 'Utente',
        'companion' => 'Acompanhante',
        'medical_assistant' => 'Auxiliar',
        'nurse' => 'Enfermeiro(a)',
        'doctor' => 'Médico(a)',
    ];
    
    $userType = optional($user->profile)->user_type;
    $roleName = $userType && isset($roles[$userType]) ? $roles[$userType] : ucfirst($userType ?? 'N/D');
@endphp

<div class="max-w-7xl mx-auto">

    <!-- Header & Botão Editar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-teal-900 tracking-tight">Perfil</h1>
            <p class="text-teal-600 mt-1">Visualize e altere as suas informações pessoais e médicas.</p>
        </div>
        
        <a href="{{ route('app.user.edit', $user) }}" 
           class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-xl shadow-md transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
            </svg>
            Editar
        </a>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        
        <!-- COLUNA DA ESQUERDA: Cartão de Perfil -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 text-center sticky top-24">
                <!-- Foto de Perfil -->
                <div class="relative w-32 h-32 mx-auto mb-4">
                    <div class="w-full h-full rounded-full overflow-hidden border-4 border-teal-50 shadow-inner">
                        @if($user->profile && $user->profile->profile_photo)
                            <img src="{{ asset('storage/' . $user->profile->profile_photo) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-teal-100 flex items-center justify-center text-teal-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Nome e Cargo -->
                <h2 class="text-xl font-bold text-teal-900 mb-1">{{ $user->name }}</h2>
                <p class="text-teal-500 font-medium mb-6">{{ $roleName }}</p>

                <!-- Link Alterar Foto -->
                <a href="{{ route('app.user.edit', $user) }}" class="text-xs text-teal-400 hover:text-teal-600 underline transition-colors">
                    Alterar foto de perfil
                </a>
            </div>
        </div>

        <!-- COLUNA DA DIREITA: Informações Detalhadas -->
        <div class="lg:col-span-2 space-y-6">

            <!-- 1. Informações Pessoais -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8">
                <h3 class="text-lg font-bold text-teal-900 mb-6">Informações Pessoais</h3>
                
                <div class="space-y-5">
                    <div>
                        <p class="text-sm font-bold text-teal-800 mb-0.5">Nome</p>
                        <p class="text-teal-600">{{ $user->name }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-bold text-teal-800 mb-0.5">Email</p>
                        <p class="text-teal-600">{{ $user->email }}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <p class="text-sm font-bold text-teal-800 mb-0.5">Telemóvel</p>
                            <p class="text-teal-600">{{ $user->profile->phone ?? 'N/D' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-teal-800 mb-0.5">Data de Nascimento</p>
                            <p class="text-teal-600">
                                {{ optional($user->profile)->birth_date ? \Carbon\Carbon::parse($user->profile->birth_date)->format('d/m/Y') : 'N/D' }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-bold text-teal-800 mb-0.5">Morada</p>
                        <p class="text-teal-600">{{ $user->profile->address ?? 'N/D' }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-bold text-teal-800 mb-0.5">NIF</p>
                        <p class="text-teal-600">{{ $user->profile->tax_id ?? 'N/D' }}</p>
                    </div>
                </div>
            </div>

            <!-- 2. Informações Médicas (Apenas para Utentes) -->
            {{-- Usamos o optional para não dar erro se profile for null, e verificamos se existe medicalInfo --}}
            @if(optional($user->profile)->user_type === 'patient' && $user->medicalInfo)
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8">
                <h3 class="text-lg font-bold text-teal-900 mb-6">Informações Médicas</h3>

                <div class="space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <p class="text-sm font-bold text-teal-800 mb-0.5">Tipo Sanguíneo</p>
                            <p class="text-teal-600 font-medium">{{ $user->medicalInfo->blood_type ?? 'N/D' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-teal-800 mb-0.5">Contacto Emergência</p>
                            <p class="text-teal-600">{{ $user->medicalInfo->emergency_contact ?? 'N/D' }}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-bold text-teal-800 mb-0.5">Alergias</p>
                        <p class="text-teal-600">{{ $user->medicalInfo->allergies ?? 'Nenhuma registada' }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-bold text-teal-800 mb-0.5">Condições Médicas</p>
                        <p class="text-teal-600">{{ $user->medicalInfo->medical_conditions ?? 'Nenhuma registada' }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-bold text-teal-800 mb-0.5">Medicação Atual</p>
                        <p class="text-teal-600">{{ $user->medicalInfo->current_medications ?? 'Nenhuma registada' }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- 3. Informações da Conta -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8">
                <h3 class="text-lg font-bold text-teal-900 mb-6">Informações Conta</h3>

                <div class="space-y-5">
                    <div>
                        <p class="text-sm font-bold text-teal-800 mb-0.5">Tipo de Utilizador</p>
                        <p class="text-teal-600">{{ $roleName }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-bold text-teal-800 mb-0.5">Membro Desde</p>
                        <p class="text-teal-600">
                            {{ $user->created_at->format('d/m/Y') }}
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection