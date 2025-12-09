<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <title>@yield('title', 'Dashboard') - Cura</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-teal-50/30">
@php
    $user = auth()->user();
    $notifications = $user
        ? $user->notifications()->latest()->limit(10)->get()
        : collect();
@endphp
    <!-- NAVBAR -->
    <nav class="fixed top-0 left-0 right-0 bg-white shadow-md z-10">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
            <a href="{{ route('app.index') }}" class="text-2xl font-bold text-teal-700">Cura</a>

            <!-- VERSÃO DESKTOP (escondida em mobile) -->
            <div class="hidden lg:flex items-center space-x-6">
                <a href="{{ route('app.index') }}" class="text-teal-700 hover:text-teal-900 font-medium transition-colors">Dashboard</a>
                <a href="{{ route('app.service.index') }}" class="text-teal-700 hover:text-teal-900 font-medium transition-colors">Serviços</a>
                <a href="{{ route('app.review.index') }}" class="text-teal-700 hover:text-teal-900 font-medium transition-colors">Avaliações</a>

                <!-- NOTIFICAÇÕES DESKTOP -->
                <div class="relative">
                    <button id="notifBtn" class="relative p-2 text-teal-700 hover:text-teal-900 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path d="M12 22c1.1 0 2-.9 2-2H10a2 2 0 002 2zm6-6V9a6 6 0 10-12 0v7l-2 2v1h16v-1l-2-2z"/>
                        </svg>

                        @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                        <span id="notifBadge" 
                            class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                        @endif
                    </button>
                </div>

                <!-- PERFIL -->
                <div class="relative">
                    <button id="profileBtn" class="w-10 h-10 rounded-full overflow-hidden border-2 border-teal-600">
                        @if($user->profile && $user->profile->profile_photo)
                            <img src="{{ asset('storage/' . $user->profile->profile_photo) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-teal-100 flex items-center justify-center text-teal-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        @endif
                    </button>

                    <div id="profileMenu" class="hidden absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg py-2">
                        <a href="{{ route('app.user.show', Auth::user()) }}" class="block px-4 py-2 text-gray-700 hover:bg-teal-50 hover:text-teal-700">Ver Perfil</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="w-full text-left px-4 py-2 text-gray-700 hover:bg-teal-50 hover:text-teal-700">Terminar Sessão</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- VERSÃO MOBILE (visível apenas em mobile) -->
            <div class="flex lg:hidden items-center gap-2">
                <!-- NOTIFICAÇÕES MOBILE -->
                <div class="relative">
                    <button id="notifBtnMobile" class="relative p-2 text-teal-700 hover:text-teal-900 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path d="M12 22c1.1 0 2-.9 2-2H10a2 2 0 002 2zm6-6V9a6 6 0 10-12 0v7l-2 2v1h16v-1l-2-2z"/>
                        </svg>

                        @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                        <span id="notifBadgeMobile" 
                            class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                        @endif
                    </button>
                </div>

                <!-- HAMBURGER MENU -->
                <button id="menuBtn" class="p-2 text-teal-700 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </nav>


    <!-- POPUP NOTIFICAÇÕES -->
    @include('components.notifications-popup', ['notifications' => $notifications ?? []])


    <!-- SIDEBAR MOBILE -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-40 hidden z-20"></div>

    <div id="mobileSidebar" class="fixed top-0 right-0 h-full w-64 bg-white shadow-lg transform translate-x-full transition-transform duration-300 z-30">
        <div class="flex justify-between items-center px-4 py-3 border-b">
            <h2 class="text-xl font-semibold text-teal-700">Menu</h2>
            <button id="closeSidebar" class="text-gray-600 hover:text-teal-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="px-4 py-4 space-y-3">
            <a href="{{ route('app.index') }}" class="block text-gray-700 hover:text-teal-700 font-medium">Dashboard</a>
            <a href="{{ route('app.service.index') }}" class="block text-gray-700 hover:text-teal-700 font-medium">Serviços</a>
            <a href="{{ route('app.review.index') }}" class="block text-gray-700 hover:text-teal-700 font-medium">Avaliações</a>
            <a href="{{ route('app.user.show', Auth::user()) }}" class="block text-gray-700 hover:text-teal-700 font-medium">Perfil</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="block text-left w-full text-gray-700 hover:text-teal-700 font-medium">Terminar Sessão</button>
            </form>
        </div>
    </div>


    <!-- CONTENT -->
    <main class="bg-neutral-50 pt-20 pb-12 px-4 sm:px-6 lg:px-8">
        @yield('content')
    </main>


    <!-- SCRIPTS -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {

        // NOTIFICATIONS DROPDOWN
        const notifBtn = document.getElementById('notifBtn');
        const notifBtnMobile = document.getElementById('notifBtnMobile');
        const notifPopup = document.getElementById('notifPopup');
        
        if (notifBtn && notifPopup) {
            notifBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                notifPopup.classList.toggle('hidden');
            });
        }
        
        if (notifBtnMobile && notifPopup) {
            notifBtnMobile.addEventListener('click', (e) => {
                e.stopPropagation();
                notifPopup.classList.toggle('hidden');
            });
        }
        
        // Fechar popup ao clicar fora
        if (notifPopup) {
            window.addEventListener('click', (e) => {
                if (!notifPopup.contains(e.target) && 
                    !notifBtn?.contains(e.target) && 
                    !notifBtnMobile?.contains(e.target)) {
                    notifPopup.classList.add('hidden');
                }
            });
        }

        // PROFILE DROPDOWN
        const profileBtn = document.getElementById('profileBtn');
        const profileMenu = document.getElementById('profileMenu');
        if (profileBtn && profileMenu) {
            profileBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                profileMenu.classList.toggle('hidden');
            });
            window.addEventListener('click', (e) => {
                if (!profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
                    profileMenu.classList.add('hidden');
                }
            });
        }

        // SIDEBAR MOBILE
        const menuBtn = document.getElementById('menuBtn');
        const mobileSidebar = document.getElementById('mobileSidebar');
        const overlay = document.getElementById('overlay');
        const closeSidebar = document.getElementById('closeSidebar');
        
        if (menuBtn && mobileSidebar && overlay && closeSidebar) {
            menuBtn.addEventListener('click', () => {
                mobileSidebar.classList.remove('translate-x-full');
                overlay.classList.remove('hidden');
            });
            closeSidebar.addEventListener('click', () => {
                mobileSidebar.classList.add('translate-x-full');
                overlay.classList.add('hidden');
            });
            overlay.addEventListener('click', () => {
                mobileSidebar.classList.add('translate-x-full');
                overlay.classList.add('hidden');
            });
        }
    });

    // MARCAR NOTIFICAÇÃO COMO LIDA
    function markAsRead(id) {
        fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Content-Type": "application/json"
            }
        }).then(() => {
            const item = document.getElementById(`notification-${id}`);
            if (item) item.classList.add('opacity-50');

            // Atualizar badge desktop
            const badge = document.getElementById('notifBadge');
            if (badge) {
                let n = parseInt(badge.innerText);
                if (n > 1) {
                    badge.innerText = n - 1;
                } else {
                    badge.remove();
                }
            }
            
            // Atualizar badge mobile
            const badgeMobile = document.getElementById('notifBadgeMobile');
            if (badgeMobile) {
                let n = parseInt(badgeMobile.innerText);
                if (n > 1) {
                    badgeMobile.innerText = n - 1;
                } else {
                    badgeMobile.remove();
                }
            }
        }).catch(err => {
            console.error('Erro ao marcar notificação como lida:', err);
        });
    }
    </script>

</body>
</html>
