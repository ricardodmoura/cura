<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Profile;
use App\Models\MedicalInfo;
use App\Models\Qualification;
use App\Models\Service;
use App\Models\Review;
use App\Enums\UserType;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. OS PROTAGONISTAS (Login Fixo)
        |--------------------------------------------------------------------------
        */
        $utente = User::factory()->create([
            'name' => 'Ricardo Utente',
            'email' => 'ricardoUtente@cura.pt',
            'password' => Hash::make('password'),
        ]);

        Profile::factory()->create([
            'user_id' => $utente->id,
            'user_type' => UserType::PATIENT,
        ]);

        MedicalInfo::factory()->create(['user_id' => $utente->id, 'blood_type' => 'O+']);

        $medicoPrincipal = User::factory()->create([
            'name' => 'Dr. Vítor Médico',
            'email' => 'vitorMedico@cura.pt',
            'password' => Hash::make('password'),
        ]);

        Profile::factory()->create([
            'user_id' => $medicoPrincipal->id,
            'user_type' => UserType::DOCTOR,
        ]);

        // Cédula já verificada para o médico principal — pode aceitar serviços de imediato.
        Qualification::factory()->verified()->create(['user_id' => $medicoPrincipal->id]);

        // Conta de administração para a equipa Cura (usar para verificar qualificações).
        $admin = User::factory()->admin()->create([
            'name' => 'Cura Admin',
            'email' => 'admin@cura.pt',
            'password' => Hash::make('password'),
        ]);
        Profile::factory()->create(['user_id' => $admin->id, 'user_type' => UserType::DOCTOR]);

        /*
        |--------------------------------------------------------------------------
        | 2. FIGURANTES
        |--------------------------------------------------------------------------
        */
        $outrosProfissionais = User::factory(10)->create()->each(function ($user) {
            Profile::factory()->create(['user_id' => $user->id, 'user_type' => UserType::DOCTOR]);
            Qualification::factory()->verified()->create(['user_id' => $user->id]);
        });

        $todosMedicos = $outrosProfissionais->push($medicoPrincipal);

        /*
        |--------------------------------------------------------------------------
        | 3. HISTÓRICO (SERVIÇOS PASSADOS)
        |--------------------------------------------------------------------------
        */
        $comentarios = [
            5 => ['Serviço impecável!', 'Muito atencioso.', 'Recomendo vivamente.'],
            4 => ['Bom médico.', 'Tudo correu bem.', 'Gostei da consulta.'],
            3 => ['Foi razoável.', 'Resolveu, mas podia ser mais simpático.'],
            1 => ['Não gostei.', 'Muito tempo de espera.']
        ];

        // 3.1 Concluídos e Avaliados
        foreach($todosMedicos as $index => $medico) {
            if (rand(0, 1) == 0 && $medico->id !== $medicoPrincipal->id) continue;

            // LÓGICA DE DATA: Tem de ser no PASSADO porque já está concluído
            $dataPassada = Carbon::now()->subDays(rand(2, 60)); 

            $service = Service::factory()->create([
                'patient_id' => $utente->id,
                'professional_id' => $medico->id,
                'status' => 'completed',
                'date' => $dataPassada->format('Y-m-d'), // A consulta foi nesta data
                'created_at' => $dataPassada->copy()->subHours(2), // O pedido foi feito 2h antes
                'updated_at' => $dataPassada->copy()->addHour(), // Terminou 1h depois
            ]);

            $rating = rand(3, 5);
            // Patient → Professional
            Review::create([
                'service_id' => $service->id,
                'user_id' => $utente->id,
                'rating' => $rating,
                'comment' => $comentarios[$rating][array_rand($comentarios[$rating])],
                'created_at' => $dataPassada->copy()->addHours(4),
            ]);
            // Professional → Patient (também avaliação bidirecional)
            $reverseRating = rand(4, 5); // utentes seedados com ratings altos
            Review::create([
                'service_id' => $service->id,
                'user_id' => $medico->id,
                'rating' => $reverseRating,
                'comment' => 'Utente colaborativo e pontual.',
                'created_at' => $dataPassada->copy()->addHours(5),
            ]);
        }

        // 3.2 Concluídos - "Por Avaliar" (Ontem ou Anteontem)
        $medicosPorAvaliar = $todosMedicos->random(3);
        foreach($medicosPorAvaliar as $medico) {
            
            $dataRecente = Carbon::now()->subDays(rand(1, 3)); // Passado recente

            $service = Service::factory()->create([
                'patient_id' => $utente->id,
                'professional_id' => $medico->id,
                'status' => 'completed',
                'date' => $dataRecente->format('Y-m-d'), // DATA FIXADA NO PASSADO
                'created_at' => $dataRecente->copy()->subHours(2),
            ]);

            // Stubs nos dois sentidos — ambos veem "Por Avaliar".
            Review::create([
                'service_id' => $service->id,
                'user_id' => $utente->id,
                'rating' => null,
                'comment' => null,
            ]);
            Review::create([
                'service_id' => $service->id,
                'user_id' => $medico->id,
                'rating' => null,
                'comment' => null,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 4. SERVIÇOS FUTUROS (AGENDADOS / PENDENTES)
        |--------------------------------------------------------------------------
        */

        // 4.1 Pendente (Para daqui a 2 dias)
        Service::factory()->create([
            'patient_id' => $utente->id,
            'professional_id' => null,
            'status' => 'pending',
            'date' => Carbon::now()->addDays(2)->format('Y-m-d'), // DATA NO FUTURO
            'created_at' => Carbon::now(), // Pedido criado hoje
        ]);

        // 4.2 Aceite (Para amanhã)
        Service::factory()->create([
            'patient_id' => $utente->id,
            'professional_id' => $medicoPrincipal->id,
            'status' => 'accepted',
            'date' => Carbon::now()->addDay()->format('Y-m-d'), // DATA NO FUTURO
            'created_at' => Carbon::now()->subHour(),
        ]);

        $this->command->info('Base de dados populada!');
        $this->command->info('Utilizador Utente: ricardoUtente@cura.pt / password');
        $this->command->info('Utilizador Médico: vitorMedico@cura.pt / password');
        $this->command->info('Admin Cura:        admin@cura.pt / password');
    }
}