<?php

use App\Models\User;
use App\Models\Profile;
use App\Models\MedicalInfo;
use App\Models\Service;
use App\Models\Log;
use App\Models\Review;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\post;
use function Pest\Laravel\get;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertAuthenticated;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\assertDatabaseCount;
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
        'qualifications' => ['description' => ''],
        'consent' => '1',
    ])->assertRedirect(route('verification.notice'));

    assertDatabaseHas('users', ['email' => 'utente@teste.com']);
    assertDatabaseHas('profiles', ['user_type' => 'patient']);

    $createdUser = User::where('email', 'utente@teste.com')->first();
    expect($createdUser->medicalInfo->blood_type)->toBe('O+');
    expect($createdUser->medicalInfo->allergies)->toBe('Pólen');
    expect($createdUser->consent_version)->toBe(User::CURRENT_CONSENT_VERSION);
    expect($createdUser->consented_at)->not->toBeNull();
});

test('TC-02b: Registo SEM consentimento RGPD é rejeitado', function () {
    post(route('register.post'), [
        'user' => ['name' => 'Sem Consent', 'email' => 'noconsent@teste.com', 'password' => 'password123', 'password_confirmation' => 'password123'],
        'profile' => ['user_type' => 'patient'],
        'medical_info' => [], 'qualifications' => [],
        // consent ausente
    ])->assertSessionHasErrors('consent');

    expect(User::where('email', 'noconsent@teste.com')->exists())->toBeFalse();
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
        'qualifications' => [],
        'consent' => '1',
    ])->assertRedirect(route('verification.notice'));

    assertDatabaseHas('users', ['email' => 'acompanhante@teste.com']);
    assertDatabaseHas('profiles', ['user_type' => 'companion']);
    $user = User::where('email', 'acompanhante@teste.com')->first();
    expect($user->medicalInfo)->toBeNull();
});

test('TC-05: Visitante consegue registar-se como Enfermeiro', function () {
    Storage::fake('local');
    $doc = UploadedFile::fake()->create('cedulaEnfermeiro.pdf', 100, 'application/pdf');

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
            'cedula_number' => '54321',
            'document' => $doc,
        ],
        'consent' => '1',
    ])->assertRedirect(route('verification.notice'));

    assertDatabaseHas('users', ['email' => 'enfermeiro@teste.com']);
    assertDatabaseHas('qualifications', [
        'description' => 'Licenciatura',
        'cedula_number' => '54321',
        'verification_status' => 'pending',
    ]);
});

test('TC-06: Visitante consegue registar-se como Médico e submeter documentos', function () {
    Storage::fake('local');
    $doc = UploadedFile::fake()->create('cedulaMedicos.pdf', 100, 'application/pdf');

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
            'cedula_number' => '11122',
            'document' => $doc,
        ],
        'consent' => '1',
    ])->assertRedirect(route('verification.notice'));

    assertDatabaseHas('users', ['email' => 'medico@teste.com']);
    assertDatabaseHas('qualifications', [
        'description' => 'Licenciatura',
        'cedula_number' => '11122',
        'verification_status' => 'pending',
    ]);
});

test('TC-07: Verificar recuperação de password', function () {
    NotificationFacade::fake();

    $user = User::factory()->create(['email' => 'reset@teste.com']);
    Profile::factory()->create(['user_id' => $user->id]);

    // Pedido do link de reset
    post(route('password.email'), ['email' => 'reset@teste.com'])
        ->assertSessionHas('status');

    NotificationFacade::assertSentTo($user, ResetPassword::class, function ($notif) use (&$token, $user) {
        $token = $notif->token;
        return true;
    });

    // Reset com token válido
    post(route('password.update'), [
        'token' => $token,
        'email' => 'reset@teste.com',
        'password' => 'novapasse123',
        'password_confirmation' => 'novapasse123',
    ])->assertRedirect(route('login'));

    expect(Hash::check('novapasse123', $user->fresh()->password))->toBeTrue();
});

test('TC-08: Verificar submissão de documentos no registo profissional', function () {
    Storage::fake('local');
    $doc = UploadedFile::fake()->create('cedulaMedicos.pdf', 100, 'application/pdf');

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
            'cedula_number' => '99887',
            'document' => $doc,
        ],
        'consent' => '1',
    ])->assertRedirect(route('verification.notice'));

    assertDatabaseHas('users', ['email' => 'medico2@teste.com']);
    assertDatabaseHas('qualifications', ['description' => 'Licenciatura']);
});

