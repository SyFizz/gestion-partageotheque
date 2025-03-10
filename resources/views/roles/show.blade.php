<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détails du rôle') }}: {{ $role->name }}
            </h2>
            <div>
                @if(auth()->user()->hasPermission('edit-role-permissions'))
                    <a href="{{ route('roles.edit', $role) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Modifier
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Informations</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Nom</p>
                            <p class="mt-1 text-gray-900">{{ $role->name }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Slug</p>
                            <p class="mt-1 text-gray-900">{{ $role->slug }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Validation requise</p>
                            <p class="mt-1">
                                @if($role->requires_validation)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Oui
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Non
                                    </span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Nombre d'utilisateurs</p>
                            <p class="mt-1 text-gray-900">{{ $role->users->count() }}</p>
                        </div>
                    </div>

                    @if($role->description)
                        <div class="mt-4">
                            <p class="text-sm font-medium text-gray-500">Description</p>
                            <p class="mt-1 text-gray-900">{{ $role->description }}</p>
                        </div>
                    @endif
                </div>

                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Permissions</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                        @forelse($role->permissions as $permission)
                            <div class="p-2 bg-gray-50 rounded">
                                <span class="font-medium">{{ $permission->name }}</span>
                                @if($permission->description)
                                    <p class="text-xs text-gray-500 mt-1">{{ $permission->description }}</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-gray-500">Aucune permission attribuée.</p>
                        @endforelse
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Utilisateurs avec ce rôle</h3>

                    @if($role->users->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Nom</th>
                                    <th class="py-2 px-4 border-b text-left">Email</th>
                                    <th class="py-2 px-4 border-b text-left">Statut</th>
                                    <th class="py-2 px-4 border-b text-left">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($role->users as $user)
                                    <tr>
                                        <td class="py-2 px-4 border-b">{{ $user->name }}</td>
                                        <td class="py-2 px-4 border-b">{{ $user->email }}</td>
                                        <td class="py-2 px-4 border-b">
                                            @if($user->is_validated)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Actif
                                                    </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        En attente
                                                    </span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            <a href="{{ route('users.show', $user) }}" class="text-blue-600 hover:text-blue-900">Voir</a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">Aucun utilisateur n'a ce rôle.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
