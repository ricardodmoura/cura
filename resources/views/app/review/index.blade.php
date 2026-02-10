@extends('app.layout.app')

@section('title', 'Dashboard')

@section('content')

    <div class="max-w-7xl mx-auto">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 sm:mb-10">
            <div>
                <h1 class="text-2xl sm:text-4xl font-extrabold text-teal-900 tracking-tight">Avaliações</h1>
            </div>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-8 sm:mb-12">
            
            <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-md border border-gray-100 flex flex-row items-center gap-3 sm:gap-5">
                <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-teal-100 flex items-center justify-center text-teal-600 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0d9488" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17.286 21.09q -1.69 .001 -5.288 -2.615q -3.596 2.617 -5.288 2.616q -2.726 0 -.495 -6.8q -9.389 -6.775 2.135 -6.775h.076q 1.785 -5.516 3.574 -5.516q 1.785 0 3.574 5.516h.076q 11.525 0 2.133 6.774q 2.23 6.802 -.497 6.8" />
                    </svg>
                </div>
                <div>
                    <p class="text-teal-500 font-medium text-xs sm:text-sm">Avaliação Média</p>
                    <p class="text-xl sm:text-3xl font-bold text-teal-900 mt-0.5 sm:mt-1">{{ $stats['average_rating'] }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-md border border-gray-100 flex flex-row items-center gap-3 sm:gap-5">
                <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-teal-100 flex items-center justify-center text-teal-600 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0d9488" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19.933 13.041a8 8 0 1 1 -9.925 -8.788c3.899 -1 7.935 1.007 9.425 4.747" />
                        <path d="M20 4v5h-5" />
                    </svg>
                </div>
                <div>
                    <p class="text-teal-500 font-medium text-xs sm:text-sm">Total Avaliações</p>
                    <p class="text-xl sm:text-3xl font-bold text-teal-900 mt-0.5 sm:mt-1">{{ $stats['total'] }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-md border border-gray-100 flex flex-row items-center gap-3 sm:gap-5">
                <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-teal-100 flex items-center justify-center text-teal-600 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0d9488" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                    </svg>
                </div>
                <div>
                    <p class="text-teal-500 font-medium text-xs sm:text-sm">Prof. Avaliados</p>
                    <p class="text-xl sm:text-3xl font-bold text-teal-900 mt-0.5 sm:mt-1">{{ $stats['rated_professionals'] }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-md border border-gray-100 flex flex-row items-center gap-3 sm:gap-5">
                <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-teal-100 flex items-center justify-center text-teal-600 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0d9488" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4.5 9.5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                        <path d="M9.5 4.5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                        <path d="M9.5 14.5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                        <path d="M4.5 19.5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                        <path d="M14.5 9.5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                        <path d="M19.5 4.5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                        <path d="M14.5 19.5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                        <path d="M19.5 14.5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                    </svg>
                </div>
                <div>
                    <p class="text-teal-500 font-medium text-xs sm:text-sm">Este Mês</p>
                    <p class="text-xl sm:text-3xl font-bold text-teal-900 mt-0.5 sm:mt-1">{{ $stats['this_month'] }}</p>
                </div>
            </div>
        </div>

        <div class="flex justify-end mb-6">
            <form action="{{ route('app.review.index') }}" method="GET">
                <select name="rating" onchange="this.form.submit()" class="appearance-none bg-white text-gray-700 px-5 py-2.5 pr-10 rounded-full shadow-sm border border-gray-200 hover:bg-gray-50 transition text-sm font-medium cursor-pointer focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <option value="">Todas as Avaliações</option>
                    @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} {{ $i === 1 ? 'Estrela' : 'Estrelas' }}</option>
                    @endfor
                </select>
            </form>
        </div>

        @if($reviews->isEmpty())
            <div class="text-center py-10 bg-white rounded-3xl shadow-sm border border-gray-100">
                <p class="text-gray-500">Ainda não existem avaliações registadas.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($reviews as $review)
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition duration-200">
                    
                    <div class="flex justify-between items-start mb-3">
                        
                        <div>
                            <h3 class="text-lg font-bold text-teal-900 leading-tight">
                                {{ $review->service->service_type ?? 'Enfermagem' }}
                            </h3>
                            <p class="text-teal-500 text-sm font-medium">
                                {{ $review->service->professional->name ?? 'Nome Indisponível' }}
                            </p>
                        </div>

                        <div>
                            @if(is_null($review->rating)) 
                                <a href="{{ route('app.review.create', ['service_id' => $review->service_id]) }}" class="flex items-center gap-2 text-teal-700 font-bold hover:text-teal-900 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                    <span>Por Avaliar</span>
                                </a>
                            @else
                                <div class="flex items-center gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            <svg class="w-5 h-5 text-teal-500 fill-current" viewBox="0 0 24 24">
                                                <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                            </svg>
                                        @endif
                                    @endfor
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-4">
                        <p class="text-gray-500 text-sm leading-relaxed line-clamp-2">
                            {{ $review->comment ?? 'Sem comentário disponível.' }}
                        </p>
                    </div>

                    <div class="flex justify-between items-center pt-2">
                        <span class="text-teal-500 text-sm font-medium">
                            {{ \Carbon\Carbon::parse($review->created_at)->format('Y-m-d') }}
                        </span>

                        @if(!is_null($review->rating))
                        <a href="{{ route('app.review.show', ['review' => $review->id]) }}" class="text-teal-500 text-sm font-medium hover:text-teal-700 flex items-center gap-1 transition">
                            Ver detalhes 
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                        @endif
                    </div>

                </div>
                @endforeach
            </div>
        @endif
        
    </div>

@endsection