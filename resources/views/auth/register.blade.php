<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registo - Saúde Domiciliar</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-teal-400 to-teal-900 min-h-screen py-12 px-4">
    <div class="max-w-4xl mx-auto">

        <div class="bg-white rounded-2xl shadow-md p-8">
            <div class="text-center mb-8">
                <a href="/" class="inline-flex items-center justify-center w-14 h-14 bg-teal-600 rounded-xl mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-teal-900 mb-2">Criar Conta</h1>
                <p class="text-teal-600">Preencha todos os dados para se juntar à plataforma</p>
            </div>

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

            <form action="{{ route('register.post') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-teal-900 mb-3">Eu sou:</label>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                        <button type="button" class="user-type-btn py-3 px-2 text-sm md:text-base rounded-xl font-medium transition-all bg-teal-600 text-white shadow-md" data-type="patient">Utente</button>
                        <button type="button" class="user-type-btn py-3 px-2 text-sm md:text-base rounded-xl font-medium transition-all bg-teal-50 text-teal-700 hover:bg-teal-100" data-type="companion">Acompanhante</button>
                        <button type="button" class="user-type-btn py-3 px-2 text-sm md:text-base rounded-xl font-medium transition-all bg-teal-50 text-teal-700 hover:bg-teal-100" data-type="medical_assistant">Auxiliar</button>
                        <button type="button" class="user-type-btn py-3 px-2 text-sm md:text-base rounded-xl font-medium transition-all bg-teal-50 text-teal-700 hover:bg-teal-100" data-type="nurse">Enfermeiro</button>
                        <button type="button" class="user-type-btn py-3 px-2 text-sm md:text-base rounded-xl font-medium transition-all bg-teal-50 text-teal-700 hover:bg-teal-100" data-type="doctor">Médico</button>
                    </div>
                    <input type="hidden" name="profile[user_type]" id="userType" value="patient" required>
                </div>

                <div class="space-y-4 border-t border-gray-100 pt-6">
                    <h3 class="text-lg font-semibold text-teal-900 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Dados Pessoais
                    </h3>

                    <div>
                        <label class="block text-sm font-medium text-teal-900 mb-2">Foto de Perfil</label>
                        <input type="file" name="profile[profile_picture]" accept="image/*" 
                            class="block w-full text-sm text-slate-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-full file:border-0
                            file:text-sm file:font-semibold
                            file:bg-teal-50 file:text-teal-700
                            hover:file:bg-teal-100 transition-all cursor-pointer">
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-teal-900 mb-1">Nome Completo *</label>
                            <input type="text" name="user[name]" value="{{ old('user.name') }}" required placeholder="João Silva"
                                class="w-full px-4 py-3 border border-teal-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-teal-900 mb-1">Email *</label>
                            <input type="email" name="user[email]" value="{{ old('user.email') }}" required placeholder="joao@email.com"
                                class="w-full px-4 py-3 border border-teal-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition-all">
                        </div>
                    </div>

                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-teal-900 mb-1">Data de Nascimento</label>
                            <input type="date" name="profile[birth_date]" value="{{ old('profile.birth_date') }}"
                                class="w-full px-4 py-3 border border-teal-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-teal-900 mb-1">Telemóvel</label>
                            <input type="tel" name="profile[phone]" value="{{ old('profile.phone') }}" placeholder="+351..."
                                class="w-full px-4 py-3 border border-teal-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition-all">
                        </div>
                         <div>
                            <label class="block text-sm font-medium text-teal-900 mb-1">NIF</label>
                            <input type="text" name="profile[tax_id]" value="{{ old('profile.tax_id') }}" placeholder="123456789"
                                class="w-full px-4 py-3 border border-teal-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition-all">
                        </div>
                    </div>

                    <div class="grid md:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-teal-900 mb-1">Morada</label>
                            <input type="text" name="profile[address]" value="{{ old('profile.address') }}" placeholder="Rua, Nº, Andar"
                                class="w-full px-4 py-3 border border-teal-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-teal-900 mb-1">NISS (Seg. Social)</label>
                            <input type="text" name="profile[social_security_number]" value="{{ old('profile.social_security_number') }}" placeholder="123..."
                                class="w-full px-4 py-3 border border-teal-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition-all">
                        </div>
                    </div>
                </div>

                <div id="medicalInfoSection" class="space-y-4 border-t border-gray-100 pt-6">
                    <h3 class="text-lg font-semibold text-teal-900 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                        Informações Médicas
                    </h3>
                    
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-teal-900 mb-1">Tipo Sanguíneo</label>
                            <select name="medical_info[blood_type]" class="w-full px-4 py-3 border border-teal-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 bg-white">
                                <option value="">Selecione...</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-teal-900 mb-1">Contacto de Emergência</label>
                            <input type="text" name="medical_info[emergency_contact]" value="{{ old('medical_info.emergency_contact') }}" placeholder="Nome e Telefone"
                                class="w-full px-4 py-3 border border-teal-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-teal-900 mb-1">Alergias</label>
                        <textarea name="medical_info[allergies]" rows="2" placeholder="Liste quaisquer alergias conhecidas..."
                            class="w-full px-4 py-3 border border-teal-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 resize-none">{{ old('medical_info.allergies') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-teal-900 mb-1">Condições Médicas / Histórico</label>
                        <textarea name="medical_info[medical_conditions]" rows="2" placeholder="Diabetes, Hipertensão, etc..."
                            class="w-full px-4 py-3 border border-teal-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 resize-none">{{ old('medical_info.medical_conditions') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-teal-900 mb-1">Medicação Atual</label>
                        <textarea name="medical_info[current_medications]" rows="2" placeholder="Nome do medicamento e dosagem..."
                            class="w-full px-4 py-3 border border-teal-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 resize-none">{{ old('medical_info.current_medications') }}</textarea>
                    </div>
                </div>

                <div id="professionalDocs" class="space-y-4 border-t border-gray-100 pt-6 hidden">
                    <h3 class="text-lg font-semibold text-teal-900 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Qualificações
                    </h3>

                    <div class="bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800 p-3 rounded text-sm">
                        A sua cédula será verificada manualmente pela equipa Cura junto da Ordem (OM/OE/OMD).
                        Só poderá aceitar serviços depois desta verificação.
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-teal-900 mb-1">Número de Cédula <span class="text-red-500">*</span></label>
                        <input type="text" name="qualifications[cedula_number]" value="{{ old('qualifications.cedula_number') }}" placeholder="Ex: 12345"
                            class="w-full px-4 py-3 border border-teal-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-teal-900 mb-1">Resumo Profissional</label>
                        <textarea name="qualifications[description]" rows="3" placeholder="Descreva a sua experiência profissional..."
                            class="w-full px-4 py-3 border border-teal-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 resize-none">{{ old('qualifications.description') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-teal-900 mb-2">Comprovativo de Cédula/Habilitações <span class="text-red-500">*</span></label>
                        <div class="border-2 border-dashed border-teal-200 rounded-xl p-6 text-center hover:border-teal-400 transition-colors cursor-pointer relative bg-teal-50/30">
                            <input type="file" name="qualifications[document]" accept="application/pdf,image/jpeg,image/png" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            <svg class="w-8 h-8 text-teal-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <p class="text-sm text-teal-700 font-medium mb-1">Carregar Ficheiro</p>
                            <p class="text-xs text-teal-500">PDF, JPG ou PNG (Máx 5MB)</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4 border-t border-gray-100 pt-6">
                    <h3 class="text-lg font-semibold text-teal-900 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Segurança da Conta
                    </h3>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-teal-900 mb-1">Palavra-passe *</label>
                            <input type="password" name="user[password]" required placeholder="Min. 8 caracteres"
                                class="w-full px-4 py-3 border border-teal-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-teal-900 mb-1">Confirmar Palavra-passe *</label>
                            <input type="password" name="user[password_confirmation]" required placeholder="Repita a palavra-passe"
                                class="w-full px-4 py-3 border border-teal-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition-all">
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-6">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="consent" value="1" required
                               class="mt-1 w-5 h-5 rounded border-teal-300 text-teal-600 focus:ring-teal-500">
                        <span class="text-sm text-teal-900">
                            Li e aceito os
                            <a href="{{ route('terms-of-service') }}" target="_blank" class="font-semibold underline">Termos de Serviço</a>
                            e a
                            <a href="{{ route('privacy-policy') }}" target="_blank" class="font-semibold underline">Política de Privacidade</a>,
                            incluindo o tratamento de dados de saúde para fins de prestação do serviço (RGPD Art. 9.º, n.º 2, alínea h).
                        </span>
                    </label>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 pt-4">
                    <button type="submit"
                        class="flex-1 bg-teal-600 hover:bg-teal-700 focus:bg-teal-700 text-white font-semibold py-4 rounded-xl transition-colors shadow-lg shadow-teal-200">
                        Criar Conta
                    </button>
                    <a href="/"
                        class="flex-1 bg-teal-50 hover:bg-teal-100 text-teal-700 font-semibold py-4 rounded-xl transition-colors text-center border border-teal-100">
                        Cancelar
                    </a>
                </div>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-teal-600">
                    Já tem conta? <a href="{{ route('login') }}" class="font-bold text-teal-700 hover:text-teal-900 underline">Entrar</a>
                </p>
            </div>
        </div>
    </div>

    <script>
    document.querySelectorAll('.user-type-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // 1. Reset Visual dos Botões
            document.querySelectorAll('.user-type-btn').forEach(b => {
                b.classList.remove('bg-teal-600', 'text-white', 'shadow-md');
                b.classList.add('bg-teal-50', 'text-teal-700');
            });
            
            // 2. Ativar Botão Clicado
            this.classList.remove('bg-teal-50', 'text-teal-700');
            this.classList.add('bg-teal-600', 'text-white', 'shadow-md');
            
            // 3. Atualizar Input Hidden
            const userType = this.dataset.type;
            document.getElementById('userType').value = userType;
            
            // 4. Lógica de Mostrar/Esconder Secções
            const professionalSection = document.getElementById('professionalDocs');
            const medicalSection = document.getElementById('medicalInfoSection');
            
            // Selecionar o input de ficheiro (adicionei um ID 'qualificationsDoc' no passo 1, ou usa o seletor por name)
            const docInput = document.querySelector('input[name="qualifications[document]"]');

            const isProfessional = ['medical_assistant', 'nurse', 'doctor'].includes(userType);
            const isPatient = (userType === 'patient');

            // Toggle Profissional
            if (isProfessional) {
                professionalSection.classList.remove('hidden');
                // TORNAR OBRIGATÓRIO SE FOR PROFISSIONAL
                docInput.setAttribute('required', 'required');
            } else {
                professionalSection.classList.add('hidden');
                // REMOVER OBRIGATORIEDADE SE NÃO FOR PROFISSIONAL
                docInput.removeAttribute('required');
                docInput.value = ''; // Opcional: limpar o ficheiro se mudar de ideia
            }

            // Toggle Utente (Médico)
            if (isPatient) {
                medicalSection.classList.remove('hidden');
            } else {
                medicalSection.classList.add('hidden');
            }
        });
    });
</script>
</body>
</html>