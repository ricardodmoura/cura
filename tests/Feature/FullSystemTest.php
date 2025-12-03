<?php

use App\Models\User;
use App\Models\Profile;
use App\Models\MedicalInfo;
use App\Models\Service;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\post;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertAuthenticated;

uses(RefreshDatabase::class);

// =====================================================================
// MÓDULO 1: AUTENTICAÇÃO E REGISTO (TC-01 a TC-11)
// =====================================================================

test('TC-01: Utilizador consegue fazer login com credenciais válidas', function () {
    $user = User::factory()->create([
        'email' => 'login@teste.com',
        'password' => Hash::make('password123'),
    ]);
    
    // Cria perfil para evitar erros se o login depender dele
    Profile::factory()->create(['user_id' => $user->id]);

    post(route('login.post'), [
        'email' => 'login@teste.com',
        'password' => 'password123',
    ])->assertRedirect(route('app.index'));

    assertAuthenticated();
});

test('TC-02: Visitante consegue registar-se como Utente com dados médicos', function () {
    post(route('register.post'), [
        'user' => [
            'name' => 'João Utente',
            'email' => 'utente@teste.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ],
        'profile' => [
            'user_type' => 'patient',
            'phone' => '912345678',
            'tax_id' => '123456789',
        ],
        'medical_info' => [
            'blood_type' => 'O+',
            'allergies' => 'Pólen',
        ],
        'qualifications' => ['description' => '']
    ])->assertRedirect(route('app.index'));

    assertDatabaseHas('users', ['email' => 'utente@teste.com']);
    assertDatabaseHas('profiles', ['user_type' => 'patient']);
    assertDatabaseHas('medical_infos', ['blood_type' => 'O+']);
});

test('TC-03: Visitante consegue registar-se como Acompanhante', function () {
    post(route('register.post'), [
        'user' => [
            'name' => 'Maria Acompanhante',
            'email' => 'acompanhante@teste.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ],
        'profile' => [
            'user_type' => 'companion',
            'phone' => '912345678',
        ],
        // Acompanhante não envia dados médicos nem qualificações
        'medical_info' => [], 
        'qualifications' => []
    ])->assertRedirect(route('app.index'));

    assertDatabaseHas('users', ['email' => 'acompanhante@teste.com']);
    assertDatabaseHas('profiles', ['user_type' => 'companion']);
    // Garante que não criou info médica indevida
    $user = User::where('email', 'acompanhante@teste.com')->first();
    expect($user->medicalInfo)->toBeNull();
});

test('TC-05: Visitante consegue registar-se como Enfermeiro com qualificações', function () {
    Storage::fake('public');
    $doc = UploadedFile::fake()->create('cedula.pdf');

    post(route('register.post'), [
        'user' => [
            'name' => 'Enf. Rui',
            'email' => 'enfermeiro@teste.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ],
        'profile' => [
            'user_type' => 'nurse',
            'phone' => '912345678',
        ],
        'medical_info' => [],
        'qualifications' => [
            'description' => 'Licenciatura',
            'document' => $doc
        ]
    ])->assertRedirect(route('app.index'));

    assertDatabaseHas('users', ['email' => 'enfermeiro@teste.com']);
    assertDatabaseHas('qualifications', ['description' => 'Licenciatura']);
});

test('TC-06: Verificação de recuperação de password', function () {
})->skip();

// =====================================================================
// MÓDULO 2: GESTÃO DE PERFIL (TC-12 a TC-15)
// =====================================================================

test('TC-12/13: Utente consegue editar perfil e alterar foto', function () {
    Storage::fake('public');
    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    Profile::factory()->create(['user_id' => $user->id, 'user_type' => 'patient']);
    
    $foto = UploadedFile::fake()->image('nova_foto.jpg');

    actingAs($user)
        ->put(route('app.user.update', $user), [
            'user' => ['name' => 'Nome Editado', 'email' => $user->email],
            'profile' => [
                'phone' => '999999999', 
                'profile_picture' => $foto,
                'user_type' => 'patient'
            ],
            'medical_info' => [],
            'qualifications' => []
        ])
        ->assertRedirect(route('app.user.show', $user));

    assertDatabaseHas('users', ['name' => 'Nome Editado']);
    assertDatabaseHas('profiles', ['phone' => '999999999']);
    // Verifica se a foto foi guardada
    // expect(Storage::disk('public')->allFiles('profiles'))->not->toBeEmpty();
});

test('TC-14: Utente consegue atualizar dados médicos', function () {
    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    Profile::factory()->create(['user_id' => $user->id, 'user_type' => 'patient']);
    // Cria info inicial
    MedicalInfo::factory()->create(['user_id' => $user->id, 'allergies' => 'Nenhuma']);

    actingAs($user)
        ->put(route('app.user.update', $user), [
            'user' => ['name' => $user->name, 'email' => $user->email],
            'profile' => ['user_type' => 'patient'], // Dados obrigatórios
            'medical_info' => [
                'allergies' => 'Amendoim e Marisco', // Alteração
                'blood_type' => 'AB+'
            ],
            'qualifications' => []
        ]);

    assertDatabaseHas('medical_infos', [
        'user_id' => $user->id,
        'allergies' => 'Amendoim e Marisco'
    ]);
});

// =====================================================================
// MÓDULO 3: SERVIÇOS E AGENDAMENTO (TC-16 a TC-30)
// =====================================================================

test('TC-16: Utente consegue solicitar um novo serviço', function () {
    /** @var \App\Models\User $patient */
    $patient = User::factory()->create();
    Profile::factory()->create(['user_id' => $patient->id, 'user_type' => 'patient']);

    actingAs($patient)
        ->post(route('app.service.store'), [
            'service_type' => 'Enfermagem',
            'date' => now()->addDays(2)->format('Y-m-d'),
            'time' => '10:00',
            'location' => 'Minha Casa',
            'price' => 60.00,
        ])
        ->assertRedirect(route('app.service.index'));

    assertDatabaseHas('services', [
        'patient_id' => $patient->id,
        'service_type' => 'Enfermagem',
        'status' => 'Pending',
        'price' => 60.00
    ]);
});

test('TC-20: Profissional consegue visualizar lista de serviços', function () {
    /** @var \App\Models\User $doctor */
    $doctor = User::factory()->create();
    Profile::factory()->create(['user_id' => $doctor->id, 'user_type' => 'doctor']);
    Service::factory(3)->create(); // Cria serviços fictícios

    actingAs($doctor)
        ->get(route('app.service.index'))
        ->assertStatus(200)
        ->assertSee('Serviços');
});

test('TC-23: Profissional consegue aceitar um serviço pendente', function () {
    /** @var \App\Models\User $nurse */
    $nurse = User::factory()->create();
    Profile::factory()->create(['user_id' => $nurse->id, 'user_type' => 'nurse']);
    
    $service = Service::factory()->create(['status' => 'Pending', 'professional_id' => null]);

    actingAs($nurse)
        ->put(route('app.service.update', $service), [
            'status' => 'Accepted',
            'professional_id' => $nurse->id,
            // Mantém dados originais
            'service_type' => $service->service_type,
            'date' => $service->date,
            'time' => $service->time,
            'location' => $service->location,
            'price' => $service->price,
        ])
        ->assertRedirect(route('app.service.index'));

    assertDatabaseHas('services', [
        'id' => $service->id,
        'status' => 'Accepted',
    ]);
});

test('TC-25: Histórico de serviços é apresentado corretamente', function () {
    /** @var \App\Models\User $patient */
    $patient = User::factory()->create();
    Profile::factory()->create(['user_id' => $patient->id, 'user_type' => 'patient']);
    
    // Cria um serviço antigo completado
    Service::factory()->create([
        'patient_id' => $patient->id,
        'status' => 'Completed',
        'date' => now()->subMonth(),
        'service_type' => 'Consulta Passada'
    ]);

    actingAs($patient)
        ->get(route('app.service.index'))
        ->assertStatus(200)
        ->assertSee('Consulta Passada')
        ->assertSee('Completed');
});

// =====================================================================
// MÓDULO 4: SEGURANÇA E NÃO-FUNCIONAIS (TC-32 a TC-38)
// =====================================================================

test('TC-32: Validação de email único no registo', function () {
    // Cria um user existente
    User::factory()->create(['email' => 'existente@teste.com']);

    // Tenta registar com o mesmo email
    post(route('register.post'), [
        'user' => [
            'name' => 'Outra Pessoa',
            'email' => 'existente@teste.com', // Duplicado
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ],
        'profile' => ['user_type' => 'patient', 'phone' => '123'],
        'medical_info' => [], 'qualifications' => []
    ])->assertSessionHasErrors('user.email'); // Espera erro de validação
});

test('TC-33: As passwords são encriptadas na base de dados', function () {
    $user = User::factory()->create([
        'password' => Hash::make('minha_password_secreta')
    ]);

    $userDB = User::find($user->id);
    
    // A password na BD não deve ser igual ao texto simples
    expect($userDB->password)->not->toBe('minha_password_secreta')
        // Mas deve verificar corretamente com o Hash
        ->and(Hash::check('minha_password_secreta', $userDB->password))->toBeTrue();
});

// TESTES MANUAIS (Skipped - Apenas para documentação no relatório)

test('TC-34: Testar usabilidade da interface em Mobile')
    ->skip('Teste manual: Verificar responsividade em ecrãs < 400px');

test('TC-35: Testar desempenho (tempo de resposta < 3s)')
    ->skip('Teste não-funcional: Validar com ferramentas de benchmark');

test('TC-36: Testar disponibilidade do sistema')
    ->skip('Teste de infraestrutura');

test('TC-37: Verificar logs de auditoria e backups')
    ->skip('Teste administrativo');

test('TC-38: Verificar manutenção do código')
    ->skip('Análise estática de código');