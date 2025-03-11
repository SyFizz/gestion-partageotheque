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
            $user = User::find($request->id);
            if ($user) {
                return response()->json([
                    'id' => $user->id,
                    'name' => $user->name . ' (' . $user->email . ')'
                ]);
            }
            return response()->json(null);
        }

        // Recherche par nom ou email
        $query = $request->get('q', '');
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $users = User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->where('email', '!=', 'system@partageotheque.local')
            ->select('id', 'name', 'email')
            ->limit(10)
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name . ' (' . $user->email . ')'
                ];
            });

        return response()->json($users);
    }
}
