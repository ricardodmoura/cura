@extends('app.layout.app')

@section('title', 'Editar Perfil')

@section('content')

<div class="max-w-3xl mx-auto">

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-teal-900 mb-2">Editar Perfil</h1>
        <p class="text-teal-600">Atualize as suas informações pessoais e médicas.</p>
    </div>

    {{-- Exibe erros de validação se existirem --}}
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r">
            <p class="font-bold">Por favor corrija os seguintes erros:</p>
            <ul class="list-disc list-inside text-sm mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('app.user.update', $user) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-md p-8 space-y-8">
        @csrf
        @method('PUT')

        <!-- Dados da Conta -->
        <section>
            <h2 class="text-xl font-semibold text-teal-900 mb-6 border-b border-teal-500 pb-2">
                Dados da Conta
            </h2>

            <div class="space-y-6">

                <!-- Foto de Perfil -->
                <div>
                    <label class="block text-sm font-medium text-teal-900 mb-2">Foto de Perfil</label>
                    <div class="flex items-center gap-4">
                        <!-- Adicionado flex-shrink-0 para impedir que a imagem encolha em mobile -->
                        <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-teal-100 bg-teal-600 shrink-0">
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
                        <input type="file" name="profile[profile_photo]" class="text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                    </div>
                </div>


                <!-- Nome -->
                <div>
                    <label class="block text-sm font-medium text-teal-900 mb-2">
                        Nome Completo <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="user[name]"
                           value="{{ old('user.name', $user->name) }}"
                           required
                           class="w-full px-4 py-3 border border-teal-500 rounded-xl focus:ring-2 focus:ring-teal-500 focus:outline-none">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-teal-900 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email"
                           name="user[email]"
                           value="{{ old('user.email', $user->email) }}"
                           required
                           class="w-full px-4 py-3 border border-teal-500 rounded-xl focus:ring-2 focus:ring-teal-500 focus:outline-none">
                </div>

                <!-- Telefone -->
                <div>
                    <label class="block text-sm font-medium text-teal-900 mb-2">
                        Telefone
                    </label>
                    <input type="tel"
                           name="profile[phone]"
                           value="{{ old('profile.phone', $user->profile->phone ?? '') }}"
                           class="w-full px-4 py-3 border border-teal-500 rounded-xl focus:ring-2 focus:ring-teal-500 focus:outline-none">
                </div>

                <!-- Tipo de Utilizador (Normalmente desabilitado para edição, mas funcional se necessário) -->
                <div>
                    <label class="block text-sm font-medium text-teal-900 mb-2">
                        Tipo de Utilizador
                    </label>
                    <!-- Usamos um input hidden para enviar o valor se o select estiver disabled -->
                    <input type="hidden" name="profile[user_type]" value="{{ $user->profile->user_type ?? 'patient' }}">
                    <select disabled 
                            class="w-full px-4 py-3 border border-teal-500 rounded-xl focus:ring-2 focus:ring-teal-500 bg-gray-50 text-gray-500 cursor-not-allowed">
                        @foreach(['patient' => 'Utente', 'companion' => 'Acompanhante', 'medical_assistant' => 'Assistente Médico', 'nurse' => 'Enfermeiro', 'doctor' => 'Médico'] as $value => $label)
                            <option value="{{ $value }}" {{ (old('profile.user_type', $user->profile->user_type ?? '') == $value) ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Para alterar o tipo de utilizador, contacte o suporte.</p>
                </div>

            </div>
        </section>

        <!-- Dados Pessoais -->
        <section>
            <h2 class="text-xl font-semibold text-teal-900 mb-6 border-b border-teal-500 pb-2">
                Dados Pessoais
            </h2>

            <div class="grid md:grid-cols-2 gap-6">

                <!-- Data Nascimento -->
                <div>
                    <label class="block text-sm font-medium text-teal-900 mb-2">
                        Data de Nascimento
                    </label>
                    <input type="date"
                           name="profile[birth_date]"
                           value="{{ old('profile.birth_date', optional($user->profile->birth_date)->format('Y-m-d')) }}"
                           class="w-full px-4 py-3 border border-teal-500 rounded-xl focus:ring-2 focus:ring-teal-500 focus:outline-none">
                </div>

                <!-- NIF -->
                <div>
                    <label class="block text-sm font-medium text-teal-900 mb-2">
                        NIF
                    </label>
                    <input type="text"
                           name="profile[tax_id]"
                           value="{{ old('profile.tax_id', $user->profile->tax_id ?? '') }}"
                           maxlength="9"
                           class="w-full px-4 py-3 border border-teal-500 rounded-xl focus:ring-2 focus:ring-teal-500 focus:outline-none">
                </div>

                <!-- NISS (Adicionado para consistência) -->
                <div>
                    <label class="block text-sm font-medium text-teal-900 mb-2">
                        NISS (Seg. Social)
                    </label>
                    <input type="text"
                           name="profile[social_security_number]"
                           value="{{ old('profile.social_security_number', $user->profile->social_security_number ?? '') }}"
                           class="w-full px-4 py-3 border border-teal-500 rounded-xl focus:ring-2 focus:ring-teal-500 focus:outline-none">
                </div>

            </div>

            <!-- Morada -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-teal-900 mb-2">Morada</label>
                <textarea name="profile[address]"
                          rows="3"
                          class="w-full px-4 py-3 border border-teal-500 rounded-xl focus:ring-2 focus:ring-teal-500 resize-none focus:outline-none">{{ old('profile.address', $user->profile->address ?? '') }}</textarea>
            </div>

        </section>

        <!-- Informações Médicas (Apenas para Utentes) -->
        @if(optional($user->profile)->user_type === 'patient')
        <section>
            <h2 class="text-xl font-semibold text-teal-900 mb-6 border-b border-teal-500 pb-2">
                Informações Médicas
            </h2>

            <div class="grid md:grid-cols-2 gap-6">

                <!-- Tipo Sanguíneo -->
                <div>
                    <label class="block text-sm font-medium text-teal-900 mb-2">
                        Tipo Sanguíneo
                    </label>
                    <select name="medical_info[blood_type]" class="w-full px-4 py-3 border border-teal-500 rounded-xl focus:ring-2 focus:ring-teal-500 focus:outline-none bg-white">
                        <option value="">Selecione...</option>
                        @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $type)
                            <option value="{{ $type }}" {{ (old('medical_info.blood_type', $user->medicalInfo->blood_type ?? '') == $type) ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Contacto Emergência -->
                <div>
                    <label class="block text-sm font-medium text-teal-900 mb-2">
                        Contacto de Emergência
                    </label>
                    <input type="tel"
                           name="medical_info[emergency_contact]"
                           value="{{ old('medical_info.emergency_contact', $user->medicalInfo->emergency_contact ?? '') }}"
                           placeholder="+351..."
                           class="w-full px-4 py-3 border border-teal-500 rounded-xl focus:ring-2 focus:ring-teal-500 focus:outline-none">
                </div>

            </div>

            <div class="mt-6 space-y-4">

                <!-- Alergias -->
                <div>
                    <label class="block text-sm font-medium text-teal-900 mb-2">
                        Alergias
                    </label>
                    <textarea name="medical_info[allergies]"
                              rows="2"
                              class="w-full px-4 py-3 border border-teal-500 rounded-xl focus:ring-2 focus:ring-teal-500 resize-none focus:outline-none">{{ old('medical_info.allergies', $user->medicalInfo->allergies ?? '') }}</textarea>
                </div>

                <!-- Condições Médicas -->
                <div>
                    <label class="block text-sm font-medium text-teal-900 mb-2">
                        Condições Médicas
                    </label>
                    <textarea name="medical_info[medical_conditions]"
                              rows="2"
                              class="w-full px-4 py-3 border border-teal-500 rounded-xl focus:ring-2 focus:ring-teal-500 resize-none focus:outline-none">{{ old('medical_info.medical_conditions', $user->medicalInfo->medical_conditions ?? '') }}</textarea>
                </div>

                <!-- Medicação -->
                <div>
                    <label class="block text-sm font-medium text-teal-900 mb-2">
                        Medicação Atual
                    </label>
                    <textarea name="medical_info[current_medications]"
                              rows="2"
                              class="w-full px-4 py-3 border border-teal-500 rounded-xl focus:ring-2 focus:ring-teal-500 resize-none focus:outline-none">{{ old('medical_info.current_medications', $user->medicalInfo->current_medications ?? '') }}</textarea>
                </div>

            </div>
        </section>
        @endif

        <!-- Palavra-passe -->
        <section>
            <h2 class="text-xl font-semibold text-teal-900 mb-6 border-b border-teal-500 pb-2">
                Alterar Palavra-passe
            </h2>
            <p class="text-sm text-teal-600 mb-4">
                Deixe os campos em branco se não pretender alterar a palavra-passe.
            </p>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-teal-900 mb-2">
                        Nova Palavra-passe
                    </label>
                    <input type="password"
                           name="user[password]"
                           placeholder="••••••••"
                           class="w-full px-4 py-3 border border-teal-500 rounded-xl focus:ring-2 focus:ring-teal-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-teal-900 mb-2">
                        Confirmar
                    </label>
                    <input type="password"
                           name="user[password_confirmation]"
                           placeholder="••••••••"
                           class="w-full px-4 py-3 border border-teal-500 rounded-xl focus:ring-2 focus:ring-teal-500 focus:outline-none">
                </div>
            </div>
        </section>

        <!-- Botões -->
        <div class="flex flex-col sm:flex-row gap-4 pt-4">
            <button type="submit"
                    class="flex-1 bg-teal-600 hover:bg-teal-700 text-white font-semibold py-3 rounded-xl transition-colors shadow-md">
                Guardar Alterações
            </button>

            <a href="{{ route('app.user.show', $user) }}"
                    class="flex-1 bg-teal-50 hover:bg-teal-100 text-teal-700 font-semibold py-3 rounded-xl transition-colors text-center border border-teal-500">
                Cancelar
            </a>
        </div>

    </form>

</div>

@endsection