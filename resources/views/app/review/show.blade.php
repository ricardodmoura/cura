@extends('app.layout.app')

@section('title', 'Detalhes da Avaliação')

@section('content')

<div class="max-w-4xl mx-auto">

    <!-- Back Button -->
    <button onclick="history.back()" class="flex items-center gap-2 text-teal-600 hover:text-teal-700 mb-6 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        Voltar
    </button>

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-teal-900 mb-2">Detalhes da Avaliação</h1>
        <p class="text-teal-600">Avaliação #123</p>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">

        <!-- Conteúdo Principal -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Review Card -->
            <div class="bg-white rounded-xl shadow-md p-6">

                <div class="flex items-start justify-between mb-6">

                    <div class="flex-1">
                        <h2 class="text-xl font-semibold text-teal-900 mb-1">Enfermagem</h2>

                        <div class="flex items-center gap-2 text-teal-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M5.121 17.804A13.937 13.937 0 0112 15c2.507 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Ana Costa</span>
                        </div>

                        <p class="text-sm text-teal-500 mt-1">Enfermagem Geriátrica</p>
                    </div>

                    <!-- Stars -->
                    <div class="flex items-center gap-1">
                        @for ($i = 0; $i < 5; $i++)
                            <svg class="w-6 h-6 fill-yellow-400 text-yellow-400"
                                 viewBox="0 0 24 24">
                                <path
                                    d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2z" />
                            </svg>
                        @endfor
                    </div>
                </div>

                <div class="space-y-4">

                    <div class="flex items-center gap-3 text-sm text-teal-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v11a2 2 0 002 2z" />
                        </svg>
                        <span>Serviço realizado em 2025-01-18</span>
                    </div>

                    <div class="flex items-center gap-3 text-sm text-teal-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 8v4l3 3m6 1a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Avaliação publicada em 2025-01-20</span>
                    </div>

                    <div class="pt-4 border-t border-teal-100">
                        <h3 class="text-sm font-semibold text-teal-900 mb-3">Comentário</h3>

                        <p class="text-teal-700 leading-relaxed">
                            Excelente profissional! Muito atenciosa e cuidadosa com o meu pai.
                            Demonstrou grande conhecimento técnico e empatia. Chegou pontualmente e trouxe
                            todo o material necessário. Recomendo vivamente os seus serviços.
                        </p>
                    </div>

                </div>
            </div>

            <!-- Avaliação Detalhada -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-teal-900 mb-4">Avaliação Detalhada</h3>

                <div class="space-y-4">

                    @foreach ([
                        'Profissionalismo',
                        'Pontualidade',
                        'Comunicação',
                        'Qualidade do Serviço'
                    ] as $categoria)
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-teal-900">{{ $categoria }}</span>

                            <div class="flex gap-1">
                                @for ($i = 0; $i < 5; $i++)
                                    <svg class="w-4 h-4 fill-yellow-400 text-yellow-400"
                                         viewBox="0 0 24 24">
                                        <path
                                            d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2z" />
                                    </svg>
                                @endfor
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">

            <!-- Avaliação Geral -->
            <div class="bg-white rounded-xl shadow-md p-6 text-center">
                <h3 class="text-sm font-medium text-teal-900 mb-3">Avaliação Geral</h3>

                <div class="text-5xl font-bold text-teal-700 mb-2">5.0</div>

                <div class="flex items-center justify-center gap-1 mb-2">
                    @for ($i = 0; $i < 5; $i++)
                        <svg class="w-5 h-5 fill-yellow-400 text-yellow-400"
                             viewBox="0 0 24 24">
                            <path
                                d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2z" />
                        </svg>
                    @endfor
                </div>

                <p class="text-sm text-teal-600">Excelente</p>
            </div>

            <!-- Ações -->
            <div class="bg-white rounded-xl shadow-md p-6 space-y-3">
                <h3 class="text-sm font-medium text-teal-900 mb-3">Ações</h3>

                <a href="#"
                   class="w-full block text-center bg-teal-600 hover:bg-teal-700 text-white font-semibold py-3 rounded-xl transition-colors">
                    Editar Avaliação
                </a>

                <button
                    class="w-full bg-red-50 hover:bg-red-100 text-red-600 font-semibold py-3 rounded-xl transition-colors">
                    Eliminar Avaliação
                </button>

                <button onclick="history.back()"
                    class="w-full bg-teal-50 hover:bg-teal-100 text-teal-700 font-semibold py-3 rounded-xl transition-colors">
                    Voltar às Avaliações
                </button>
            </div>

        </div>
    </div>
</div>

@endsection
