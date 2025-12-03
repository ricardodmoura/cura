<?php

namespace App\Http\Controllers;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AppController extends Controller
{
    /**
     * Exibe a Dashboard principal da aplicação.
     *
     * Este método é responsável por:
     * 1. Carregar o utilizador autenticado e o seu perfil.
     * 2. Determinar o contexto (Utente vs Profissional).
     * 3. Calcular estatísticas de serviços (Pendentes, Ativos, Completados).
     * 4. Pegar na lista dos 5 serviços mais recentes relacionados com o utilizador.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
   
        // --- DADOS DE TESTE ---
        // 1. Mock das Estatísticas
        $stats = [
            'pending' => 3,
            'active' => 2,
            'completed' => 24,
            'rating' => 4.8, 
        ];
        // 2. Mock dos Serviços
        // 'collect' para simular uma coleção do Eloquent
        // (object) para simular Models
        // Carbon::parse para simular campos de data/hora
        $recentServices = collect([
            (object) [
                'id' => 1,
                'service_type' => 'Enfermagem Domiciliária',
                'date' => Carbon::parse('2024-06-15'),
                'time' => Carbon::parse('14:30'),
                'status' => 'Pending',
                'patient' => (object) ['name' => 'Alice Johnson'],
                'professional' => (object) ['name' => 'Enf. Ana Silva'],
            ],
            (object) [
                'id' => 2,
                'service_type' => 'Consulta Médica Geral',
                'date' => Carbon::parse('2024-06-10'),
                'time' => Carbon::parse('09:00'),
                'status' => 'Accepted',
                'patient' => (object) ['name' => 'Bob Brown'],
                'professional' => (object) ['name' => 'Dr. Emily Smith'],
            ],
            (object) [
                'id' => 3,
                'service_type' => 'Fisioterapia',
                'date' => Carbon::parse('2024-06-05'),
                'time' => Carbon::parse('16:45'),
                'status' => 'Completed',
                'patient' => (object) ['name' => 'Carlos Ruiz'],
                'professional' => (object) ['name' => 'Dr. João Santos'],
            ],
            (object) [
                'id' => 4,
                'service_type' => 'Apoio Domiciliário',
                'date' => Carbon::parse('2024-06-01'),
                'time' => Carbon::parse('11:00'),
                'status' => 'Cancelled',
                'patient' => (object) ['name' => 'Diana Prince'],
                'professional' => (object) ['name' => 'Aux. Maria Costa'],
            ],
        ]);

        return view('app.index', compact('recentServices', 'stats'));
    }
}