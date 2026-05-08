@extends('app.layout.app')

@section('title', 'Detalhes do Serviço')

@section('content')

{{-- Lógica para definir cores e traduções do estado --}}
@php
    $statusColors = match($service->status) {
        'pending' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
        'confirmed', 'accepted' => 'bg-teal-100 text-teal-700 border-teal-200',
        'completed' => 'bg-green-100 text-green-700 border-green-200',
        'canceled' => 'bg-red-100 text-red-700 border-red-200',
        default => 'bg-gray-100 text-gray-700 border-gray-200',
    };

    $statusLabel = match($service->status) {
        'pending' => 'Pendente',
        'confirmed', 'accepted' => 'Confirmado',
        'completed' => 'Concluído',
        'canceled' => 'Cancelado',
        default => ucfirst($service->status),
    };
@endphp

<div class="max-w-4xl mx-auto">
    
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-teal-900 mb-2">Detalhes do Serviço</h1>
            <p class="text-teal-600">Serviço #{{ $service->id }}</p>
        </div>
        <div>
            <span class="px-4 py-2 rounded-xl text-sm font-semibold border {{ $statusColors }}">
                {{ $statusLabel }}
            </span>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 space-y-6">
            
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-xl font-semibold text-teal-900 mb-4">Informações do Serviço</h2>
                <div class="space-y-4">
                    
                    <div class="flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-teal-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M5 8h14M5 4h14a2 2 0 012 2v2H3V6a2 2 0 012-2zM3 10h18v10a2 2 0 01-2 2H5a2 2 0 01-2-2V10z" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-teal-900">Tipo de Serviço</p>
                            <p class="text-teal-600">{{ $service->service_type }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-teal-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10m-11 8h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-teal-900">Data</p>
                            <p class="text-teal-600">{{ \Carbon\Carbon::parse($service->date)->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-teal-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-teal-900">Hora</p>
                            <p class="text-teal-600">{{ \Carbon\Carbon::parse($service->time)->format('H:i') }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-teal-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 .552-.448 1-1 1s-1-.448-1-1a1 1 0 112 0zm0 0a9 9 0 11-6.364 2.636A9 9 0 0112 11z" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-teal-900">Localização</p>
                            <p class="text-teal-600 whitespace-pre-line">{{ $service->location }}</p>
                        </div>
                    </div>

                    @if($service->report)
                        <div class="pt-4 border-t border-teal-100">
                            <p class="text-sm font-medium text-teal-900 mb-2">Notas Adicionais</p>
                            <p class="text-teal-600">{{ $service->report }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-xl font-semibold text-teal-900 mb-4">Profissional Atribuído</h2>
                
                @if($service->professional)
                    <div class="space-y-4">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 bg-teal-100 rounded-full flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 0112 15a9 9 0 016.879 2.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-teal-900">{{ $service->professional->name }}</h3>
                                <p class="text-sm text-teal-600">Profissional de Saúde</p>
                            </div>
                        </div>

                        <div class="space-y-3 pt-4 border-t border-teal-100">
                            {{-- Telefone (Se existir no model User) --}}
                            @if(!empty($service->professional->phone))
                                <div class="flex items-center gap-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h2l3 7v5h8v-5l3-7h2" />
                                    </svg>
                                    <a href="tel:{{ $service->professional->phone }}" class="text-teal-600 hover:text-teal-700">
                                        {{ $service->professional->phone }}
                                    </a>
                                </div>
                            @endif

                            {{-- Email --}}
                            <div class="flex items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12H8m0 0l8 8m-8-8l8-8" />
                                </svg>
                                <a href="mailto:{{ $service->professional->email }}" class="text-teal-600 hover:text-teal-700">
                                    {{ $service->professional->email }}
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Estado Vazio (Sem Profissional) --}}
                    <div class="text-center py-6 bg-teal-50 rounded-xl border border-dashed border-teal-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-teal-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <p class="text-teal-900 font-medium">A aguardar atribuição</p>
                        <p class="text-sm text-teal-600">Iremos notificá-lo assim que um profissional aceitar o seu pedido.</p>
                    </div>
                @endif
            </div>

        </div>

        <div class="space-y-6">
            
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-sm font-medium text-teal-900 mb-2">Valor Total Estimado</h3>
                <p class="text-3xl font-bold text-teal-700">€{{ number_format($service->price, 2) }}</p>
                <p class="text-sm text-teal-600 mt-1">Valor por hora</p>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6 space-y-3">
                <h3 class="text-sm font-medium text-teal-900 mb-3">Ações</h3>
                
                @if($service->canBeRescheduled())
                    <a href="{{ route('app.service.edit', $service->id) }}"
                        class="w-full flex items-center justify-center gap-2 bg-teal-600 hover:bg-teal-700 text-white font-semibold py-3 rounded-xl transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12l4-4m0 0l-4-4m4 4H9m13 8h-6m0 0l4-4m-4 4l4 4" />
                        </svg>
                        Editar
                    </a>
                @endif

                {{-- Profissional atribuído pode marcar como concluído após a hora do serviço. --}}
                @if($service->professional_id === auth()->id() && in_array($service->status, ['confirmed', 'accepted', 'in_progress']) && !$service->dateTime->isFuture())
                    <form action="{{ route('app.service.complete', $service->id) }}" method="POST" onsubmit="return confirm('Marcar serviço como concluído?');">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-xl transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Marcar como concluído
                        </button>
                    </form>
                @endif

                @if($service->canBeCancelled())
                    <form action="{{ route('app.service.destroy', $service->id) }}" method="POST" onsubmit="return confirm('Tem a certeza que deseja cancelar este serviço?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full flex items-center justify-center gap-2 bg-red-50 hover:bg-red-100 text-red-600 font-semibold py-3 rounded-xl transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-9 0h10" />
                            </svg>
                            Cancelar Serviço
                        </button>
                    </form>
                @endif

                {{-- Exportar para calendário (.ics) --}}
                <a href="{{ route('app.service.ics', $service->id) }}"
                   class="block w-full text-center bg-teal-50 hover:bg-teal-100 text-teal-700 font-semibold py-3 rounded-xl transition-colors">
                    Adicionar ao calendário (.ics)
                </a>

                <a href="javascript:history.back()"
                   class="block w-full text-center bg-teal-50 hover:bg-teal-100 text-teal-700 font-semibold py-3 rounded-xl transition-colors">
                    Voltar
                </a>
            </div>

        </div>
    </div>
</div>

@endsection