<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Estatísticas Reais para os cartões com símbolos
        $stats = [
            'average' => number_format($user->reviews()->avg('rating') ?? 0, 1),
            'total' => $user->reviews()->count(),
            'pros_count' => Service::where('patient_id', $user->id)
                            ->whereHas('reviews')
                            ->distinct('professional_id')
                            ->count(),
        ];

        // 1. Serviços concluídos sem avaliação
        $pending = Service::where('patient_id', $user->id)
            ->where('status', 'completed')
            ->whereDoesntHave('reviews')
            ->with('professional')
            ->get()
            ->map(function ($s) {
                $s->is_pending = true;
                $s->service_name = $s->service_type;
                $s->professional_name = $s->professional->name;
                return $s;
            });

        // 2. Avaliações já realizadas
        $completed = Review::where('user_id', $user->id)
            ->with('service.professional')
            ->latest()
            ->get()
            ->map(function ($r) {
                $r->is_pending = false;
                $r->service_name = $r->service->service_type;
                $r->professional_name = $r->service->professional->name;
                $r->date = $r->created_at->format('d/m/Y');
                return $r;
            });

        $reviews = $pending->concat($completed);

        return view('app.review.index', compact('reviews', 'stats'));
    }

    public function create(Request $request)
    {
        $serviceId = $request->query('service_id');
        $service = Service::where('id', $serviceId)
            ->where('patient_id', Auth::id())
            ->where('status', 'completed')
            ->with('professional')
            ->firstOrFail();

        return view('app.review.create', compact('service'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'rating_overall' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10',
        ]);

        Review::create([
            'user_id' => Auth::id(),
            'service_id' => $validated['service_id'],
            'rating' => $validated['rating_overall'],
            'comment' => $validated['comment'],
        ]);

        return redirect()->route('app.review.index')->with('success', 'Avaliação publicada!');
    }
}