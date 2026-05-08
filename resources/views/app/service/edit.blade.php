@extends('app.layout.app')

@section('title', 'Editar Serviço')

@section('content')

<div class="max-w-3xl mx-auto">
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('app.service.index') }}" class="text-teal-600 hover:text-teal-800 transition-colors">
                <i data-lucide="arrow-left" class="w-6 h-6"></i>
            </a>
            <h1 class="text-3xl font-bold text-teal-900">Editar Serviço #{{ $service->id }}</h1>
        </div>
        <p class="text-teal-600">Altere os dados do serviço agendado.</p>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 flex items-center gap-2">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-md p-8">
        {{-- Formulário de Edição --}}
        <form action="{{ route('app.service.update', $service->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-teal-900 mb-2">
                    Tipo de Serviço <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <select id="service_select" 
                            name="service_type" 
                            required 
                            class="w-full pl-4 pr-10 py-3 border border-teal-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent appearance-none">
                        <option value="">Selecione o tipo de serviço</option>
                        
                        @foreach($catalog as $key => $details)
                            <option value="{{ $key }}" 
                                    data-price="{{ $details['price'] }}"
                                    {{ (old('service_type') == $key || (empty(old('service_type')) && $service->service_type == $details['label'])) ? 'selected' : '' }}>
                                {{ $details['label'] }} - €{{ $details['price'] }}/hora
                            </option>
                        @endforeach
                    </select>
                    
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-teal-500">
                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                    </div>
                </div>
                @error('service_type') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-teal-900 mb-2">Data <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i data-lucide="calendar" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-teal-400"></i>
                        <input type="date" 
                               name="date" 
                               value="{{ old('date', $service->date) }}"
                               required 
                               class="w-full pl-11 pr-4 py-3 border border-teal-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                    </div>
                    @error('date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-teal-900 mb-2">Hora <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i data-lucide="clock" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-teal-400"></i>
                        <input type="time" 
                               name="time" 
                               value="{{ old('time', \Carbon\Carbon::parse($service->time)->format('H:i')) }}"
                               required 
                               class="w-full pl-11 pr-4 py-3 border border-teal-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                    </div>
                    @error('time') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-teal-900 mb-2">Localização <span class="text-red-500">*</span></label>
                <div class="relative">
                    <i data-lucide="map-pin" class="absolute left-3 top-3 w-5 h-5 text-teal-400"></i>
                    <textarea name="location" required rows="3" class="w-full pl-11 pr-4 py-3 border border-teal-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent resize-none">{{ old('location', $service->location) }}</textarea>
                </div>
                @error('location') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-teal-900 mb-2">Notas Adicionais</label>
                <div class="relative">
                    <i data-lucide="file-text" class="absolute left-3 top-3 w-5 h-5 text-teal-400"></i>
                    <textarea name="notes" rows="4" class="w-full pl-11 pr-4 py-3 border border-teal-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent resize-none">{{ old('notes', $service->report) }}</textarea>
                </div>
            </div>

            <div class="bg-teal-50 border border-teal-200 rounded-xl p-6 flex justify-between items-center">
                <div>
                     <h3 class="text-lg font-semibold text-teal-900 mb-1">Preço Registado</h3>
                     <p class="text-sm text-teal-600">Por hora.</p>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-bold text-teal-700">
                        <span id="price_display">€{{ number_format($service->price, 2) }}</span>
                    </p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 pt-4">
                <button type="submit" class="flex-1 flex items-center justify-center gap-2 bg-teal-600 hover:bg-teal-700 text-white font-semibold py-3 rounded-xl transition-colors">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    Atualizar Serviço
                </button>
                <a href="{{ route('app.service.index') }}" class="text-center flex-1 bg-teal-50 hover:bg-teal-100 text-teal-700 font-semibold py-3 rounded-xl transition-colors">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('service_select');
        const priceDisplay = document.getElementById('price_display');

        function updatePrice() {
            const option = select.options[select.selectedIndex];
            const price = option.getAttribute('data-price');
            if(price) {
                priceDisplay.textContent = '€' + parseFloat(price).toFixed(2);
            }
        }

        if(select && priceDisplay) {
            select.addEventListener('change', updatePrice);
        }
    });
</script>

@endsection