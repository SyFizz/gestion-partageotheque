<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestion des emprunts') }}
            </h2>
            @if(auth()->user()->hasPermission('create-loan'))
                <a href="{{ route('loans.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Nouvel emprunt
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Filtres -->
                <form action="{{ route('loans.index') }}" method="GET" class="mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Statut</label>
                            <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Tous les statuts</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>En cours</option>
                                <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Retournés</option>
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
                                <a href="{{ route('loans.index') }}" class="ml-2 text-gray-600 hover:text-gray-800">Réinitialiser</a>
                            @endif
                        </div>
                    </div>
                </form>

                <!-- Liste des emprunts -->
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                        <tr>
                            <th class="py-2 px-4 border-b text-left">Objet</th>
                            <th class="py-2 px-4 border-b text-left">Emprunteur</th>
                            <th class="py-2 px-4 border-b text-left">Date d'emprunt</th>
                            <th class="py-2 px-4 border-b text-left">Date de retour prévue</th>
                            <th class="py-2 px-4 border-b text-left">Statut</th>
                            <th class="py-2 px-4 border-b text-left">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($loans as $loan)
                            <tr>
                                <td class="py-2 px-4 border-b">{{ $loan->item->name }}</td>
                                <td class="py-2 px-4 border-b">{{ $loan->user->name }}</td>
                                <td class="py-2 px-4 border-b">{{ $loan->loan_date->format('d/m/Y') }}</td>
                                <td class="py-2 px-4 border-b">{{ $loan->due_date->format('d/m/Y') }}</td>
                                <td class="py-2 px-4 border-b">
                                    @if($loan->return_date)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Retourné le {{ $loan->return_date->format('d/m/Y') }}
                                            </span>
                                    @elseif($loan->due_date < now())
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                En retard
                                            </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                En cours
                                            </span>
                                    @endif
                                </td>
                                <td class="py-2 px-4 border-b">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('loans.show', $loan) }}" class="text-blue-600 hover:text-blue-900">Voir</a>

                                        @if(!$loan->return_date && auth()->user()->hasPermission('return-loan'))
                                            <form action="{{ route('loans.return', $loan) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="!text-green-600 hover:!text-green-900">Retourner</button>
                                            </form>
                                        @endif

                                        @if(!$loan->return_date && auth()->user()->hasPermission('extend-loan'))
                                            <a href="{{ route('loans.show', $loan) }}?extend=true" class="text-purple-600 hover:text-purple-900">Prolonger</a>
                                        @endif

                                        @if(auth()->user()->hasPermission('edit-loan'))
                                            <a href="{{ route('loans.edit', $loan) }}" class="text-indigo-600 hover:text-indigo-900">Modifier</a>
                                        @endif

                                        @if(auth()->user()->hasPermission('delete-loan'))
                                            <form action="{{ route('loans.destroy', $loan) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet emprunt ?');">
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
                                <td colspan="6" class="py-4 px-4 border-b text-center text-gray-500">Aucun emprunt trouvé</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $loans->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