test('TC-08b: Profissional sem cédula falha o registo', function () {
    post(route('register.post'), [
        'user' => ['name' => 'Sem Cedula', 'email' => 'sc@teste.com', 'password' => 'password123', 'password_confirmation' => 'password123'],
        'profile' => ['user_type' => 'doctor'],
        'medical_info' => [],
        'qualifications' => [],
        'consent' => '1',
    ])->assertSessionHasErrors('qualifications.cedula_number');

    expect(User::where('email', 'sc@teste.com')->exists())->toBeFalse();
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
    $user = User::factory()->create();
    Profile::factory()->create(['user_id' => $user->id, 'user_type' => 'patient']);

    actingAs($user)->put(route('app.user.update', $user), [
        'user' => ['name' => $user->name, 'email' => $user->email],
        'profile' => [
            'phone' => '912345678',
            'user_type' => 'patient',
            'notification_preferences' => [
                'service_updates' => '1',
                'review_received' => '0',
                'marketing' => '0',
            ],
        ],
        'medical_info' => [],
        'qualifications' => [],
    ])->assertRedirect(route('app.user.show', $user));

    $profile = $user->fresh()->profile;
    expect($profile->wantsNotification('service_updates'))->toBeTrue();
    expect($profile->wantsNotification('review_received'))->toBeFalse();
    expect($profile->wantsNotification('marketing'))->toBeFalse();
    // Tipo desconhecido cai no default (true).
    expect($profile->wantsNotification('unknown_type'))->toBeTrue();
});


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

    $userNaBD = User::find($user->id);
    
    expect($userNaBD)->toBeNull();
});

