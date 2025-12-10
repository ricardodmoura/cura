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
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\assertGuest;

uses(RefreshDatabase::class);

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

test('TC-02: Visitante consegue registar-se como Utente ', function () {
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
        ],
        'medical_info' => [
            'blood_type' => 'O+',
            'allergies' => 'Pólen',
        ],
        'qualifications' => ['description' => '']
    ])->assertRedirect(route('app.index'));

    assertDatabaseHas('users', ['email' => 'utente@teste.com']);
    assertDatabaseHas('profiles', ['user_type' => 'patient']);
    assertDatabaseHas('medical_infos', ['blood_type' => 'O+', 'allergies' => 'Pólen']);
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

        'medical_info' => [], 
        'qualifications' => []
    ])->assertRedirect(route('app.index'));

    assertDatabaseHas('users', ['email' => 'acompanhante@teste.com']);
    assertDatabaseHas('profiles', ['user_type' => 'companion']);
    $user = User::where('email', 'acompanhante@teste.com')->first();
    expect($user->medicalInfo)->toBeNull();
});

test('TC-05: Visitante consegue registar-se como Enfermeiro', function () {
    Storage::fake('public');
    $doc = UploadedFile::fake()->create('cedulaEnfermeiro.pdf');

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

test('TC-06: Visitante consegue registar-se como Médico e submeter documentos', function () {
    Storage::fake('public');
    $doc = UploadedFile::fake()->create('cedulaMedicos.pdf');

    post(route('register.post'), [
        'user' => [
            'name' => 'Dr. Rui',
            'email' => 'medico@teste.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ],
        'profile' => [
            'user_type' => 'doctor',
            'phone' => '912345678',
        ],
        'medical_info' => [],
        'qualifications' => [
            'description' => 'Licenciatura',
            'document' => $doc
        ]
    ])->assertRedirect(route('app.index'));

    assertDatabaseHas('users', ['email' => 'medico@teste.com']);
    assertDatabaseHas('qualifications', ['description' => 'Licenciatura']);
});

test('TC-07: Verificar recuperação de password', function () {
})->skip('Não implementado - Requer configuração de email');

test('TC-08: Verificar submissão de documentos no registo profissional', function () {
    Storage::fake('public');
    $doc = UploadedFile::fake()->create('cedulaMedicos.pdf');

    post(route('register.post'), [
        'user' => [
            'name' => 'Dra. Ana',
            'email' => 'medico2@teste.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ],
        'profile' => [
            'user_type' => 'doctor',
            'phone' => '912345678',
        ],  
        'medical_info' => [],
        'qualifications' => [
            'description' => 'Licenciatura',
            'document' => $doc
        ]
    ])->assertRedirect(route('app.index'));

    assertDatabaseHas('users', ['email' => 'medico2@teste.com']);
    assertDatabaseHas('qualifications', ['description' => 'Licenciatura']);
});

test('TC-09: Verificar logout', function () {
    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    Profile::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->post(route('logout'))
        ->assertRedirect(route('login'));

    assertGuest();
});

test('TC-10: Verificar visualização de dados pessoais no perfil', function () {
    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    Profile::factory()->create([
        'user_id' => $user->id,
        'phone' => '912345678',
        'user_type' => 'patient'
    ]);

    actingAs($user)
        ->get(route('app.user.show', $user))
        ->assertStatus(200)
        ->assertSee('912345678');

});

test('TC-11: Verificar visualização de dados médicos no perfil', function () {
    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    Profile::factory()->create([
        'user_id' => $user->id,
        'phone' => '912345678',
        'user_type' => 'patient'
    ]);
    MedicalInfo::factory()->create([
        'user_id' => $user->id,
        'blood_type' => 'A+',
        'allergies' => 'Pólen'
    ]);
    actingAs($user)
        ->get(route('app.user.show', $user))
        ->assertStatus(200)
        ->assertSee('A+')
        ->assertSee('Pólen');
});

test('TC-12: Verificar configuração de notificação', function () {
})->skip('Não implementado - Tabela de notificações foi usada para guardar as notificações, não as preferências do utilizador, sendo que todas as notificações são enviadas pela app.');


test('TC-13: Utente consegue editar perfil', function () {
    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    Profile::factory()->create(['user_id' => $user->id, 'user_type' => 'patient']);
    
    actingAs($user)
        ->put(route('app.user.update', $user), [
            'user' => ['name' => 'Nome Editado', 'email' => $user->email],
            'profile' => [
                'phone' => '999999999', 
                'user_type' => 'patient'
            ],
            'medical_info' => [],
            'qualifications' => []
        ])
        ->assertRedirect(route('app.user.show', $user));

    assertDatabaseHas('users', ['name' => 'Nome Editado']);
    assertDatabaseHas('profiles', ['phone' => '999999999']);
});

test('TC-14: Verificar adição de qualificações', function () {
    /** @var \App\Models\User $nurse */
    $nurse = User::factory()->create();
    Profile::factory()->create(['user_id' => $nurse->id, 'user_type' => 'nurse']);
    Storage::fake('public');
    $doc = UploadedFile::fake()->create('novaCedula.pdf');

    actingAs($nurse)
        ->put(route('app.user.update', $nurse), [
            'user' => ['name' => $nurse->name, 'email' => $nurse->email],
            'profile' => [
                'phone' => '912345678', 
                'user_type' => 'nurse'
            ],
            'medical_info' => [],
            'qualifications' => [
                'description' => 'Especialização em Feridas',
                'document' => $doc
            ]
        ])
        ->assertRedirect(route('app.user.show', $nurse));

    assertDatabaseHas('qualifications', ['description' => 'Especialização em Feridas']);
});

test('TC-15: Verificar eliminação de conta', function () {
    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    Profile::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->delete(route('app.user.destroy', $user))
        ->assertRedirect(route('landing'));

    assertDatabaseMissing('users', ['id' => $user->id]);
    assertDatabaseMissing('profiles', ['user_id' => $user->id]);
});

test('TC-16: Utente consegue solicitar um novo serviço', function () {
    /** @var \App\Models\User $patient */
    $patient = User::factory()->create();
    Profile::factory()->create(['user_id' => $patient->id, 'user_type' => 'patient']);

    actingAs($patient)
        ->post(route('app.service.store'), [
            'service_type' => 'enfermagem',
            'date' => now()->addDays(3)->toDateString(),
            'time' => '10:00',
            'location' => 'Morada do Paciente',
            'price' => 50.00,
        ])
        ->assertRedirect(route('app.service.index'));

    assertDatabaseHas('services', [
        'patient_id' => $patient->id,
        'service_type' => 'Enfermagem',
        'status' => 'pending',
    ]);
});

test('TC-17: Verificar reagendamento de serviço', function () {
    /** @var \App\Models\User $patient */
    $patient = User::factory()->create();
    Profile::factory()->create(['user_id' => $patient->id, 'user_type' => 'patient']);
    
    $service = Service::factory()->create([
        'patient_id' => $patient->id,
        'service_type' => 'Consulta',
        'status' => 'pending',
        'date' => now()->addDays(5)->toDateString(),
        'time' => '14:00',
        'location' => 'Morada Antiga',
        'price' => 100.00,
    ]);

    actingAs($patient)
        ->put(route('app.service.update', $service), [
            'service_type' => 'Consulta',
            'date' => now()->addDays(10)->toDateString(),
            'time' => '16:00',
            'location' => 'Nova Morada',
            'price' => 100.00,
        ])
        ->assertRedirect(route('app.service.index'));

    assertDatabaseHas('services', [
        'id' => $service->id,
        'date' => now()->addDays(10)->toDateString(),
        'time' => '16:00',
        'location' => 'Nova Morada',
    ]);
});

test('TC-18: Verificar cancelamento de serviço', function () {

});

test('TC-19: Verificar visualização de histórico de serviços', function () {

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

test('TC-21: Verificar visualização de estatísticas de serviços do utente', function () {

});

test('TC-22: Verificar visualização de serviços pendentes do profissional', function () {

});

test('TC-23: Verificar aceitação de serviço pelo profissional', function () {
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

test('TC-24: Verificar recusa de serviço pelo profissional', function () {

});

test('TC-25: Verificar visualização de estatísticas do profissional', function () {
    
});

test('TC-26: Verificar cancelamento de serviço pelo profissional', function () {

});

test('TC-27: Verificar acesso aos dados do utente 72h antes', function () {
    
});

test('TC-28: Verificar visualização de serviços aceites/completados', function () {
    
});

test('TC-29: Verificar filtragem de serviços', function () {
    
});

test('TC-30: Verificar exportação de serviço como ICS', function () {
    
});

test('TC-31: Verificar avaliação de serviço', function () {
    
});

test('TC-32: Verificar segurança e proteção de dados', function () {
    
});

test('TC-33: Verificar encriptação de dados sensíveis', function () {
    
});

test('TC-34: Testar usabilidade da interface', function () {

 })->skip('Teste manual: Verificar responsividade em ecrãs < 400px');
    

test('TC-35: Testar desempenho (tempo de resposta < 3s)', function () {
    
})->skip('Teste não-funcional: Validar com ferramentas de benchmark');

test('TC-36: Testar disponibilidade do sistema', function () {
    
})->skip('Teste de infraestrutura');

test('TC-37: Verificar logs de auditoria e backups', function () {
    
})->skip('Teste administrativo');

test('TC-38: Verificar manutenção do código', function () {
    
})->skip('Análise estática de código');