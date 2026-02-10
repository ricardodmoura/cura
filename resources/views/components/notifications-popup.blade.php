<!-- POPUP NOTIFICAÇÕES -->
<div id="notifPopup" class="hidden fixed top-16 right-4 w-80 bg-white border border-gray-200 rounded-lg shadow-lg z-50 max-h-96 overflow-y-auto">
    <div class="p-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-teal-900">Notificações</h3>
    </div>

    @if($notifications && $notifications->count() > 0)
        <div class="divide-y divide-gray-100">
            @foreach($notifications as $notification)
                <div id="notification-{{ $notification->id }}" 
                     class="p-4 hover:bg-teal-50 cursor-pointer transition-colors {{ $notification->read_at ? 'opacity-50' : '' }}"
                     onclick="markAsRead('{{ $notification->id }}')">
                    
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            @if(!$notification->read_at)
                                <div class="w-2 h-2 bg-teal-600 rounded-full mt-2"></div>
                            @endif
                        </div>
                        
                        <div class="flex-1">
                            <p class="text-sm font-medium text-teal-900">
                                {{ $notification->title ?? 'Notificação' }}
                            </p>
                            <p class="text-sm text-teal-600 mt-1">
                                {{ $notification->description ?? 'Sem mensagem' }}
                            </p>
                            <p class="text-xs text-teal-400 mt-1">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="p-3 border-t border-gray-200 text-center">
            <a href="{{ route('app.notifications.index') }}" class="text-sm text-teal-600 hover:text-teal-700 font-medium">
                Ver todas as notificações
            </a>
        </div>
    @else
        <div class="p-8 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto text-teal-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <p class="text-teal-600 text-sm">Sem notificações.</p>
        </div>
    @endif
</div>