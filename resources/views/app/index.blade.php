@extends('app.layout.app')

@section('title', 'Dashboard')

@section('content')

    <div class="max-w-7xl mx-auto">

        <!-- Cabeçalho -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 sm:mb-10">
            <div>
                <h1 class="text-3xl font-extrabold text-teal-900 tracking-tight">Dashboard</h1>
                <p class="text-teal-600 mt-2 text-sm sm:text-lg">Bem-vindo de volta, {{ Auth::user()->name }}!</p>
            </div>
            
            @patientOrCompanion
            <a href="{{ route('app.service.create') }}" 
               class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-xl shadow-md transition-colors w-full sm:w-auto text-sm sm:text-base">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Pedir Serviço
            </a>
            @endpatientOrCompanion
        </div>

        <!-- Estatísticas -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-8 sm:mb-12">
            
            <!-- Ativos -->
            <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-md border border-gray-100 flex flex-row items-center gap-3 sm:gap-5">
                <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-teal-100 flex items-center justify-center text-teal-600 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 sm:w-8 sm:h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
                <div>
                    <p class="text-teal-500 font-medium text-xs sm:text-sm">Ativos</p>
                    <p class="text-xl sm:text-3xl font-bold text-teal-900 mt-0.5 sm:mt-1">{{ $stats['active'] }}</p>
                </div>
            </div>

            <!-- Pendentes -->
            <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-md border border-gray-100 flex flex-row items-center gap-3 sm:gap-5">
                <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-teal-100 flex items-center justify-center text-teal-600 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 sm:w-8 sm:h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-teal-500 font-medium text-xs sm:text-sm">Pendentes</p>
                    <p class="text-xl sm:text-3xl font-bold text-teal-900 mt-0.5 sm:mt-1">{{ $stats['pending'] }}</p>
                </div>
            </div>

            <!-- Feitos -->
            <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-md border border-gray-100 flex flex-row items-center gap-3 sm:gap-5">
                <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-teal-100 flex items-center justify-center text-teal-600 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 sm:w-8 sm:h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <p class="text-teal-500 font-medium text-xs sm:text-sm">Feitos</p>
                    <p class="text-xl sm:text-3xl font-bold text-teal-900 mt-0.5 sm:mt-1">{{ $stats['completed'] }}</p>
                </div>
            </div>

            <!-- Avaliação -->
            <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-md border border-gray-100 flex flex-row items-center gap-3 sm:gap-5">
                <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-teal-100 flex items-center justify-center text-teal-600 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 sm:w-8 sm:h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                </div>
                <div>
                    <p class="text-teal-500 font-medium text-xs sm:text-sm">Média</p>
                    <p class="text-xl sm:text-3xl font-bold text-teal-900 mt-0.5 sm:mt-1">4.8</p>
                </div>
            </div>
        </div>

        <!-- Secção Histórico -->
        <div class="bg-white rounded-2xl sm:rounded-3xl shadow-md border border-gray-100 p-4 sm:p-8">
            <div class="flex items-center justify-between mb-6 sm:mb-8">
                <h2 class="text-lg sm:text-2xl font-bold text-teal-900">Histórico de Serviços</h2>
                <a href="{{ route('app.service.index') }}" class="text-xs sm:text-sm font-semibold text-teal-600 hover:text-teal-800 flex items-center gap-1 transition-colors">
                    Ver todos 
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
            </div>

            @if($recentServices->isEmpty())
                <div class="text-center py-10">
                    <p class="text-gray-500">Ainda não existem serviços registados.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($recentServices as $service)
                        <!-- Cartão de Serviço Individual -->
                        <div class="bg-white border border-gray-100 rounded-xl sm:rounded-2xl shadow-md overflow-hidden flex flex-row">
                            
                            <!-- Conteúdo Principal -->
                            <div class="p-4 sm:p-5 grow grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                                <!-- Coluna Esquerda: Título e Status -->
                                <div>
                                    <div class="flex flex-wrap items-center gap-2 sm:gap-3 mb-2">
                                        <h3 class="font-bold text-base sm:text-lg text-teal-900">{{ $service->service_type }}</h3>
                                        <!-- Badge Status -->
                                        @if($service->status === 'Pending')
                                            <span class="px-2 sm:px-3 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-bold bg-yellow-100 text-yellow-700">Pendente</span>
                                        @elseif($service->status === 'Accepted')
                                            <span class="px-2 sm:px-3 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-bold bg-teal-100 text-teal-700">Aceite</span>
                                        @elseif($service->status === 'Completed')
                                            <span class="px-2 sm:px-3 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-bold bg-green-100 text-green-700">Feito</span>
                                        @elseif($service->status === 'Cancelled')
                                            <span class="px-2 sm:px-3 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-bold bg-red-100 text-red-700">Cancelado</span>
                                        @endif
                                    </div>
                                    <p class="text-gray-500 text-xs sm:text-sm mb-0.5">
                                        <span class="font-semibold text-teal-700">Data:</span> 
                                        {{ $service->date ? $service->date->format('d/m/Y') : 'N/D' }}
                                    </p>
                                    <p class="text-gray-500 text-xs sm:text-sm">
                                        <span class="font-semibold text-teal-700">Hora:</span> 
                                        {{ $service->time ? $service->time->format('H:i') : 'N/D' }}
                                    </p>
                                </div>

                                <!-- Coluna Direita: Profissional -->
                                <div class="flex items-center sm:justify-start">
                                    <p class="text-gray-600 text-xs sm:text-sm">
                                        <span class="font-semibold text-teal-700 block mb-0.5 sm:mb-1">Profissional:</span>
                                        @patient
                                            {{ $service->professional ? $service->professional->name : 'A aguardar...' }}
                                        @else
                                            {{ $service->patient ? $service->patient->name : 'Desconhecido' }}
                                        @endpatient
                                    </p>
                                </div>
                            </div>

                            <!-- Barra Lateral de Ações -->
                            <!-- Mantém-se sempre à direita com largura fixa -->
                            <div class="flex flex-col bg-gray-50 w-12 sm:w-14 shrink-0">
                                <!-- Ver Detalhes -->
                                <a href="{{ route('app.service.show', $service->id) }}"
                                class="flex-1 flex items-center justify-center bg-teal-100 hover:bg-teal-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-teal-500" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>

                                <!-- Editar -->
                                <a href="{{ route('app.service.edit', $service->id) }}"
                                class="flex-1 flex items-center justify-center bg-yellow-100 hover:bg-yellow-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-yellow-500" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>

                                <!-- Apagar -->
                                <form action="{{ route('app.service.destroy', $service->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Tem a certeza que deseja apagar este serviço?');"
                                    class="flex-1">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="w-full h-full flex items-center justify-center bg-red-100 hover:bg-red-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-500" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

@endsection