<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Models\Qualification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QualificationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', Qualification::STATUS_PENDING);

        $qualifications = Qualification::with('user.profile')
            ->where('verification_status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.qualifications.index', compact('qualifications', 'status'));
    }

    public function verify(Qualification $qualification)
    {
        $qualification->update([
            'verification_status' => Qualification::STATUS_VERIFIED,
            'verified_at' => now(),
            'verified_by' => Auth::id(),
            'rejection_reason' => null,
        ]);

        Log::record('qualification.verify', "Qualification #{$qualification->id} (user #{$qualification->user_id})");

        return back()->with('success', 'Qualificação verificada.');
    }

    public function reject(Request $request, Qualification $qualification)
    {
        $data = $request->validate([
            'reason' => 'required|string|min:10|max:500',
        ]);

        $qualification->update([
            'verification_status' => Qualification::STATUS_REJECTED,
            'verified_at' => now(),
            'verified_by' => Auth::id(),
            'rejection_reason' => $data['reason'],
        ]);

        Log::record('qualification.reject', "Qualification #{$qualification->id} (user #{$qualification->user_id}) — {$data['reason']}");

        return back()->with('success', 'Qualificação rejeitada.');
    }
}
