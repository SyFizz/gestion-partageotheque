<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Catalogue des objets') }}
            </h2>
            @if(auth()->user()->hasPermission('create-item'))
                <a href="{{ route('items.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Nouvel objet
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Filtres -->
                <form action="{{ route('items.index') }}" method="GET" class="mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700">Catégorie</label>
                            <select id="category" name="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Toutes les catégories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Statut</label>
                            <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Tous les statuts</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}" {{ request('status') == $status->id ? 'selected' : '' }}>
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700">Recherche</label>
                            <input type="text" id="search" name="search" value="{{ request('search') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Nom, identifiant ou description">
                        </div>

                        <div class="flex items-end">
                            <button type="submit" class="bg-gray-800 text-white py-2 px-4 rounded hover:bg-gray-700">Filtrer</button>
                            @if(request()->hasAny(['category', 'status', 'search']))
                                <a href="{{ route('items.index') }}" class="ml-2 text-gray-600 hover:text-gray-800">Réinitialiser</a>
                            @endif
                        </div>
                    </div>
                </form>
                <!-- Bouton pour afficher les objets archivés -->
                @if(Auth::user()->hasPermission('view-archived-items'))
                <div class="mt-4 mb-6">
                    @if(request('show_archived'))
                        <a href="{{ route('items.index', array_merge(request()->except('show_archived'), [])) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Voir les objets actifs
                        </a>
                    @else
                        <a href="{{ route('items.index', array_merge(request()->except('show_archived'), ['show_archived' => 1])) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Voir les objets archivés
                        </a>
                    @endif
                </div>
                @endif

                <!-- Liste des objets -->
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                        <tr>
                            <th class="py-2 px-4 border-b text-left">ID</th>
                            <th class="py-2 px-4 border-b text-left">Nom</th>
                            <th class="py-2 px-4 border-b text-left">Catégorie</th>
                            <th class="py-2 px-4 border-b text-left">Statut</th>
                            <th class="py-2 px-4 border-b text-left">Caution</th>
                            <th class="py-2 px-4 border-b text-left">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td class="py-2 px-4 border-b">{{ $item->identifier }}</td>
                                <td class="py-2 px-4 border-b">{{ $item->name }}</td>
                                <td class="py-2 px-4 border-b">{{ $item->category->name }}</td>
                                <td class="py-2 px-4 border-b">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($item->status->slug == 'in-stock') bg-green-100 text-green-800
                                            @elseif($item->status->slug == 'on-loan') bg-yellow-100 text-yellow-800
                                            @elseif($item->status->slug == 'reserved') bg-blue-100 text-blue-800
                                            @elseif($item->status->slug == 'in-repair') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $item->status->name }}
                                        </span>
                                </td>
                                <td class="py-2 px-4 border-b">{{ number_format($item->caution_amount, 2) }} €</td>
                                <td class="py-2 px-4 border-b">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('items.show', $item) }}" class="text-blue-600 hover:text-blue-900">Voir</a>

                                        @if(auth()->user()->hasPermission('edit-item'))
                                            <a href="{{ route('items.edit', $item) }}" class="text-indigo-600 hover:text-indigo-900">Modifier</a>
                                        @endif

                                        @if(auth()->user()->hasPermission('delete-item'))
                                            <form action="{{ route('items.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir archiver cet objet ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="!text-red-600 hover:!text-red-900 ">Archiver</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-4 px-4 border-b text-center text-gray-500">Aucun objet trouvé</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $items->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