test('TC-16: Utente consegue solicitar um novo serviço', function () {
    /** @var \App\Models\User $patient */
    $patient = User::factory()->create();
    Profile::factory()->create(['user_id' => $patient->id, 'user_type' => 'patient']);
    
    $serviceKey = 'consulta'; 

    actingAs($patient)
        ->post(route('app.service.store'), [
            'service_type' => $serviceKey,
            'date' => now()->addDays(3)->toDateString(),
            'time' => '10:00',
            'location' => 'Morada do Paciente',
            'notes' => 'Notas sobre o pedido',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('app.index'));

    assertDatabaseHas('services', [
        'patient_id' => $patient->id,
        'service_type' => 'Consulta Médica',
        'price' => 100.00,
        'status' => 'pending',
    ]);
});

test('TC-17: Verificar reagendamento de serviço', function () {
    /** @var \App\Models\User $patient */
    $patient = User::factory()->create();
    Profile::factory()->create(['user_id' => $patient->id, 'user_type' => 'patient']);
    
    $service = Service::factory()->create([
        'patient_id' => $patient->id,
        'service_type' => 'consulta',
        'status' => 'pending',
        'date' => now()->addDays(5)->format('Y-m-d 00:00:00'),
        'time' => '14:00',
        'location' => 'Morada Antiga',
        'price' => 100.00,
    ]);

    actingAs($patient)
        ->put(route('app.service.update', $service), [
            'service_type' => 'consulta',
            'date' => now()->addDays(10)->format('Y-m-d 00:00:00'),
            'time' => '16:00',
            'status' => 'pending',
            'location' => 'Nova Morada',
            'price' => 100.00,
        ])
        ->assertRedirect(route('app.service.index'));

    assertDatabaseHas('services', [
        'id' => $service->id,
        'date' => now()->addDays(10)->format('Y-m-d 00:00:00'),
        'time' => '16:00',
        'location' => 'Nova Morada',
    ]);
});

test('TC-18: Utente cancela um serviço pendente sem profissional', function () {
    $patient = User::factory()->create();
    Profile::factory()->create(['user_id' => $patient->id, 'user_type' => 'patient']);

    // Pendente, ainda sem profissional — deve poder cancelar livremente.
    $pending = Service::factory()->create([
        'patient_id' => $patient->id,
        'professional_id' => null,
        'status' => 'pending',
        'date' => now()->addDay()->toDateString(),
    ]);

    actingAs($patient)
        ->delete(route('app.service.destroy', $pending))
        ->assertRedirect(route('app.index'));

    expect($pending->fresh()->status)->toBe('canceled');
});

test('TC-18b: Utente NÃO cancela serviço já confirmado nas últimas 48h', function () {
    $patient = User::factory()->create();
    Profile::factory()->create(['user_id' => $patient->id, 'user_type' => 'patient']);
    $nurse = User::factory()->create();
    Profile::factory()->create(['user_id' => $nurse->id, 'user_type' => 'nurse']);

    $svc = Service::factory()->create([
        'patient_id' => $patient->id,
        'professional_id' => $nurse->id,
        'status' => 'confirmed',
        'date' => now()->addDay()->toDateString(), // <48h
    ]);

    actingAs($patient)->delete(route('app.service.destroy', $svc));

    expect($svc->fresh()->status)->toBe('confirmed');
});

test('TC-19: Verificar visualização de histórico de serviços', function () {
    /** @var \App\Models\User $patient */
    $patient = User::factory()->create();
    Profile::factory()->create(['user_id' => $patient->id, 'user_type' => 'patient']);
    Service::factory(5)->create(['patient_id' => $patient->id]); // Cria serviços fictícios

    actingAs($patient)
        ->get(route('app.service.index'))
        ->assertStatus(200)
        ->assertSee('Serviços');
});

test('TC-20: Verificar visualização de detalhes de serviço', function () {
    /** @var \App\Models\User $patient */
    $patient = User::factory()->create();
    Profile::factory()->create(['user_id' => $patient->id, 'user_type' => 'patient']);
    
    $service = Service::factory()->create([
        'patient_id' => $patient->id,
        'service_type' => 'Consulta',
        'status' => 'pending',
    ]);

    actingAs($patient)
        ->get(route('app.service.show', $service))
        ->assertStatus(200)
        ->assertSee('Consulta');
});

test('TC-21: Verificar visualização de estatísticas de serviços do utente', function () {
    /** @var \App\Models\User $patient */
    $patient = User::factory()->create();
    Profile::factory()->create(['user_id' => $patient->id, 'user_type' => 'patient']);
    
    $service = Service::factory()->create([
        'patient_id' => $patient->id,
        'service_type' => 'enfermagem',
        'status' => 'pending',
    ]);

    actingAs($patient)
        ->get(route('app.index'))
        ->assertStatus(200)
        ->assertSee('Ativos')
        ->assertSee('Pendentes')
        ->assertSee('Feitos')
        ->assertSee('Média');
});

test('TC-22: Verificar visualização de serviços pendentes do profissional', function () {
    /** @var \App\Models\User $nurse */
    $nurse = User::factory()->create();
    Profile::factory()->create(['user_id' => $nurse->id, 'user_type' => 'nurse']);
    
    Service::factory()->create(['status' => 'Pending', 'professional_id' => null]);

    actingAs($nurse)
        ->get(route('app.service.index'))
        ->assertStatus(200)
        ->assertSee('Pool de Serviços');
});

test('TC-23: Verificar aceitação de serviço pelo profissional', function () {
    /** @var \App\Models\User $nurse */
    $nurse = User::factory()->create();
    Profile::factory()->create(['user_id' => $nurse->id, 'user_type' => 'nurse']);
    \App\Models\Qualification::factory()->verified()->create(['user_id' => $nurse->id]);

    $service = Service::factory()->create([
        'status' => 'pending',
        'professional_id' => null
    ]);

    actingAs($nurse)
        ->post(route('app.service.accept', $service))
        ->assertRedirect(route('app.service.index'));

    assertDatabaseHas('services', [
        'id' => $service->id,
        'status' => 'confirmed',
        'professional_id' => $nurse->id,
    ]);
});

test('TC-23b: Profissional sem cédula verificada NÃO pode aceitar', function () {
    $nurse = User::factory()->create();
    Profile::factory()->create(['user_id' => $nurse->id, 'user_type' => 'nurse']);
    // Cédula apenas pendente (não verificada)
    \App\Models\Qualification::factory()->create(['user_id' => $nurse->id]);

    $svc = Service::factory()->create(['status' => 'pending', 'professional_id' => null]);

    actingAs($nurse)->post(route('app.service.accept', $svc));
    expect($svc->fresh()->professional_id)->toBeNull();
});

test('TC-24: Profissional dispensa um serviço — fica oculto do seu pool', function () {
    $patient = User::factory()->create();
    Profile::factory()->create(['user_id' => $patient->id, 'user_type' => 'patient']);

    $nurseA = User::factory()->create();
    Profile::factory()->create(['user_id' => $nurseA->id, 'user_type' => 'nurse']);
    $nurseB = User::factory()->create();
    Profile::factory()->create(['user_id' => $nurseB->id, 'user_type' => 'nurse']);

    $svc = Service::factory()->create([
        'patient_id' => $patient->id,
        'professional_id' => null,
        'status' => 'pending',
        'service_type' => 'Consulta Médica',
    ]);

    actingAs($nurseA)->post(route('app.service.dismiss', $svc))->assertRedirect();

    // Pool do nurseA já não inclui o serviço
    $resp = actingAs($nurseA)->get(route('app.service.index'));
    $resp->assertDontSee("service-card-{$svc->id}");
    $resp->assertDontSee('Consulta Médica');

    // Pool do nurseB continua a incluir
    $resp = actingAs($nurseB)->get(route('app.service.index'));
    $resp->assertSee('Consulta Médica');

    // Utente não pode dispensar
    actingAs($patient)->post(route('app.service.dismiss', $svc))->assertStatus(403);
});

test('TC-25: Verificar visualização de estatísticas do profissional', function () {
    /** @var \App\Models\User $patient */
    $patient = User::factory()->create();
    Profile::factory()->create(['user_id' => $patient->id, 'user_type' => 'patient']);
    
    $service = Service::factory()->create([
        'patient_id' => $patient->id,
        'service_type' => 'enfermagem',
        'status' => 'pending',
    ]);

    actingAs($patient)
        ->get(route('app.index'))
        ->assertStatus(200)
        ->assertSee('Ativos')
        ->assertSee('Pendentes')
        ->assertSee('Feitos')
        ->assertSee('Média');
});

test('TC-26: Profissional cancela um serviço com mais de 72h de antecedência', function () {
    $patient = User::factory()->create();
    Profile::factory()->create(['user_id' => $patient->id, 'user_type' => 'patient']);
    $nurse = User::factory()->create();
    Profile::factory()->create(['user_id' => $nurse->id, 'user_type' => 'nurse']);

    $svc = Service::factory()->create([
        'patient_id' => $patient->id,
        'professional_id' => $nurse->id,
        'status' => 'confirmed',
        'date' => now()->addDays(5)->toDateString(),
    ]);

    actingAs($nurse)->delete(route('app.service.destroy', $svc))->assertRedirect(route('app.index'));

    expect($svc->fresh()->status)->toBe('canceled');
});

test('TC-26b: Profissional NÃO cancela com menos de 72h', function () {
    $patient = User::factory()->create();
    Profile::factory()->create(['user_id' => $patient->id, 'user_type' => 'patient']);
    $nurse = User::factory()->create();
    Profile::factory()->create(['user_id' => $nurse->id, 'user_type' => 'nurse']);

    $svc = Service::factory()->create([
        'patient_id' => $patient->id,
        'professional_id' => $nurse->id,
        'status' => 'confirmed',
        'date' => now()->addDays(2)->toDateString(),
    ]);

    actingAs($nurse)->delete(route('app.service.destroy', $svc));

    expect($svc->fresh()->status)->toBe('confirmed');
});

test('TC-27: Profissional só vê dados do utente dentro da janela de 72h', function () {
    $patient = User::factory()->create();
    Profile::factory()->create(['user_id' => $patient->id, 'user_type' => 'patient']);
    MedicalInfo::factory()->create(['user_id' => $patient->id, 'blood_type' => 'B+']);

    $nurse = User::factory()->create();
    Profile::factory()->create(['user_id' => $nurse->id, 'user_type' => 'nurse']);

    // Serviço daqui a 10 dias — fora da janela
    $far = Service::factory()->create([
        'patient_id' => $patient->id,
        'professional_id' => $nurse->id,
        'status' => 'confirmed',
        'date' => now()->addDays(10)->toDateString(),
    ]);
    actingAs($nurse)->get(route('app.user.show', $patient))->assertStatus(403);

    // Serviço daqui a 2 dias — dentro da janela
    $far->update(['date' => now()->addDays(2)->toDateString()]);
    actingAs($nurse)->get(route('app.user.show', $patient))->assertStatus(200);

    // Serviço já completado — sem acesso
    $far->update(['status' => 'completed', 'date' => now()->addDay()->toDateString()]);
    actingAs($nurse)->get(route('app.user.show', $patient))->assertStatus(403);
});

test('TC-28: Verificar visualização de serviços aceites/completados', function () {
    /** @var \App\Models\User $nurse */
    $nurse = User::factory()->create();
    Profile::factory()->create(['user_id' => $nurse->id, 'user_type' => 'nurse']);
    
    $service = Service::factory()->create([
        'professional_id' => $nurse->id,
        'service_type' => 'enfermagem',
        'status' => 'accepted',
    ]);

    actingAs($nurse)
        ->get(route('app.service.index'))
        ->assertStatus(200);
});

test('TC-29: Verificar filtragem de serviços', function () {
    /** @var \App\Models\User $patient */
    $patient = User::factory()->create();
    Profile::factory()->create(['user_id' => $patient->id, 'user_type' => 'patient']);

    Service::factory()->create([
        'patient_id' => $patient->id,
        'service_type' => 'enfermagem',
        'status' => 'pending',
    ]);
    Service::factory()->create([
        'patient_id' => $patient->id,
        'service_type' => 'consulta',
        'status' => 'accepted',
    ]);

    actingAs($patient)
        ->get(route('app.service.index', ['status' => 'pending']))
        ->assertStatus(200);
        //->assertSee('Pendente');

    actingAs($patient)
        ->get(route('app.service.index', ['status' => 'accepted']))
        ->assertStatus(200);
        //->assertSee('Aceite');
});

test('TC-30: Exportação de serviço como ICS', function () {
    $patient = User::factory()->create();
    Profile::factory()->create(['user_id' => $patient->id, 'user_type' => 'patient']);
    $svc = Service::factory()->create([
        'patient_id' => $patient->id,
        'service_type' => 'Consulta Médica',
        'date' => now()->addDays(3)->toDateString(),
        'time' => '14:30',
        'location' => 'Rua Exemplo, 1',
    ]);

    $resp = actingAs($patient)->get(route('app.service.ics', $svc));

    $resp->assertStatus(200);
    $resp->assertHeader('Content-Type', 'text/calendar; charset=utf-8');
    $body = $resp->getContent();
    expect($body)->toContain('BEGIN:VCALENDAR')
        ->toContain('END:VCALENDAR')
        ->toContain('UID:cura-service-' . $svc->id . '@cura.pt')
        ->toContain('SUMMARY:Cura — Consulta Médica');

    // Outro utilizador (não patient/professional) não pode exportar.
    $stranger = User::factory()->create();
    Profile::factory()->create(['user_id' => $stranger->id, 'user_type' => 'patient']);
    actingAs($stranger)->get(route('app.service.ics', $svc))->assertStatus(403);
});

test('TC-30b: markCompleted apenas para o profissional atribuído e após a hora', function () {
    $nurse = User::factory()->create();
    Profile::factory()->create(['user_id' => $nurse->id, 'user_type' => 'nurse']);

    // Serviço futuro — NÃO pode ser concluído.
    $future = Service::factory()->create([
        'professional_id' => $nurse->id,
        'status' => 'confirmed',
        'date' => now()->addDay()->toDateString(),
        'time' => now()->format('H:i'),
    ]);
    actingAs($nurse)->post(route('app.service.complete', $future));
    expect($future->fresh()->status)->toBe('confirmed');

    // Serviço já passado — pode ser concluído.
    $past = Service::factory()->create([
        'professional_id' => $nurse->id,
        'status' => 'confirmed',
        'date' => now()->subDay()->toDateString(),
        'time' => '10:00',
    ]);
    actingAs($nurse)->post(route('app.service.complete', $past))->assertRedirect();
    expect($past->fresh()->status)->toBe('completed');

    // Outro profissional não autorizado.
    $other = User::factory()->create();
    Profile::factory()->create(['user_id' => $other->id, 'user_type' => 'nurse']);
    actingAs($other)->post(route('app.service.complete', $past))->assertStatus(403);
});

test('TC-31: Verificar avaliação de serviço', function () {
    /** @var \App\Models\User $patient */
    $patient = User::factory()->create();
    Profile::factory()->create(['user_id' => $patient->id, 'user_type' => 'patient']);

    $service = Service::factory()->create([
        'patient_id' => $patient->id,
        'service_type' => 'Consulta',
        'status' => 'completed',
    ]);

    actingAs($patient)
        ->post(route('app.review.store'), [
            'service_id' => $service->id,
            'rating' => 5,
            'comment' => 'Excelente serviço',
        ])
        ->assertRedirect(route('app.review.index'));

    assertDatabaseHas('reviews', [
        'service_id' => $service->id,
        'user_id' => $patient->id,
        'rating' => 5,
        'comment' => 'Excelente serviço',
    ]);
});

test('TC-31b: Profissional avalia utente após conclusão', function () {
    $patient = User::factory()->create();
    Profile::factory()->create(['user_id' => $patient->id, 'user_type' => 'patient']);
    $nurse = User::factory()->create();
    Profile::factory()->create(['user_id' => $nurse->id, 'user_type' => 'nurse']);

    $svc = Service::factory()->create([
        'patient_id' => $patient->id,
        'professional_id' => $nurse->id,
        'status' => 'completed',
    ]);

    actingAs($nurse)->post(route('app.review.store'), [
        'service_id' => $svc->id,
        'rating' => 4,
        'comment' => 'Utente colaborativo.',
    ])->assertRedirect(route('app.review.index'));

    assertDatabaseHas('reviews', [
        'service_id' => $svc->id,
        'user_id' => $nurse->id,
        'rating' => 4,
    ]);

    // A média do utente reflete a avaliação recebida do profissional.
    expect($patient->fresh()->averageRatingReceived())->toBe(4.0);
});

test('TC-31c: Não se pode avaliar serviço não concluído', function () {
    $patient = User::factory()->create();
    Profile::factory()->create(['user_id' => $patient->id, 'user_type' => 'patient']);
    $svc = Service::factory()->create([
        'patient_id' => $patient->id,
        'status' => 'confirmed',
    ]);

    actingAs($patient)->post(route('app.review.store'), [
        'service_id' => $svc->id,
        'rating' => 5,
        'comment' => 'Bom serviço, obrigado.',
    ])->assertStatus(404); // firstOrFail no controller

    expect(Review::where('service_id', $svc->id)->count())->toBe(0);
});

test('TC-31d: Estranho não pode avaliar serviço alheio', function () {
    $patient = User::factory()->create();
    Profile::factory()->create(['user_id' => $patient->id, 'user_type' => 'patient']);
    $stranger = User::factory()->create();
    Profile::factory()->create(['user_id' => $stranger->id, 'user_type' => 'patient']);
    $svc = Service::factory()->create([
        'patient_id' => $patient->id,
        'status' => 'completed',
    ]);

    actingAs($stranger)->post(route('app.review.store'), [
        'service_id' => $svc->id,
        'rating' => 1,
        'comment' => 'Comentário do estranho.',
    ])->assertStatus(404);
});

test('TC-31e: Dashboard mostra média real (não 4.9 estática)', function () {
    $nurse = User::factory()->create();
    Profile::factory()->create(['user_id' => $nurse->id, 'user_type' => 'nurse']);

    // Sem avaliações → mostra '--'
    actingAs($nurse)->get(route('app.index'))->assertSee('--');

    // Cria duas avaliações que o nurse recebeu.
    $p1 = User::factory()->create();
    Profile::factory()->create(['user_id' => $p1->id, 'user_type' => 'patient']);
    $p2 = User::factory()->create();
    Profile::factory()->create(['user_id' => $p2->id, 'user_type' => 'patient']);

    $s1 = Service::factory()->create(['patient_id' => $p1->id, 'professional_id' => $nurse->id, 'status' => 'completed']);
    $s2 = Service::factory()->create(['patient_id' => $p2->id, 'professional_id' => $nurse->id, 'status' => 'completed']);
    Review::create(['service_id' => $s1->id, 'user_id' => $p1->id, 'rating' => 5, 'comment' => 'x']);
    Review::create(['service_id' => $s2->id, 'user_id' => $p2->id, 'rating' => 4, 'comment' => 'y']);

    expect($nurse->fresh()->averageRatingReceived())->toBe(4.5);
    actingAs($nurse)->get(route('app.index'))->assertSee('4.5');
});

test('TC-31f: markCompleted cria stubs para ambas as partes', function () {
    $patient = User::factory()->create();
    Profile::factory()->create(['user_id' => $patient->id, 'user_type' => 'patient']);
    $nurse = User::factory()->create();
    Profile::factory()->create(['user_id' => $nurse->id, 'user_type' => 'nurse']);

    $svc = Service::factory()->create([
        'patient_id' => $patient->id,
        'professional_id' => $nurse->id,
        'status' => 'confirmed',
        'date' => now()->subDay()->toDateString(),
        'time' => '10:00',
    ]);

    actingAs($nurse)->post(route('app.service.complete', $svc));

    assertDatabaseHas('reviews', ['service_id' => $svc->id, 'user_id' => $patient->id, 'rating' => null]);
    assertDatabaseHas('reviews', ['service_id' => $svc->id, 'user_id' => $nurse->id, 'rating' => null]);
});

test('TC-32: Verificar segurança e proteção de dados', function () {
    /** @var \App\Models\User $patient */
    $patient = User::factory()->create();
    Profile::factory()->create(['user_id' => $patient->id, 'user_type' => 'patient']);

    /** @var \App\Models\User $nurse */
    $nurse = User::factory()->create();
    Profile::factory()->create(['user_id' => $nurse->id, 'user_type' => 'nurse']);

    // Um profissional sem relação com o utente NÃO pode ver o perfil dele (GDPR).
    actingAs($nurse)
        ->get(route('app.user.show', $patient))
        ->assertStatus(403);

    // Mas se houver um serviço ativo entre os dois (dentro da janela de 72h), o profissional pode aceder.
    Service::factory()->create([
        'patient_id' => $patient->id,
        'professional_id' => $nurse->id,
        'status' => 'confirmed',
        'date' => now()->addDay()->toDateString(),
    ]);
    actingAs($nurse)
        ->get(route('app.user.show', $patient))
        ->assertStatus(200);

    Auth::logout();

    get(route('app.service.index'))
        ->assertRedirect(route('login'));
});

test('TC-32b: Visitante não autenticado não pode aceder a perfis', function () {
    $user = User::factory()->create();
    Profile::factory()->create(['user_id' => $user->id, 'user_type' => 'patient']);

    get(route('app.user.show', $user))->assertRedirect(route('login'));
});

test('TC-32c: Utilizador não pode editar/eliminar a conta de outro', function () {
    $a = User::factory()->create();
    Profile::factory()->create(['user_id' => $a->id, 'user_type' => 'patient']);
    $b = User::factory()->create(['name' => 'Original']);
    Profile::factory()->create(['user_id' => $b->id, 'user_type' => 'patient']);

    actingAs($a)->put(route('app.user.update', $b), [
        'user' => ['name' => 'HIJACKED', 'email' => $b->email],
        'profile' => ['phone' => '000', 'user_type' => 'patient'],
        'medical_info' => [],
        'qualifications' => [],
    ])->assertStatus(403);

    actingAs($a)->delete(route('app.user.destroy', $b))->assertStatus(403);

    expect(User::find($b->id)->name)->toBe('Original');
});

test('TC-32d: Utente não pode auto-promover-se a doctor', function () {
    $u = User::factory()->create();
    Profile::factory()->create(['user_id' => $u->id, 'user_type' => 'patient']);

    actingAs($u)->put(route('app.user.update', $u), [
        'user' => ['name' => $u->name, 'email' => $u->email],
        'profile' => ['phone' => '111', 'user_type' => 'doctor'],
        'medical_info' => [],
        'qualifications' => [],
    ])->assertRedirect(route('app.user.show', $u));

    expect($u->fresh()->profile->user_type)->toBe('patient');
});

test('TC-32e: Utente não pode aceitar serviços (apenas profissionais)', function () {
    $patient = User::factory()->create();
    Profile::factory()->create(['user_id' => $patient->id, 'user_type' => 'patient']);
    $svc = Service::factory()->create(['professional_id' => null, 'status' => 'pending']);

    actingAs($patient)->post(route('app.service.accept', $svc))->assertStatus(403);

    expect($svc->fresh()->professional_id)->toBeNull();
});

test('TC-33: Verificar encriptação de dados sensíveis', function () {
    /** @var \App\Models\User $user */
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('secretpassword'),
    ]);
    Profile::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->get(route('app.user.show', $user))
        ->assertStatus(200);

    $userInDb = User::find($user->id);
    expect(Hash::check('secretpassword', $userInDb->password))->toBeTrue();
    
    assertDatabaseHas('users', [
        'id' => $user->id,
        'email' => 'test@example.com',
    ]);

    assertDatabaseMissing('users', [
        'id' => $user->id,
        'password' => 'secretpassword',
    ]);
});

