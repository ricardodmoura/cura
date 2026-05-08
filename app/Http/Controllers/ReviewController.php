<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Service; // <--- Importante adicionar isto
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $baseQuery = Review::where('user_id', $user->id);

        $stats = [
            'average_rating' => number_format((clone $baseQuery)->avg('rating') ?? 0, 1),
            'total' => (clone $baseQuery)->count(),
            // Pessoas distintas que avaliei (em qualquer direção — prof ou utente).
            'rated_people' => (clone $baseQuery)
                ->join('services', 'reviews.service_id', '=', 'services.id')
                ->whereNotNull('reviews.rating')
                ->selectRaw('CASE WHEN reviews.user_id = services.patient_id THEN services.professional_id ELSE services.patient_id END as ratee_id')
                ->distinct()
                ->count(),
            'this_month' => (clone $baseQuery)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        $reviewsQuery = Review::with(['service.professional.profile', 'service.patient.profile'])
            ->where('user_id', $user->id);

        // Filter by rating
        if ($request->filled('rating')) {
            $reviewsQuery->where('rating', $request->rating);
        }

        $reviews = $reviewsQuery
            ->orderByRaw('CASE WHEN rating IS NULL THEN 0 ELSE 1 END')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('app.review.index', compact('reviews', 'stats'));
    }
    public function create(Request $request)
    {
        $serviceId = $request->query('service_id');

        if (!$serviceId) {
            return redirect()->route('app.review.index')->with('error', 'Serviço não especificado.');
        }

        // Tanto o utente como o profissional podem avaliar o outro lado, mas só após conclusão.
        $service = Service::with(['professional.profile', 'patient.profile'])
            ->where('id', $serviceId)
            ->where(function ($q) {
                $q->where('patient_id', Auth::id())->orWhere('professional_id', Auth::id());
            })
            ->where('status', 'completed')
            ->firstOrFail();

        $ratee = Auth::id() === $service->patient_id ? $service->professional : $service->patient;

        return view('app.review.create', compact('service', 'ratee'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:5',
        ]);

        // Só o paciente ou o profissional do serviço podem avaliar, e só após conclusão.
        $service = Service::where('id', $validated['service_id'])
            ->where(function ($q) {
                $q->where('patient_id', Auth::id())->orWhere('professional_id', Auth::id());
            })
            ->where('status', 'completed')
            ->firstOrFail();

        Review::updateOrCreate(
            ['service_id' => $service->id, 'user_id' => Auth::id()],
            ['rating' => $validated['rating'], 'comment' => $validated['comment'], 'updated_at' => now()],
        );

        return redirect()->route('app.review.index')->with('success', 'Avaliação registada com sucesso!');
    }

    public function show($id)
    {
        $review = Review::with(['service.professional.profile', 'service.patient.profile'])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('app.review.show', compact('review'));
    }

    public function edit($id)
    {
        $review = Review::with(['service.professional.profile', 'service.patient.profile'])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('app.review.edit', compact('review'));
    }

    public function update(Request $request, $id)
    {
        $review = Review::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:5',
        ]);

        $review->update($validated);

        return redirect()->route('app.review.show', $review->id)
            ->with('success', 'Avaliação atualizada com sucesso!');
    }

    public function destroy($id)
    {
        $review = Review::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $review->delete();

        return redirect()->route('app.review.index')
            ->with('success', 'Avaliação eliminada com sucesso.');
    }
}