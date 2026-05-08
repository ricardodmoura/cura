@extends('app.layout.app')

@section('title', 'Admin · Qualificações')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-teal-900">Verificação de Cédulas</h1>
        <p class="text-teal-600 text-sm mt-1">Cruzar cada documento com o registo público da Ordem antes de aprovar.</p>
    </div>

    <div class="flex gap-2 mb-6">
        @foreach (['pending' => 'Pendentes', 'verified' => 'Verificadas', 'rejected' => 'Rejeitadas'] as $key => $label)
            <a href="{{ route('admin.qualifications.index', ['status' => $key]) }}"
               class="px-4 py-2 rounded-full text-sm font-semibold {{ $status === $key ? 'bg-teal-600 text-white' : 'bg-white text-teal-700 border border-teal-100' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm">{{ session('success') }}</div>
    @endif

    @forelse ($qualifications as $q)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-4">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="font-bold text-teal-900">{{ $q->user->name }}</h3>
                    <p class="text-sm text-teal-600">{{ $q->user->email }} · {{ $q->user->profile->user_type ?? '—' }}</p>
                </div>
                <span class="text-xs text-gray-400">{{ $q->created_at->diffForHumans() }}</span>
            </div>

            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm mb-4">
                <div>
                    <dt class="font-semibold text-teal-700">Cédula</dt>
                    <dd class="text-teal-900">{{ $q->cedula_number ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-teal-700">Resumo</dt>
                    <dd class="text-teal-900">{{ $q->description ?? '—' }}</dd>
                </div>
            </dl>

            @if ($q->document)
                <a href="{{ route('app.qualification.document', $q) }}"
                   class="inline-block mb-4 text-sm text-teal-700 underline" target="_blank">
                    Abrir documento submetido
                </a>
            @endif

            @if ($q->verification_status === 'rejected')
                <div class="p-3 rounded bg-red-50 text-red-800 text-sm mb-4">
                    <strong>Rejeitada:</strong> {{ $q->rejection_reason }}
                </div>
            @endif

            @if ($q->verification_status === 'pending')
                <div class="flex flex-col sm:flex-row gap-2">
                    <form method="POST" action="{{ route('admin.qualifications.verify', $q) }}" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full py-2 rounded-xl bg-teal-600 text-white font-semibold hover:bg-teal-700">
                            Verificar
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.qualifications.reject', $q) }}" class="flex-1 flex gap-2">
                        @csrf
                        <input type="text" name="reason" required minlength="10" placeholder="Motivo da rejeição (mín. 10 caracteres)"
                               class="flex-1 px-3 py-2 border border-red-200 rounded-xl text-sm">
                        <button type="submit" class="px-4 py-2 rounded-xl bg-red-50 text-red-700 font-semibold hover:bg-red-100">
                            Rejeitar
                        </button>
                    </form>
                </div>
            @else
                <p class="text-xs text-gray-500">
                    Decidida em {{ $q->verified_at?->format('d/m/Y H:i') }} por #{{ $q->verified_by ?? '—' }}
                </p>
            @endif
        </div>
    @empty
        <div class="bg-white rounded-2xl p-10 text-center text-teal-600">
            Nenhuma qualificação no estado <strong>{{ $status }}</strong>.
        </div>
    @endforelse

    <div class="mt-6">{{ $qualifications->links() }}</div>
</div>
@endsection
