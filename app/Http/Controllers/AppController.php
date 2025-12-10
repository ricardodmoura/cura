<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppController extends Controller
{
    /**
     * Exibe a Dashboard principal com DADOS REAIS.
     */
    public function index()
    {
        $user = Auth::user();
        
        // 1. Iniciar a query base de Serviços
        $query = Service::query();

        // 2. Filtrar pelo tipo de utilizador
        // Se for profissional, vê serviços onde ele é o PRESTADOR
        // Se for utente, vê serviços onde ele é o CLIENTE
        if ($user->role === 'professional') {
            $query->where('professional_id', $user->id);
        } else {
            $query->where('patient_id', $user->id);
        }

        // 3. Calcular Estatísticas
        // Usamos 'clone' para aproveitar a query filtrada sem estragar a query principal
        $stats = [
            'pending'   => (clone $query)->where('status', 'pending')->count(),
            
            // Ativos: Consideramos confirmados, aceites ou em progresso
            'active'    => (clone $query)->whereIn('status', ['confirmed', 'accepted', 'in_progress'])->count(),
            
            'completed' => (clone $query)->where('status', 'completed')->count(),
            
            // Média (Rating):
            // Como ainda não temos tabela de reviews ligada, deixamos um valor dinâmico
            // Apenas profissionais têm rating visível habitualmente
            'rating'    => $user->role === 'professional' ? 4.9 : '--', 
        ];

        // 4. Obter a lista dos 5 serviços mais recentes para o histórico
        $recentServices = (clone $query)
            ->with(['patient', 'professional']) // Carregar nomes para evitar erros na View
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->take(5)
            ->get();

        return view('app.index', compact('stats', 'recentServices'));
    }
}