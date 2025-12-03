<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
   
    public function index()
    {
        abort(404);
    }

    
    public function create()
    {
        abort(404);
    }

    public function store(StoreUserRequest $request)
    {
        abort(404);
    }

    /**
     * Exibe o perfil detalhado do utilizador.
     * 
     * Carrega as relações necessárias (MedicalInfo, Qualifications)
     * para exibir todas as informações no perfil.
     * 
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function show(User $user)
    {
        $user->load('medicalInfo', 'qualifications');

        return view('app.user.show', compact('user'));
    }

    /**
     * Exibe o formulário de edição do perfil do utilizador.
     *
     * Carrega as relações necessárias (MedicalInfo, Qualifications)
     * para popular o formulário de edição.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        $user->load('medicalInfo', 'qualifications');

        return view('app.user.edit', compact('user'));
    }

    /**
     * Atualiza os dados do utilizador, perfil e informações médicas.
     *
     * Realiza a atualização em várias tabelas relacionadas.
     * - A password só é atualizada (hash) se o campo não estiver vazio.
     * - A foto de perfil é processada e guardada no storage se for enviada.
     * - Usa 'updateOrCreate' para MedicalInfo e Qualifications para garantir
     * que os registos são criados caso não existam previamente.
     *
     * @param  \App\Http\Requests\UpdateUserRequest  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        // Se a password vier vazia (null), removemos a chave do array para não atualizar
        if (empty($data['user']['password'])) {
            unset($data['user']['password']);
        } else {
            $data['user']['password'] = Hash::make($data['user']['password']);
        }

        $user->update($data['user']);

        // Se houver upload, guardamos o ficheiro e atualizamos o caminho
        if (isset($data['profile']['profile_picture'])) {
            $data['profile']['profile_picture'] = $data['profile']['profile_picture']->store('profiles', 'public');
        } else {
            // Se não houver upload, removemos a chave para não apagar a foto atual com 'null'
            unset($data['profile']['profile_picture']);
        }

        $user->profile()->update($data['profile']);

        if (!empty($data['medical_info'])) {
            $user->medicalInfo()->updateOrCreate(
                ['user_id' => $user->id],
                $data['medical_info']
            );
        }

        if (!empty($data['qualifications'])) {
            if (isset($data['qualifications']['document'])) {
                $data['qualifications']['document'] = $data['qualifications']['document']->store('qualifications', 'public');
            }

            $user->qualifications()->updateOrCreate(
                ['user_id' => $user->id],
                $data['qualifications']
            );
        }

        return redirect()->route('app.user.show', $user)->with('success', 'Perfil atualizado com sucesso.');
    }

    /**
     * Remove a conta do utilizador e todos os dados associados.
     *
     * Implementa uma verificação de segurança para garantir que apenas
     * o próprio utilizador pode apagar a sua conta.
     * Utiliza uma transação para remover manualmente as relações
     * (Profile, MedicalInfo, Qualifications) antes de remover o User,
     * garantindo uma limpeza completa da base de dados.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        DB::transaction(function () use ($user) {
            if ($user->profile()) 
                $user->profile()->delete();
            if ($user->medicalInfo()) 
                $user->medicalInfo()->delete();
            if ($user->qualifications()) 
                $user->qualifications()->delete();
            
            $user->delete();
        });

        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('landing')->with('success', 'A sua conta e todos os dados associados foram apagados.');
    }
}
