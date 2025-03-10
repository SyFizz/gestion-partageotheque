<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestion des utilisateurs') }}
            </h2>
            @if(auth()->user()->hasPermission('create-user'))
                <a href="{{ route('users.create') }}"
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Nouvel utilisateur
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Filtres -->
                <form action="{{ route('users.index') }}" method="GET" class="mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700">Rôle</label>
                            <select id="role" name="role"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Tous les rôles</option>
                                @foreach($roles as $role)
                                    <option
                                        value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700">Recherche</label>
                            <input type="text" id="search" name="search" value="{{ request('search') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   placeholder="Nom ou email">
                        </div>

                        <div class="flex items-end">
                            <button type="submit" class="bg-gray-800 text-white py-2 px-4 rounded hover:bg-gray-700">
                                Filtrer
                            </button>
                            @if(request()->hasAny(['role', 'search']))
                                <a href="{{ route('users.index') }}" class="ml-2 text-gray-600 hover:text-gray-800">Réinitialiser</a>
                            @endif
                        </div>
                    </div>
                </form>

                <!-- Liste des utilisateurs -->
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                        <tr>
                            <th class="py-2 px-4 border-b text-left">Nom</th>
                            <th class="py-2 px-4 border-b text-left">Email</th>
                            <th class="py-2 px-4 border-b text-left">Rôles</th>
                            <th class="py-2 px-4 border-b text-left">Statut</th>
                            <th class="py-2 px-4 border-b text-left">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($users as $user)
                            @if($user->name == "Utilisateur système" && $user->email == "system@partageotheque.local")
                                @continue
                            @endif
                            <tr>
                                <td class="py-2 px-4 border-b">{{ $user->name }}</td>
                                <td class="py-2 px-4 border-b">{{ $user->email }}</td>
                                <td class="py-2 px-4 border-b">
                                    @foreach($user->roles as $role)
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 mr-1">
                                                {{ $role->name }}
                                            </span>
                                    @endforeach
                                </td>
                                <td class="py-2 px-4 border-b">
                                    @if($user->is_validated)
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Actif
                                            </span>
                                    @else
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                En attente
                                            </span>
                                    @endif
                                </td>
                                <td class="py-2 px-4 border-b">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('users.show', $user) }}"
                                           class="text-blue-600 hover:text-blue-900">Voir</a>

                                        @if(auth()->user()->hasPermission('edit-user'))
                                            <a href="{{ route('users.edit', $user) }}"
                                               class="text-indigo-600 hover:text-indigo-900">Modifier</a>
                                        @endif

                                        @if(auth()->user()->hasPermission('delete-user'))
                                            <form action="{{ route('users.destroy', $user) }}" method="POST"
                                                  class="inline"
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="!text-red-600 hover:text-red-900">
                                                    Supprimer
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 px-4 border-b text-center text-gray-500">Aucun utilisateur
                                    trouvé
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
