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
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        abort(404);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('medicalInfo', 'qualifications');

        return view('app.user.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $user->load('medicalInfo', 'qualifications');

        return view('app.user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
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
