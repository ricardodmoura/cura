<?php

namespace App\Http\Controllers;

use App\Models\Qualification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class QualificationController extends Controller
{
    /**
     * Devolve o documento da qualificação se o utilizador for o dono ou um admin.
     * Why: cédulas estão em disco privado (storage/app/) — nunca devem ser servidas
     * diretamente por URL pública.
     */
    public function download(Qualification $qualification)
    {
        $user = Auth::user();
        abort_unless(
            $user && ($user->id === $qualification->user_id || $user->isAdmin()),
            403
        );

        if (!$qualification->document || !Storage::disk('local')->exists($qualification->document)) {
            abort(404);
        }

        return Storage::disk('local')->download($qualification->document);
    }
}
