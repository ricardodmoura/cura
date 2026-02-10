@extends('app.layout.app')

@section('title', 'Notificações')

@section('content')

<div class="max-w-3xl mx-auto">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-4xl font-extrabold text-teal-900 tracking-tight">Notificações</h1>
            <p class="text-teal-600 text-sm mt-1">Todas as suas notificações</p>
        </div>

        @if($notifications->where('read_at', null)->count() > 0)
            <form action="{{ route('app.notifications.readAll') }}" method="POST">
                @csrf
                <button type="submit" class="text-sm font-medium text-teal-600 hover:text-teal-800 bg-teal-50 hover:bg-teal-100 px-4 py-2 rounded-full transition">
                    Marcar todas como lidas
                </button>
            </form>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 rounded-xl bg-green-100 border border-green-200 text-green-800 text-sm flex items-center gap-2 font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if($notifications->isEmpty())
        <div class="text-center py-16 bg-white rounded-3xl shadow-sm border border-gray-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 mx-auto text-teal-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <h3 class="text-teal-900 font-bold text-lg">Sem notificações</h3>
            <p class="text-teal-600 mt-2">Não tem notificações de momento.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($notifications as $notification)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-start gap-4 {{ $notification->read_at ? 'opacity-60' : '' }}">

                    <div class="shrink-0 mt-1">
                        @if(!$notification->read_at)
                            <div class="w-3 h-3 bg-teal-500 rounded-full"></div>
                        @else
                            <div class="w-3 h-3 bg-gray-200 rounded-full"></div>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-semibold text-teal-900 text-sm">
                                {{ $notification->title ?? 'Notificação' }}
                            </h3>
                            <span class="text-xs text-teal-400 whitespace-nowrap">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-sm text-teal-600 mt-1">
                            {{ $notification->description ?? 'Sem descrição' }}
                        </p>

                        @if($notification->action_url)
                            <a href="{{ $notification->action_url }}" class="inline-block mt-2 text-xs font-medium text-teal-600 hover:text-teal-800 transition">
                                Ver detalhes &rarr;
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $notifications->links() }}
        </div>
    @endif

</div>

@endsection
