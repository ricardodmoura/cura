<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //

    $reviews = collect([
        // 1. Exemplo: "Por Avaliar" (Baseado no Serviço 1 - Enfermagem)
        // Na prática, isto seria um serviço concluído que ainda não tem registo na tabela 'reviews'
        (object) [
            'id' => null, // Ainda não tem ID de review
            'service_id' => 1,
            'user_id' => 1, 
            'rating' => null,
            'comment' => null,
            'created_at' => null,
            
            // Dados Mapeados do Serviço para a View
            'service_name' => 'Enfermagem Domiciliária',
            'professional_name' => 'Enf. Ana Silva',
            'date' => Carbon::parse('2024-06-15')->format('Y-m-d'),
            'is_pending' => true, // A flag que ativa o sino e esconde o texto
        ],

        // 2. Exemplo: Avaliado com 5 Estrelas (Baseado no Serviço 2 - Consulta)
        (object) [
            'id' => 101,
            'service_id' => 2,
            'user_id' => 1,
            'rating' => 5,
            'comment' => 'Excelente profissional! A Dra. Emily foi muito atenciosa e esclareceu todas as minhas dúvidas com clareza.',
            'created_at' => Carbon::parse('2024-06-12'),
            
            // Dados Mapeados
            'service_name' => 'Consulta Médica Geral',
            'professional_name' => 'Dr. Emily Smith',
            'date' => Carbon::parse('2024-06-10')->format('Y-m-d'),
            'is_pending' => false,
        ],

        // 3. Exemplo: Avaliado com 4 Estrelas (Baseado no Serviço 3 - Fisioterapia)
        (object) [
            'id' => 102,
            'service_id' => 3,
            'user_id' => 1,
            'rating' => 4,
            'comment' => 'O tratamento foi eficaz e senti melhorias logo após a sessão. O único ponto foi um ligeiro atraso no início.',
            'created_at' => Carbon::parse('2024-06-06'),
            
            // Dados Mapeados
            'service_name' => 'Fisioterapia',
            'professional_name' => 'Dr. João Santos',
            'date' => Carbon::parse('2024-06-05')->format('Y-m-d'),
            'is_pending' => false,
        ],
    ]);

        $user = Auth::user();
        //$reviews = Review::where('user_id', $user->id)->get();
        return view('app.review.index', compact('reviews'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('app.review.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReviewRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        //
        return view('app.review.show', compact('review'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review $review)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReviewRequest $request, Review $review)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        //
    }
}
