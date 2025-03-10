<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string',
            'requires_validation' => 'boolean',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'requires_validation' => $request->requires_validation ?? false,
        ]);

        $role->permissions()->attach($request->permissions);
        ActivityLogger::log('Création', 'Rôle', $role->id);

        return redirect()->route('roles.index')
            ->with('success', 'Rôle créé avec succès.');
    }

    public function show(Role $role)
    {
        $role->load('permissions', 'users');
        return view('roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        return view('roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string',
            'requires_validation' => 'boolean',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'requires_validation' => $request->requires_validation ?? false,
        ]);

        $role->permissions()->sync($request->permissions);
        ActivityLogger::log('Modification', 'Rôle', $role->id);

        return redirect()->route('roles.show', $role)
            ->with('success', 'Rôle mis à jour avec succès.');
    }

    public function destroy(Role $role)
    {
        // Vérifier si le rôle a des utilisateurs
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer ce rôle car il est attribué à des utilisateurs.');
        }

        $role->permissions()->detach();
        $role->delete();
        ActivityLogger::log('Suppression', 'Rôle', $role->id);

        return redirect()->route('roles.index')
            ->with('success', 'Rôle supprimé avec succès.');
    }

    public function destroyAndReplace(Role $role, Role $replacement)
    {
        // Récupérer tous les utilisateurs de ce rôle
        $users = $role->users;

        // Assigner le rôle de remplacement à ces utilisateurs
        foreach ($users as $user) {
            $user->roles()->detach($role->id);
            if (!$user->roles->contains($replacement->id)) {
                $user->roles()->attach($replacement->id);
            }
        }

        $role->permissions()->detach();
        $role->delete();
        ActivityLogger::log('Suppression avec remplacement', 'Rôle', $role->id, 'Remplacé par le rôle #' . $replacement->id);

        return redirect()->route('roles.index')
            ->with('success', 'Rôle supprimé et utilisateurs transférés avec succès.');
    }
}
