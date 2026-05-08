<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir palavra-passe - Cura</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-teal-400 to-teal-900 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-md p-8">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-teal-900 mb-2">Definir nova palavra-passe</h1>
                <p class="text-teal-600 text-sm">Escolha uma nova palavra-passe segura.</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label for="email" class="block text-sm font-medium text-teal-900 mb-2">Email</label>
                    <input type="email" id="email" name="email" required value="{{ old('email', $email) }}"
                           class="w-full px-4 py-3 border border-teal-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-teal-900 mb-2">Nova palavra-passe</label>
                    <input type="password" id="password" name="password" required minlength="8" placeholder="Mín. 8 caracteres"
                           class="w-full px-4 py-3 border border-teal-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-teal-900 mb-2">Confirmar palavra-passe</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                           class="w-full px-4 py-3 border border-teal-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>

                <button type="submit" class="w-full bg-teal-600 text-white py-3 rounded-xl font-semibold hover:bg-teal-700 shadow-md">
                    Redefinir
                </button>
            </form>
        </div>
    </div>
</body>
</html>
