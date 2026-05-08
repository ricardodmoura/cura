<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar email - Cura</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-teal-400 to-teal-900 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-md p-8">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-teal-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-teal-900 mb-2">Verifique o seu email</h1>
                <p class="text-teal-600 text-sm">
                    Enviámos um link de confirmação para
                    <strong>{{ auth()->user()->email }}</strong>.
                    Clique nesse link para ativar a conta.
                </p>
            </div>

            @if (session('status'))
                <div class="mb-4 p-3 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}" class="mb-3">
                @csrf
                <button type="submit" class="w-full bg-teal-600 text-white py-3 rounded-xl font-semibold hover:bg-teal-700">
                    Reenviar email de verificação
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-sm text-teal-600 hover:text-teal-800 underline">
                    Terminar sessão
                </button>
            </form>
        </div>
    </div>
</body>
</html>
