<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function search(Request $request)
    {
        // Si on recherche un ID spécifique
        if ($request->has('id')) {
            return User::where('id', $request->id)
                ->select('id', 'name', 'email')
                ->get();
        }

        // Recherche par nom ou email
        $query = $request->get('q', '');

        return User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->and('email', 'not like', "system@partageotheque.local")
            ->select('id', 'name', 'email')
            ->limit(10)
            ->get()
            ->map(function($user) {
                $user->name = $user->name . ' (' . $user->email . ')';
                return $user;
            });
    }
}
