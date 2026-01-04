@extends('app.layout.app')

@section('title', 'Agendar Serviço')

@section('content')

<div class="max-w-3xl mx-auto">
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-teal-900 mb-2">Agendar Serviço</h1>
        <p class="text-teal-600">Preencha os dados para agendar um novo serviço.</p>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-md p-8">
        
        <form action="{{ route('app.service.store') }}" method="POST" class="space-y-6">
            @csrf 
            <div>
                <label class="block text-sm font-medium text-teal-900 mb-2">Tipo de Serviço <span class="text-red-500">*</span></label>
                <div class="relative">
                    <select id="service_select" 
                            name="service_type" 
                            required
                            class="w-full pl-4 pr-10 py-3 border border-teal-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 appearance-none">
                        
                        <option value="">Selecione o serviço</option>

                        {{-- Loop pelo Catálogo do Controller --}}
                        @foreach($catalog as $key => $details)
                            <option value="{{ $key }}" 
                                    data-price="{{ $details['price'] }}"
                                    {{ old('service_type') == $key ? 'selected' : '' }}>
                                {{ $details['label'] }} - €{{ $details['price'] }}/hora
                            </option>
                        @endforeach

                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-teal-500">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </div>
                </div>
                @error('service_type') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-teal-900 mb-2">Data <span class="text-red-500">*</span></label>
                    <input type="date" name="date" required min="{{ date('Y-m-d') }}" value="{{ old('date') }}"
                           class="w-full px-4 py-3 border border-teal-200 rounded-xl focus:ring-2 focus:ring-teal-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-teal-900 mb-2">Hora <span class="text-red-500">*</span></label>
                    <input type="time" name="time" required value="{{ old('time') }}"
                           class="w-full px-4 py-3 border border-teal-200 rounded-xl focus:ring-2 focus:ring-teal-500" />
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-teal-900 mb-2">Localização <span class="text-red-500">*</span></label>
                <textarea name="location" rows="3" required placeholder="Morada completa"
                          class="w-full px-4 py-3 border border-teal-200 rounded-xl focus:ring-2 focus:ring-teal-500 resize-none">{{ old('location') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-teal-900 mb-2">Notas Adicionais</label>
                <textarea name="notes" rows="3" placeholder="Informações extra..."
                          class="w-full px-4 py-3 border border-teal-200 rounded-xl focus:ring-2 focus:ring-teal-500 resize-none">{{ old('notes') }}</textarea>
            </div>

            <div class="bg-teal-50 border border-teal-200 rounded-xl p-6">
                <p class="text-lg font-semibold text-teal-900">Preço Estimado</p>
                <p class="text-3xl font-bold text-teal-700">
                    <span id="price_display">--</span>
                    <span class="text-lg font-normal text-teal-600">/hora</span>
                </p>
            </div>

            <div class="flex gap-4 pt-4">
                <a href="javascript:history.back()" class="flex-1 bg-teal-50 text-teal-700 py-3 rounded-xl hover:bg-teal-100 font-semibold">Cancelar</a>
                <button type="submit" class="flex-1 bg-teal-600 text-white py-3 rounded-xl hover:bg-teal-700 font-semibold">Confirmar</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Script para atualizar o preço visualmente
    const select = document.getElementById('service_select');
    const priceDisplay = document.getElementById('price_display');
    
    function updatePrice() {
        const option = select.options[select.selectedIndex];
        const price = option.getAttribute('data-price');
        priceDisplay.textContent = price ? '€' + price : '--';
    }

    select.addEventListener('change', updatePrice);
    updatePrice(); // Correr ao carregar
</script>

@endsection