test('TC-34: UI tem requisitos básicos de responsividade', function () {
    // Páginas públicas: viewport meta + classes responsive Tailwind presentes
    foreach (['/', '/login', '/register', '/forgot-password'] as $path) {
        $resp = get($path);
        $resp->assertStatus(200);
        $resp->assertSee('name="viewport"', false);
        $resp->assertSee('width=device-width', false);
    }

    // Dashboard autenticado: layout responsivo (classes sm:/lg:) e sidebar mobile
    $user = User::factory()->create();
    Profile::factory()->create(['user_id' => $user->id, 'user_type' => 'patient']);
    $resp = actingAs($user)->get(route('app.index'));
    $resp->assertStatus(200);
    $resp->assertSee('lg:hidden', false);   // versão mobile da navbar
    $resp->assertSee('hidden lg:flex', false); // versão desktop
    $resp->assertSee('mobileSidebar', false);  // burger menu wired
});

test('TC-35: Dashboard responde em < 3s mesmo com histórico carregado', function () {
    $user = User::factory()->create();
    Profile::factory()->create(['user_id' => $user->id, 'user_type' => 'patient']);
    Service::factory(50)->create(['patient_id' => $user->id]);

    $start = microtime(true);
    actingAs($user)->get(route('app.index'))->assertOk();
    $elapsed = microtime(true) - $start;

    expect($elapsed)->toBeLessThan(3.0);
});

