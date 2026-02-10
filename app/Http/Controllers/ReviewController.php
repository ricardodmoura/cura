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
            'rated_professionals' => Review::where('user_id', $user->id)
                ->join('services', 'reviews.service_id', '=', 'services.id')
                ->distinct('services.professional_id')
                ->count('services.professional_id'),
            'this_month' => (clone $baseQuery)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        $reviewsQuery = Review::with(['service.professional.profile'])
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

        $service = Service::with('professional.profile')
            ->where('id', $serviceId)
            ->where('patient_id', Auth::id())
            ->firstOrFail();

        return view('app.review.create', compact('service'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:5',
        ]);

        Review::updateOrCreate(
            [
                'service_id' => $validated['service_id'],
                'user_id' => Auth::id()
            ],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'],
                'updated_at' => now() 
            ]
        );

        return redirect()->route('app.review.index')->with('success', 'Avaliação registada com sucesso!');
    }

    public function show($id)
    {
        // Validar que a review existe e pertence ao utilizador logado
        $review = Review::with(['service.professional.profile'])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('app.review.show', compact('review'));
    }

    public function edit($id)
    {
        $review = Review::with(['service.professional.profile'])
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