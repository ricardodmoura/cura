<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página não encontrada - Cura</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-teal-50 min-h-screen flex items-center justify-center px-4">

    <div class="max-w-lg w-full text-center">
        <div class="mb-8 relative">
            <div class="w-32 h-32 bg-teal-100 rounded-full mx-auto flex items-center justify-center animate-pulse">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
        </div>

        <h1 class="text-6xl font-extrabold text-teal-900 tracking-tight mb-4">403</h1>
        <h2 class="text-2xl font-bold text-teal-800 mb-2">Acesso Proibido</h2>
        <p class="text-teal-600 mb-8 max-w-md mx-auto">
            Desculpe! Não tem permissão para aceder a este recurso.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button onclick="history.back()" class="inline-flex items-center justify-center px-6 py-3 border border-teal-200 text-base font-medium rounded-xl text-teal-700 bg-white hover:bg-teal-50 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Voltar
            </button>

            <a href="{{ route('app.index') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-teal-600 hover:bg-teal-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 shadow-lg shadow-teal-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Ir para Dashboard
            </a>
        </div>
    </div>

</body>
</html>