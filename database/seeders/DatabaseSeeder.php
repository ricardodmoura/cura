<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Profile;
use App\Models\MedicalInfo;
use App\Enums\UserType;
use App\Models\Qualification;


class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Ricardo Admin',
            'email' => 'admin@cura.test',
            'password' => Hash::make('password'), // A password é 'password'
        ]);

        Profile::factory()->create([
            'user_id' => $user->id,
            'user_type' => UserType::PATIENT,
        ]);

        MedicalInfo::factory()->create([
            'user_id' => $user->id,
            'blood_type' => 'O+',
        ]);

        User::factory(10)->create()->each(function ($user) {
            Profile::factory()->create([
                'user_id' => $user->id,
                'user_type' => UserType::PATIENT,
            ]);
            
            MedicalInfo::factory()->create([
                'user_id' => $user->id
            ]);
        });

        User::factory(5)->create()->each(function ($user) {
            Profile::factory()->create([
                'user_id' => $user->id,
                'user_type' => UserType::DOCTOR,
            ]);
            
            Qualification::factory()->create(['user_id' => $user->id]);
        });
        
        $this->command->info('Base de dados populada com sucesso!');
        $this->command->info('Login: admin@cura.test | Password: password');
    }
}
