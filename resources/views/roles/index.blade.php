<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestion des rôles') }}
            </h2>
            @if(auth()->user()->hasPermission('create-role'))
                <a href="{{ route('roles.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Nouveau rôle
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                        <tr>
                            <th class="py-2 px-4 border-b text-left">Nom</th>
                            <th class="py-2 px-4 border-b text-left">Description</th>
                            <th class="py-2 px-4 border-b text-left">Utilisateurs</th>
                            <th class="py-2 px-4 border-b text-left">Nécessite validation</th>
                            <th class="py-2 px-4 border-b text-left">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($roles as $role)
                            <tr>
                                <td class="py-2 px-4 border-b">{{ $role->name }}</td>
                                <td class="py-2 px-4 border-b">{{ Str::limit($role->description, 100) }}</td>
                                <td class="py-2 px-4 border-b">{{ $role->users_count }}</td>
                                <td class="py-2 px-4 border-b">
                                    @if($role->requires_validation)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Oui
                                            </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Non
                                            </span>
                                    @endif
                                </td>
                                <td class="py-2 px-4 border-b">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('roles.show', $role) }}" class="text-blue-600 hover:text-blue-900">Voir</a>

                                        @if(auth()->user()->hasPermission('edit-role-permissions'))
                                            <a href="{{ route('roles.edit', $role) }}" class="text-indigo-600 hover:text-indigo-900">Modifier</a>
                                        @endif

                                        @if(auth()->user()->hasPermission('delete-role'))
                                            <form action="{{ route('roles.destroy', $role) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce rôle ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="!text-red-600 hover:text-red-900">Supprimer</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 px-4 border-b text-center text-gray-500">Aucun rôle trouvé</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
