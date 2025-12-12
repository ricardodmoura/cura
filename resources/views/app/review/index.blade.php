@extends('app.layout.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-teal-900 mb-2">Minhas Avaliações</h1>
        <p class="text-teal-500 font-medium text-lg">Histórico de feedback e serviços por avaliar</p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
        {{-- Avaliação Média --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-5 flex flex-col justify-between h-40">
            <div class="w-12 h-12 rounded-2xl bg-teal-100 flex items-center justify-center text-teal-900">
                <i data-lucide="star" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-teal-500 uppercase tracking-wide">Avaliação Média</span>
                <p class="text-3xl font-bold text-teal-900 mt-1">{{ $stats['average'] }}</p>
            </div>
        </div>

        {{-- Total Avaliações --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-5 flex flex-col justify-between h-40">
            <div class="w-12 h-12 rounded-2xl bg-teal-100 flex items-center justify-center text-teal-900">
                <i data-lucide="rotate-ccw" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-teal-500 uppercase tracking-wide">Total Avaliações</span>
                <p class="text-3xl font-bold text-teal-900 mt-1">{{ $stats['total'] }}</p>
            </div>
        </div>

        {{-- Profissionais Avaliados --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-5 flex flex-col justify-between h-40">
            <div class="w-12 h-12 rounded-2xl bg-teal-100 flex items-center justify-center text-teal-600">
                <i data-lucide="user" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-teal-500 uppercase tracking-wide">Prof. Avaliados</span>
                <p class="text-3xl font-bold text-teal-900 mt-1">{{ $stats['pros_count'] }}</p>
            </div>
        </div>
    </div>

    {{-- Lista de Itens --}}
    <div class="space-y-4">
        @foreach($reviews as $review)
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-teal-900 leading-tight">{{ $review->service_name }}</h3>
                        <p class="text-sm text-teal-500 mt-1">{{ $review->professional_name }}</p>
                    </div>

                    <div class="flex-shrink-0">
                        @if($review->is_pending)
                            <a href="{{ route('app.review.create', ['service_id' => $review->id]) }}" class="flex items-center gap-2 text-teal-800 font-semibold bg-teal-50 px-4 py-2 rounded-full hover:bg-teal-100 transition">
                                <i data-lucide="bell-ring" class="w-5 h-5 text-teal-600"></i>
                                <span class="text-sm">Por Avaliar</span>
                            </a>
                        @else
                            {{-- Estrelas Teal Preenchidas --}}
                            <div class="flex gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-6 h-6 text-teal-500" 
                                         fill="{{ $i <= $review->rating ? '#CCFBF1' : 'none' }}" 
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                    </svg>
                                @endfor
                            </div>
                        @endif
                    </div>
                </div>

                @if(!$review->is_pending)
                    <p class="text-teal-700 text-sm leading-relaxed mb-4 italic">"{{ $review->comment }}"</p>
                    <div class="flex justify-between text-xs text-teal-400 border-t border-gray-50 pt-3">
                        <span>Avaliado em {{ $review->date }}</span>
                    </div>
                @else
                    <div class="mt-2 text-xs text-teal-400 font-medium">
                        Realizado em: {{ \Carbon\Carbon::parse($review->date)->format('d/m/Y') }}
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endsection