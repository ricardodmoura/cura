@extends('app.layout.app')

@section('title', 'Criar Avaliação')

@section('content')

<div class="max-w-3xl mx-auto">

    <!-- Header -->
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-teal-900 mb-2">Criar Avaliação</h1>
        <p class="text-teal-600">Partilhe a sua experiência e ajude outros utilizadores</p>
    </div>

    <form class="space-y-8">

        <!-- Serviço Avaliado -->
        <section class="bg-white rounded-xl shadow-md p-8">
            <h2 class="text-xl font-semibold text-teal-900 mb-6 border-b border-teal-100 pb-2">
                Serviço Avaliado
            </h2>

            <div class="space-y-6">

                <!-- Profissional -->
                <div>
                    <label class="block text-sm font-medium text-teal-900 mb-2">Profissional <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <select required
                                class="w-full pl-10 pr-4 py-3 border border-teal-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                            <option value="">Selecione o profissional</option>
                            <option>Dr. João Silva - Enfermagem</option>
                            <option>Dra. Maria Santos - Fisioterapia</option>
                            <option>Dr. Pedro Costa - Cuidados Paliativos</option>
                        </select>

                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-teal-400"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5.121 17.804A10.97 10.97 0 0112 15c2.39 0 4.602.746 6.879 2.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Tipo de Serviço -->
                <div>
                    <label class="block text-sm font-medium text-teal-900 mb-2">Tipo de Serviço <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <select required
                                class="w-full pl-10 pr-4 py-3 border border-teal-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                            <option value="">Selecione o tipo de serviço</option>
                            <option>Enfermagem</option>
                            <option>Fisioterapia</option>
                            <option>Cuidados Paliativos</option>
                            <option>Terapia Ocupacional</option>
                        </select>

                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-teal-400"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m2 8H7a2 2 0 01-2-2V6a2 2 0 012-2h4l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>

            </div>
        </section>

        <!-- Avaliação -->
        <section class="bg-white rounded-xl shadow-md p-8">
            <h2 class="text-xl font-semibold text-teal-900 mb-6 border-b border-teal-100 pb-2">
                Avaliação
            </h2>

            <div class="space-y-6">

                <!-- Geral -->
                <div>
                    <label class="block text-sm font-medium text-teal-900 mb-2">
                        Avaliação Geral
                    </label>
                    <div class="flex gap-1 text-2xl">
                        <span class="text-yellow-400">★</span>
                        <span class="text-yellow-400">★</span>
                        <span class="text-yellow-400">★</span>
                        <span class="text-gray-300">★</span>
                        <span class="text-gray-300">★</span>
                    </div>
                </div>

                <!-- Profissionalismo -->
                <div>
                    <label class="block text-sm font-medium text-teal-900 mb-2">
                        Profissionalismo
                    </label>
                    <div class="flex gap-1 text-2xl">
                        <span class="text-yellow-400">★</span>
                        <span class="text-yellow-400">★</span>
                        <span class="text-yellow-400">★</span>
                        <span class="text-yellow-400">★</span>
                        <span class="text-gray-300">★</span>
                    </div>
                </div>

                <!-- Pontualidade -->
                <div>
                    <label class="block text-sm font-medium text-teal-900 mb-2">
                        Pontualidade
                    </label>
                    <div class="flex gap-1 text-2xl">
                        <span class="text-yellow-400">★</span>
                        <span class="text-yellow-400">★</span>
                        <span class="text-gray-300">★</span>
                        <span class="text-gray-300">★</span>
                        <span class="text-gray-300">★</span>
                    </div>
                </div>

                <!-- Qualidade -->
                <div>
                    <label class="block text-sm font-medium text-teal-900 mb-2">
                        Qualidade do Serviço
                    </label>
                    <div class="flex gap-1 text-2xl">
                        <span class="text-yellow-400">★</span>
                        <span class="text-yellow-400">★</span>
                        <span class="text-yellow-400">★</span>
                        <span class="text-gray-300">★</span>
                        <span class="text-gray-300">★</span>
                    </div>
                </div>

            </div>
        </section>

        <!-- Comentário -->
        <section class="bg-white rounded-xl shadow-md p-8">
            <h2 class="text-xl font-semibold text-teal-900 mb-4">
                Comentário
            </h2>

            <label class="block text-sm font-medium text-teal-900 mb-2">Descreva a sua experiência</label>
            <textarea rows="6"
                      placeholder="Partilhe detalhes sobre o serviço, o profissional e a sua experiência geral..."
                      class="w-full px-4 py-3 border border-teal-200 rounded-xl focus:ring-2 focus:ring-teal-500 resize-none"></textarea>

            <p class="text-sm text-teal-600 mt-2">Mínimo de 50 caracteres</p>

            <label class="flex items-center gap-2 mt-4 cursor-pointer">
                <input type="checkbox" class="w-4 h-4 text-teal-600 border-teal-300 rounded focus:ring-teal-500">
                <span class="text-sm text-teal-700">Publicar avaliação anonimamente</span>
            </label>
        </section>

        <!-- Botões -->
        <div class="flex flex-col sm:flex-row gap-4">
            <button type="button"
                    onclick="history.back()"
                    class="flex-1 bg-teal-50 hover:bg-teal-100 text-teal-700 font-semibold py-3 rounded-xl transition-colors">
                Cancelar
            </button>

            <button type="submit"
                    class="flex-1 bg-teal-600 hover:bg-teal-700 text-white font-semibold py-3 rounded-xl transition-colors flex items-center justify-center gap-2">

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5 13l4 4L19 7" />
                </svg>

                Publicar Avaliação
            </button>
        </div>

    </form>

</div>

@endsection