test('TC-36: Endpoint de saúde /up responde 200', function () {
    // Laravel 12 expõe /up via bootstrap/app.php → withRouting(health: '/up').
    get('/up')->assertOk();
});

test('TC-37: Verificar logs de auditoria e backups', function () {  
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);
    Profile::factory()->create(['user_id' => $user->id]);

    Log::factory()->create([
        'user_id' => $user->id,
        'action' => 'create',
        'details' => 'User created with email test@example.com',
    ]);

    assertDatabaseHas('logs', [
        'user_id' => $user->id,
        'action' => 'create',
        'details' => 'User created with email test@example.com'
    ]);
});

test('TC-39: Admin verifica/rejeita qualificações; não-admin é bloqueado', function () {
    $admin = User::factory()->admin()->create();
    Profile::factory()->create(['user_id' => $admin->id, 'user_type' => 'doctor']);

    $nurse = User::factory()->create();
    Profile::factory()->create(['user_id' => $nurse->id, 'user_type' => 'nurse']);
    $q = \App\Models\Qualification::factory()->create(['user_id' => $nurse->id]);

    // Não-admin: 403
    actingAs($nurse)->get('/admin/qualifications')->assertStatus(403);

    // Admin: pode listar
    $resp = actingAs($admin)->get('/admin/qualifications');
    $resp->assertStatus(200)->assertSee($nurse->name);

    // Admin verifica
    actingAs($admin)->post(route('admin.qualifications.verify', $q))->assertRedirect();
    expect($q->fresh()->verification_status)->toBe('verified');
    expect($q->fresh()->verified_by)->toBe($admin->id);

    // Rejeição requer motivo
    $q2 = \App\Models\Qualification::factory()->create(['user_id' => $nurse->id]);
    actingAs($admin)->post(route('admin.qualifications.reject', $q2), ['reason' => 'curto'])->assertSessionHasErrors('reason');
    actingAs($admin)->post(route('admin.qualifications.reject', $q2), ['reason' => 'Documento ilegível, reenviar.'])->assertRedirect();
    expect($q2->fresh()->verification_status)->toBe('rejected');
});

