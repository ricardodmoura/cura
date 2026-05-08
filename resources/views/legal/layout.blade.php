<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Cura</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-teal-50/30 min-h-screen">
    <div class="max-w-3xl mx-auto py-12 px-4">
        <a href="{{ url('/') }}" class="text-teal-700 hover:text-teal-900 text-sm font-semibold">&larr; Voltar</a>

        <h1 class="text-3xl font-bold text-teal-900 mt-6 mb-2">@yield('title')</h1>
        <p class="text-teal-600 text-sm mb-8">Última atualização: @yield('lastUpdated', 'Pendente revisão jurídica')</p>

        <div class="bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800 p-4 rounded mb-8">
            <strong>Em revisão legal.</strong>
            Este documento será disponibilizado pela equipa jurídica antes do lançamento.
            Para questões sobre tratamento de dados, contacte
            <a href="mailto:dpo@cura.pt" class="underline font-semibold">dpo@cura.pt</a>.
        </div>

        <div class="prose prose-teal max-w-none text-teal-900 space-y-6">
            @yield('content')
        </div>
    </div>
</body>
</html>
