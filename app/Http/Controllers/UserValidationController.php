<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserValidated;
use App\Mail\UserRejected;

class UserValidationController extends Controller
{
    public function index()
    {
        $pendingUsers = User::where('is_validated', false)
            ->with('roles')
            ->orderBy('created_at')
            ->get();

        return view('users.validate', compact('pendingUsers'));
    }

    public function validateUser(Request $request, User $user)
    {
        $user->update(['is_validated' => true]);
        ActivityLogger::log('validate', 'User', $user->id);

        // Envoyer email de confirmation
        try {
            Mail::to($user->email)->send(new UserValidated($user));
        } catch (\Exception $e) {
            // Log l'erreur mais continue
        }

        return redirect()->route('users.validate')
            ->with('success', 'Utilisateur validé avec succès.');
    }

    public function reject(Request $request, User $user)
    {
        // Envoyer email de rejet
        try {
            Mail::to($user->email)->send(new UserRejected());
        } catch (\Exception $e) {
            // Log l'erreur mais continue
        }

        $userId = $user->id;
        $user->roles()->detach();
        $user->delete();

        ActivityLogger::log('reject', 'User', $userId);

        return redirect()->route('users.validate')
            ->with('success', 'Utilisateur rejeté et supprimé.');
    }
}
