@extends('app.layout.app')

@section('title', 'Minhas Avaliações')

@section('content')

<div class="max-w-5xl mx-auto px-4 py-6">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-teal-900 mb-2">Minhas Avaliações</h1>
        <p class="text-teal-500 font-medium">Veja todas as avaliações</p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-5 flex flex-col justify-between h-40">
            <div class="w-12 h-12 rounded-full bg-teal-100 flex items-center justify-center text-teal-600 mb-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
            </div>
            <div>
                <span class="text-xs font-semibold text-teal-500 uppercase tracking-wide">Avaliação Média</span>
                <p class="text-3xl font-bold text-teal-900 mt-1">4.6</p>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-5 flex flex-col justify-between h-40">
            <div class="w-12 h-12 rounded-full bg-teal-100 flex items-center justify-center text-teal-600 mb-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </div>
            <div>
                <span class="text-xs font-semibold text-teal-500 uppercase tracking-wide">Total Avaliações</span>
                <p class="text-3xl font-bold text-teal-900 mt-1">13</p>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-5 flex flex-col justify-between h-40">
            <div class="w-12 h-12 rounded-full bg-teal-100 flex items-center justify-center text-teal-600 mb-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div>
                <span class="text-xs font-semibold text-teal-500 uppercase tracking-wide">Prof. Avaliados</span>
                <p class="text-3xl font-bold text-teal-900 mt-1">27</p>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-5 flex flex-col justify-between h-40">
            <div class="w-12 h-12 rounded-full bg-teal-100 flex items-center justify-center text-teal-600 mb-2 opacity-50">
            </div>
            <div>
                <span class="text-xs font-semibold text-teal-500 uppercase tracking-wide">XXXXXXXXXXX</span>
                <p class="text-3xl font-bold text-teal-900 mt-1">XX</p>
            </div>
        </div>
    </div>

    <div class="flex justify-end mb-6">
        <button class="flex items-center gap-2 bg-white px-5 py-2 rounded-full shadow-sm border border-gray-200 text-teal-900 font-semibold hover:bg-gray-50 transition">
            Filtrar
            <svg class="w-5 h-5 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
    </div>

    <div class="space-y-4">

        {{-- Loop Starts Here --}}
        @foreach($reviews as $review)
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                {{-- 1. LINHA DE TOPO (Sempre visível) --}}
                <div class="flex justify-between items-start mb-4">
                    
                    {{-- Lado Esquerdo: Nome do Serviço e Profissional --}}
                    <div>
                        <h3 class="text-lg font-bold text-teal-900 leading-tight">{{ $review->service_name ?? 'Serviço' }}</h3>
                        <p class="text-sm text-teal-500 mt-1">{{ $review->professional_name ?? 'Profissional' }}</p>
                    </div>

                    {{-- Lado Direito: Lógica do Ícone (Sino ou Estrelas) --}}
                    <div class="flex-shrink-0 ml-4">
                        @if(isset($review->is_pending) && $review->is_pending)
                            {{-- ESTADO: POR AVALIAR (Sino) --}}
                            <div class="flex items-center gap-2 text-teal-800 font-semibold bg-teal-50 px-3 py-1.5 rounded-full">
                                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <span class="text-sm">Por Avaliar</span>
                            </div>
                        @else
                            {{-- ESTADO: AVALIADO (Estrelas Preenchidas Consoante a Nota) --}}
                            <div class="flex gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-6 h-6 text-teal-500" 
                                        fill="{{ $i <= $review->rating ? '#CCFBF1' : 'none' }}" 
                                        stroke="currentColor" 
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                    </svg>
                                @endfor
                            </div>
                        @endif
                    </div>
                </div>

                {{-- 2. CONTEÚDO (Só aparece se NÃO estiver pendente) --}}
                @if(!isset($review->is_pending) || !$review->is_pending)
                    
                    {{-- Comentário --}}
                    <p class="text-teal-700 text-sm leading-relaxed mb-6">
                        {{ $review->comment ?? 'Sem comentário disponível.' }}
                    </p>

                    {{-- Rodapé: Data e Botão --}}
                    <div class="flex justify-between items-center text-sm border-t border-gray-50 pt-4">
                        <span class="text-teal-500 font-medium">{{ $review->date ?? 'N/A' }}</span>
                        
                        <a href="{{ isset($review->id) ? route('app.review.show', $review->id) : '#' }}" 
                        class="text-teal-600 hover:text-teal-800 font-bold flex items-center gap-1 transition-colors group">
                            Ver detalhes 
                            <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                    </div>
                @else
                    {{-- Se estiver pendente, apenas mostra a data discretamente (opcional) --}}
                    <div class="mt-2 text-xs text-teal-400 font-medium">
                        Agendado para: {{ $review->date ?? 'N/A' }}
                    </div>
                @endif

            </div>
        @endforeach
        {{-- Loop Ends --}}

        
        {{-- Exemplo Estático (Atualizado para refletir o seu pedido) --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h3 class="text-lg font-bold text-teal-900">Enfermagem</h3>
                    <p class="text-sm text-teal-500">Silvia Santos</p>
                </div>
                <div class="flex items-center gap-2 text-teal-900 font-semibold">
                    <svg class="w-6 h-6 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span>Por Avaliar</span>
                </div>
            </div>
            
            {{-- Sem descrição e sem botão "ver detalhes" --}}
            <div class="mt-2 text-sm text-teal-500">
                2025-01-20
            </div>
        </div>

    </div>
</div>

@endsection