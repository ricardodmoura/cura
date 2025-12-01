@extends('app.layout.app')

@section('title', 'Serviços Disponíveis')

@section('content')

<div class="max-w-md mx-auto"> <div class="mb-6">
        <h1 class="text-2xl font-bold text-teal-900">Serviços</h1>
        <div class="flex justify-between items-end mt-1">
            <p class="text-sm text-teal-600 max-w-[70%]">Veja todos os serviços que pode aceitar.</p>
            
            {{-- Filtro (Dropdown simples como na imagem) --}}
            <div class="relative">
                <select onchange="this.form ? this.form.submit() : null" class="appearance-none bg-white border border-teal-100 text-teal-700 text-sm font-medium rounded-lg py-1.5 pl-3 pr-8 focus:outline-none focus:ring-1 focus:ring-teal-500 shadow-sm cursor-pointer">
                    <option>Filtrar</option>
                    <option>Enfermagem</option>
                    <option>Médico</option>
                </select>
                <i data-lucide="chevron-down" class="absolute right-2 top-1/2 -translate-y-1/2 w-4 h-4 text-teal-500 pointer-events-none"></i>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4"></i> {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm flex items-center gap-2">
            <i data-lucide="x-circle" class="w-4 h-4"></i> {{ session('error') }}
        </div>
    @endif

    <div class="space-y-4">
        @forelse($services as $service)
            <div class="bg-white rounded-2xl shadow-sm border border-teal-50 p-4 flex justify-between items-center relative overflow-hidden">
                
                <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-teal-500"></div>

                <div class="pl-3 space-y-1.5">
                    <h3 class="font-bold text-teal-900 text-sm">
                        {{ $service->service_type }}
                    </h3>

                    <div class="text-xs text-gray-500 space-y-1">
                        <div class="flex items-center gap-1.5">
                            <span class="font-semibold text-gray-700 w-8">Data:</span> 
                            {{ \Carbon\Carbon::parse($service->date)->format('d/m/Y') }}
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="font-semibold text-gray-700 w-8">Hora:</span> 
                            {{ \Carbon\Carbon::parse($service->time)->format('H:i') }}
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="font-semibold text-gray-700 w-8">Utente:</span> 
                            {{-- Nome do paciente (relação patient) --}}
                            {{ $service->patient ? $service->patient->name : 'Utilizador #' . $service->patient_id }}
                        </div>
                         <div class="flex items-center gap-1.5 text-teal-600/80">
                            <i data-lucide="map-pin" class="w-3 h-3"></i> 
                            <span class="truncate max-w-[150px]">{{ $service->location }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-3 ml-2">
                    
                    {{-- Botão Aceitar (Verde) --}}
                    <form action="{{ route('app.service.accept', $service->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600 hover:bg-green-200 transition-colors shadow-sm group">
                            <i data-lucide="check" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                        </button>
                    </form>

                    {{-- Botão Recusar/Ignorar (Vermelho) --}}
                    {{-- Por agora, não faz nada no backend, apenas visual, ou podes fazer um route de 'hide' --}}
                    <button type="button" class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600 hover:bg-red-200 transition-colors shadow-sm group">
                        <i data-lucide="ban" class="w-4 h-4 group-hover:scale-110 transition-transform"></i>
                    </button>
                    
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <div class="bg-teal-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="inbox" class="w-8 h-8 text-teal-400"></i>
                </div>
                <h3 class="text-teal-900 font-medium">Sem serviços disponíveis</h3>
                <p class="text-teal-600 text-sm mt-1">Não há novos pedidos para aceitar no momento.</p>
            </div>
        @endforelse
    </div>
</div>

@endsection