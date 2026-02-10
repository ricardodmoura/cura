@extends('app.layout.app')

@section('title', 'Detalhes da Avaliação')

@section('content')
<div class="max-w-lg mx-auto">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-1">Detalhes Avaliação</h1>
        <p class="text-sm font-medium text-teal-600">Avaliação #{{ $review->id }}</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
        
        <h2 class="text-lg font-bold text-gray-800 mb-5 border-b border-gray-100 pb-2">
            {{ $review->service->professional->profile->specialty ?? 'Especialidade Geral' }}
        </h2>

        <div class="space-y-4 mb-6">
            
            <div class="flex items-center text-teal-700 font-medium">
                <div class="w-6 h-6 mr-3 shrink-0">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
                <span>{{ $review->service->professional->name ?? 'Profissional' }}</span>
            </div>

            <div class="flex items-center text-teal-700 font-medium">
                <div class="w-6 h-6 mr-3 shrink-0">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                </div>
                <span>{{ $review->service->professional->profile->specialty ?? 'Profissional de Saúde' }}</span>
            </div>

            <div class="flex items-center text-teal-700 font-medium">
                <div class="w-6 h-6 mr-3 shrink-0">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </div>
                <span>Serviço realizado em {{ \Carbon\Carbon::parse($review->service->date)->format('Y-m-d') }}</span>
            </div>

            <div class="flex items-center text-teal-700 font-medium">
                <div class="w-6 h-6 mr-3 shrink-0">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
                        <rect x="9" y="3" width="6" height="4" rx="2"/>
                    </svg>
                </div>
                <span>Avaliação publicada em {{ \Carbon\Carbon::parse($review->created_at)->format('Y-m-d') }}</span>
            </div>
        </div>

        <div class="flex gap-2 pt-2 border-t border-gray-100">
            @for($i = 1; $i <= 5; $i++)
                <svg class="w-8 h-8 {{ $i <= $review->rating ? 'text-teal-600' : 'text-gray-300' }}" viewBox="0 0 24 24" fill="currentColor">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                </svg>
            @endfor
        </div>
    </div>

    <div class="mt-6">
        <h3 class="text-lg font-bold text-gray-800 mb-3">Comentário</h3>
        <div class="bg-gray-50 rounded-lg p-5 border border-gray-200 text-gray-600 text-sm leading-relaxed">
            @if($review->comment)
                {{ $review->comment }}
            @else
                <span class="italic text-gray-400">Sem comentário escrito.</span>
            @endif
        </div>
    </div>

    <div class="mt-6 flex gap-3">
        <a href="{{ route('app.review.edit', $review->id) }}"
           class="flex-1 text-center py-3 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-xl transition-colors">
            Editar
        </a>
        <form action="{{ route('app.review.destroy', $review->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Tem a certeza que deseja eliminar esta avaliação?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="w-full py-3 bg-red-50 hover:bg-red-100 text-red-600 font-semibold rounded-xl transition-colors">
                Eliminar
            </button>
        </form>
    </div>

</div>
@endsection