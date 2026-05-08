@extends('app.layout.app')

@section('content')
<div class="max-w-2xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold text-teal-900 mb-8 text-center">Editar Avaliação</h1>

    <form action="{{ route('app.review.update', $review->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Info do Serviço --}}
        <div class="bg-teal-50 rounded-3xl p-6 border border-teal-100 flex justify-between items-center">
            <div>
                <p class="text-xs font-bold text-teal-500 uppercase">Avaliar</p>
                <h2 class="text-xl font-bold text-teal-900">{{ $review->ratee?->name ?? 'Pessoa avaliada' }}</h2>
                <p class="text-sm text-teal-600">{{ $review->service->service_type }}</p>
            </div>
        </div>

        {{-- Estrelas Interativas --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 text-center">
            <h3 class="text-lg font-bold text-teal-900 mb-6">Como avalia este atendimento?</h3>
            <input type="hidden" name="rating" id="rating_value" value="{{ $review->rating }}">

            <div class="flex justify-center gap-2" id="star-container">
                @for($i = 1; $i <= 5; $i++)
                    <button type="button" data-val="{{ $i }}" class="star-btn transition-transform hover:scale-110">
                        <svg class="w-10 h-10 text-teal-500" fill="{{ $i <= $review->rating ? '#CCFBF1' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </button>
                @endfor
            </div>
            @error('rating') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
        </div>

        {{-- Comentário --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
            <h3 class="text-lg font-bold text-teal-900 mb-4">O seu comentário</h3>
            <textarea name="comment" rows="5" required class="w-full p-4 border border-teal-100 rounded-2xl focus:ring-2 focus:ring-teal-500 outline-none" placeholder="Conte-nos os detalhes...">{{ old('comment', $review->comment) }}</textarea>
            @error('comment') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-4">
            <button type="button" onclick="history.back()" class="flex-1 py-4 bg-gray-100 text-gray-500 rounded-2xl font-bold">Cancelar</button>
            <button type="submit" class="flex-1 py-4 bg-teal-600 text-white rounded-2xl font-bold shadow-lg shadow-teal-200 hover:bg-teal-700 transition">Guardar</button>
        </div>
    </form>
</div>

<script>
    const stars = document.querySelectorAll('.star-btn');
    const input = document.getElementById('rating_value');

    stars.forEach(btn => {
        btn.addEventListener('click', () => {
            const val = btn.dataset.val;
            input.value = val;

            stars.forEach(s => {
                const svg = s.querySelector('svg');
                if (s.dataset.val <= val) {
                    svg.setAttribute('fill', '#CCFBF1');
                } else {
                    svg.setAttribute('fill', 'none');
                }
            });
        });
    });
</script>
@endsection
