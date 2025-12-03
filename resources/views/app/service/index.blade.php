@extends('app.layout.app')

@section('title', 'Serviços Disponíveis')

@section('content')

<div class="max-w-md mx-auto pb-12">
    
    {{-- ========================================================= --}}
    {{-- SECÇÃO 1: OS MEUS SERVIÇOS ACEITES (PRÓXIMOS TRABALHOS) --}}
    {{-- ========================================================= --}}
    
    @if($myUpcomingServices->isNotEmpty())
        <div class="mb-8 px-2">
            <h1 class="text-3xl font-bold text-teal-900 mb-3">
                <i data-lucide="briefcase" class="w-5 h-5 text-teal-600"></i>
                A minha Agenda
            </h1>

            <div class="space-y-4">
                @foreach($myUpcomingServices as $job)
                    <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden flex flex-row">
                        
                        <div class="p-4 grow">
                            <div class="flex flex-wrap items-center justify-between mb-2">
                                <h3 class="font-bold text-base text-teal-900">{{ $job->service_type }}</h3>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-100 text-teal-700">Confirmado</span>
                            </div>

                            <div class="space-y-1">
                                <p class="text-gray-500 text-xs">
                                    <span class="font-semibold text-teal-700">Data:</span> 
                                    {{ \Carbon\Carbon::parse($job->date)->format('d/m/Y') }} às {{ \Carbon\Carbon::parse($job->time)->format('H:i') }}
                                </p>
                                <p class="text-gray-500 text-xs">
                                    <span class="font-semibold text-teal-700">Utente:</span> 
                                    {{ $job->patient ? $job->patient->name : 'N/D' }}
                                </p>
                                <p class="text-gray-500 text-xs flex items-center gap-1">
                                    <i data-lucide="map-pin" class="w-3 h-3 text-teal-500"></i>
                                    <span class="truncate max-w-[150px]">{{ $job->location }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-col bg-gray-50 w-12 shrink-0 border-l border-gray-100">
                            <a href="{{ route('app.service.show', $job->id) }}"
                               class="flex-1 flex items-center justify-center hover:bg-teal-50 transition-colors group">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-teal-400 group-hover:text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>

                            <a href="#" class="flex-1 flex items-center justify-center hover:bg-red-50 transition-colors group border-t border-gray-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-300 group-hover:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="relative flex py-8 items-center">
                <div class="flex-grow border-t border-teal-600"></div>
                <span class="flex-shrink-0 mx-4 text-teal-600 text-xs uppercase font-bold tracking-wider">Novos Pedidos</span>
                <div class="flex-grow border-t border-teal-600"></div>
            </div>
        </div>
    @endif


    {{-- ========================================================= --}}
    {{-- SECÇÃO 2: MARKETPLACE (SERVIÇOS DISPONÍVEIS) --}}
    {{-- ========================================================= --}}

    <div class="mb-6 px-2">
        <h1 class="text-3xl font-bold text-teal-900">Pool de Serviços</h1>
        <div class="flex justify-between items-end mt-1">
            <p class="text-sm text-teal-600 max-w-[60%] leading-tight">Veja todos os serviços que pode aceitar.</p>
            
            {{-- Filtro Dropdown --}}
            <div class="relative">
                <form action="{{ route('app.service.index') }}" method="GET">
                    <select name="filter" onchange="this.form.submit()" class="appearance-none bg-white border border-teal-100 text-teal-900 text-sm font-bold rounded-full py-2 pl-4 pr-10 focus:outline-none focus:ring-2 focus:ring-teal-500 shadow-sm cursor-pointer hover:bg-teal-50 transition-colors">
                        <option value="">Filtrar</option>
                        <option value="Enfermagem" {{ request('filter') == 'Enfermagem' ? 'selected' : '' }}>Enfermagem</option>
                        <option value="Médica" {{ request('filter') == 'Médica' ? 'selected' : '' }}>Médica</option>
                        <option value="Auxiliar" {{ request('filter') == 'Auxiliar' ? 'selected' : '' }}>Auxiliar</option>
                    </select>
                    <i data-lucide="chevron-down" class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-teal-900 pointer-events-none"></i>
                </form>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="mx-2 mb-4 p-3 rounded-xl bg-green-100 border border-green-200 text-green-800 text-sm flex items-center gap-2 font-medium">
            <i data-lucide="check-circle" class="w-4 h-4"></i> {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mx-2 mb-4 p-3 rounded-xl bg-red-100 border border-red-200 text-red-800 text-sm flex items-center gap-2 font-medium">
            <i data-lucide="alert-circle" class="w-4 h-4"></i> {{ session('error') }}
        </div>
    @endif

    <div class="space-y-4">
        @forelse($services as $service)
            <div class="bg-white rounded-[20px] shadow-sm overflow-hidden flex min-h-[140px]">
                
                <div class="flex-1 p-5 flex flex-col justify-center">
                    <h3 class="font-bold text-teal-900 text-lg mb-3">
                        {{ $service->service_type }} 
                        <span class="font-normal text-teal-600">- Domicílio</span>
                    </h3>

                    <div class="space-y-2 text-sm">
                        <div class="flex items-center">
                            <span class="font-bold text-teal-800 w-16">Data:</span>
                            <span class="text-teal-600">{{ \Carbon\Carbon::parse($service->date)->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex items-center">
                            <span class="font-bold text-teal-800 w-16">Hora:</span>
                            <span class="text-teal-600">{{ \Carbon\Carbon::parse($service->time)->format('H:i') }}</span>
                        </div>
                        <div class="flex items-center">
                            <span class="font-bold text-teal-800 w-16">Utente:</span>
                            <span class="text-teal-600 truncate max-w-[150px]">
                                {{ $service->patient ? $service->patient->name : 'Utilizador #' . $service->patient_id }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="w-20 flex flex-col">
                    
                    <form action="{{ route('app.service.accept', $service->id) }}" method="POST" class="h-1/2 w-full">
                        @csrf
                        <button type="submit" class="w-full h-full bg-[#D1FAE5] hover:bg-green-200 transition-colors flex items-center justify-center group cursor-pointer border-b border-white/50">
                            <div class="w-8 h-8 rounded-full border-2 border-[#10B981] flex items-center justify-center text-[#10B981] bg-transparent group-hover:bg-[#10B981] group-hover:text-white transition-all">
                                <i data-lucide="check" class="w-5 h-5 stroke-[3]"></i>
                            </div>
                        </button>
                    </form>

                    <button type="button" class="h-1/2 w-full bg-[#FEE2E2] hover:bg-red-200 transition-colors flex items-center justify-center group cursor-pointer">
                        <div class="w-8 h-8 rounded-full border-2 border-[#EF4444] flex items-center justify-center text-[#EF4444] bg-transparent group-hover:bg-[#EF4444] group-hover:text-white transition-all">
                             <i data-lucide="ban" class="w-5 h-5 stroke-[3]"></i>
                        </div>
                    </button>
                </div>

            </div>
        @empty
            <div class="text-center py-16 px-4">
                <div class="bg-teal-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="clipboard-list" class="w-10 h-10 text-teal-300"></i>
                </div>
                <h3 class="text-teal-900 font-bold text-lg">Sem novos pedidos</h3>
                <p class="text-teal-600 mt-2">Não existem novos serviços para aceitar de momento.</p>
            </div>
        @endforelse
    </div>
</div>

@endsection