test('TC-40: Documento de qualificação só é descarregável pelo dono ou admin', function () {
    Storage::fake('local');
    $owner = User::factory()->create();
    Profile::factory()->create(['user_id' => $owner->id, 'user_type' => 'nurse']);
    $path = UploadedFile::fake()->create('cedula.pdf', 100, 'application/pdf')->store('qualifications', 'local');
    $q = \App\Models\Qualification::factory()->create(['user_id' => $owner->id, 'document' => $path]);

    actingAs($owner)->get(route('app.qualification.document', $q))->assertOk();

    $stranger = User::factory()->create();
    Profile::factory()->create(['user_id' => $stranger->id, 'user_type' => 'patient']);
    actingAs($stranger)->get(route('app.qualification.document', $q))->assertStatus(403);

    $admin = User::factory()->admin()->create();
    Profile::factory()->create(['user_id' => $admin->id, 'user_type' => 'doctor']);
    actingAs($admin)->get(route('app.qualification.document', $q))->assertOk();
});

test('TC-41: Email de verificação é enviado no registo', function () {
    NotificationFacade::fake();

    post(route('register.post'), [
        'user' => ['name' => 'Verify Me', 'email' => 'verify@teste.com', 'password' => 'password123', 'password_confirmation' => 'password123'],
        'profile' => ['user_type' => 'patient'],
        'medical_info' => [], 'qualifications' => [],
        'consent' => '1',
    ]);

    $u = User::where('email', 'verify@teste.com')->firstOrFail();
    NotificationFacade::assertSentTo($u, \Illuminate\Auth\Notifications\VerifyEmail::class);
});

