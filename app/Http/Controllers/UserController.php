<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Role;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');

        if ($request->filled('role')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('roles.id', $request->role);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->paginate(15);
        $roles = Role::all();

        return view('users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'is_validated' => 'boolean',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'is_validated' => $request->is_validated ?? false,
        ]);

        $user->roles()->attach($request->roles);
        ActivityLogger::log('Création', 'Utilisateur', $user->id);

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    public function show(User $user)
    {
        $user->load('roles', 'loans', 'reservations', 'payments');
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'is_validated' => 'boolean',
        ]);
        if ($user->name === "Utilisateur système" && $user->email === "system@partageotheque.local") {
            return to_route('users.index')->with('error', 'Vous ne pouvez pas modifier l\'utilisateur système.');
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'is_validated' => $request->is_validated ?? false,
        ]);

        $user->roles()->sync($request->roles);
        ActivityLogger::log('Modification', 'Utilisateur', $user->id);

        return redirect()->route('users.show', $user)
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }
        if ($user->name === "Utilisateur système" && $user->email === "system@partageotheque.local") {
            return back()->with('error', 'Vous ne pouvez pas supprimer l\'utilisateur système.');
        }
        // Vérifier si l'utilisateur a des emprunts actifs
        if ($user->loans()->whereNull('return_date')->count() > 0) {
            return back()->with('error', 'Impossible de supprimer cet utilisateur car il a des emprunts en cours.');
        }

        // Commencer une transaction pour assurer l'intégrité des données

        try {
            DB::beginTransaction();
            // Récupérer les informations de l'utilisateur avant suppression
            $userName = $user->name;
            $userEmail = $user->email;
            $userId = $user->id;

            // Stocker une trace de la suppression (par l'utilisateur courant)
            ActivityLogger::log('Suppression', 'Utilisateur', $userId, "Nom de l'utilisateur supprimé : {$userName} ({$userEmail})");

            // Trouver ou créer un utilisateur "système"
            $systemUser = User::firstOrCreate(
                ['email' => 'system@partageotheque.local'],
                [
                    'name' => 'Utilisateur système',
                    'password' => Hash::make(Str::random(20)),
                    'is_validated' => true
                ]
            );

            // Mettre à jour les journaux d'activité pour les lier à l'utilisateur système
            // en ajoutant une note pour préserver l'information originale
            ActivityLog::where('user_id', $userId)
                ->update([
                    'user_id' => $systemUser->id,
                    'details' => DB::raw("CONCAT(IFNULL(details, ''), ' [Action originale par: {$userName}]')")
                ]);

            // Modifier les journaux où l'utilisateur apparaît comme créateur/modificateur d'autres entités
            Loan::where('created_by', $userId)->update(['created_by' => $systemUser->id]);
            Loan::where('updated_by', $userId)->update(['updated_by' => $systemUser->id]);

            Reservation::where('created_by', $userId)->update(['created_by' => $systemUser->id]);
            Reservation::where('updated_by', $userId)->update(['updated_by' => $systemUser->id]);

            Payment::where('created_by', $userId)->update(['created_by' => $systemUser->id]);
            Payment::where('updated_by', $userId)->update(['updated_by' => $systemUser->id]);

            // Supprimer les relations et l'utilisateur
            $user->roles()->detach();
            $user->delete();

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'Utilisateur supprimé avec succès.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Une erreur est survenue lors de la suppression de l\'utilisateur: ' . $e->getMessage());
        } catch (\Throwable $e) {
            DB::rollback();
            return back()->with('error', 'Une erreur est survenue lors de la suppression de l\'utilisateur: ' . $e->getMessage());
        }
    }
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        ActivityLogger::log('Réinitialisation de mot de passe', 'Utilisateur', $user->id);

        return redirect()->route('users.show', $user)
            ->with('success', 'Mot de passe réinitialisé avec succès.');
    }
}
