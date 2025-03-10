<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestion des réservations') }}
            </h2>
            @if(auth()->user()->hasPermission('create-reservation'))
                <a href="{{ route('reservations.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Nouvelle réservation
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Filtres -->
                <form action="{{ route('reservations.index') }}" method="GET" class="mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Statut</label>
                            <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Tous les statuts</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actives</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactives</option>
                            </select>
                        </div>

                        <div>
                            <label for="user" class="block text-sm font-medium text-gray-700">Utilisateur</label>
                            <select id="user" name="user" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Tous les utilisateurs</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700">Recherche</label>
                            <input type="text" id="search" name="search" value="{{ request('search') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Nom de l'objet ou identifiant">
                        </div>

                        <div class="flex items-end">
                            <button type="submit" class="bg-gray-800 text-white py-2 px-4 rounded hover:bg-gray-700">Filtrer</button>
                            @if(request()->hasAny(['status', 'user', 'search']))
                                <a href="{{ route('reservations.index') }}" class="ml-2 text-gray-600 hover:text-gray-800">Réinitialiser</a>
                            @endif
                        </div>
                    </div>
                </form>

                <!-- Liste des réservations -->
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                        <tr>
                            <th class="py-2 px-4 border-b text-left">Objet</th>
                            <th class="py-2 px-4 border-b text-left">Demandeur</th>
                            <th class="py-2 px-4 border-b text-left">Date de réservation</th>
                            <th class="py-2 px-4 border-b text-left">Expiration</th>
                            <th class="py-2 px-4 border-b text-left">Priorité</th>
                            <th class="py-2 px-4 border-b text-left">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($reservations as $reservation)
                            <tr>
                                <td class="py-2 px-4 border-b">{{ $reservation->item->name }}</td>
                                <td class="py-2 px-4 border-b">{{ $reservation->user->name }}</td>
                                <td class="py-2 px-4 border-b">{{ $reservation->reservation_date->format('d/m/Y') }}</td>
                                <td class="py-2 px-4 border-b">{{ $reservation->expiry_date->format('d/m/Y') }}</td>
                                <td class="py-2 px-4 border-b">{{ $reservation->priority_order }}</td>
                                <td class="py-2 px-4 border-b">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('reservations.show', $reservation) }}" class="text-blue-600 hover:text-blue-900">Voir</a>

                                        @if(auth()->user()->hasPermission('edit-reservation'))
                                            <a href="{{ route('reservations.edit', $reservation) }}" class="text-indigo-600 hover:text-indigo-900">Modifier</a>
                                        @endif

                                        @if(auth()->user()->hasPermission('delete-reservation'))
                                            <form action="{{ route('reservations.destroy', $reservation) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette réservation ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="!text-red-600 hover:!text-red-900">Supprimer</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-4 px-4 border-b text-center text-gray-500">Aucune réservation trouvée</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $reservations->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