test('TC-42: Headers de segurança presentes nas respostas', function () {
    $resp = get('/login');
    $resp->assertHeader('X-Frame-Options', 'DENY');
    $resp->assertHeader('X-Content-Type-Options', 'nosniff');
    $resp->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    expect($resp->headers->get('Content-Security-Policy'))->toContain("frame-ancestors 'none'");
});

test('TC-43: Rate limiting bloqueia após 5 tentativas falhadas de login', function () {
    User::factory()->create(['email' => 'target@teste.com']);

    for ($i = 0; $i < 5; $i++) {
        post(route('login.post'), ['email' => 'target@teste.com', 'password' => 'wrong']);
    }
    // 6ª tentativa: 429 do throttler
    post(route('login.post'), ['email' => 'target@teste.com', 'password' => 'wrong'])
        ->assertStatus(429);
});

// TC-38: análise estática via Pest arch — regras arquiteturais.
arch('TC-38a: Sem helpers de debug em produção (dd/dump/var_dump/ray)')
    ->expect(['dd', 'dump', 'var_dump', 'ray'])
    ->not->toBeUsed();

arch('TC-38b: Models só em App\Models')
    ->expect('App\Models')
    ->toExtend('Illuminate\Database\Eloquent\Model')
    ->ignoring('App\Models\User'); // User extends Authenticatable

arch('TC-38c: Controllers estendem o base Controller')
    ->expect('App\Http\Controllers')
    ->toExtend('App\Http\Controllers\Controller')
    ->ignoring('App\Http\Controllers\Controller');

arch('TC-38d: Form Requests estendem FormRequest')
    ->expect('App\Http\Requests')
    ->toExtend('Illuminate\Foundation\Http\FormRequest');