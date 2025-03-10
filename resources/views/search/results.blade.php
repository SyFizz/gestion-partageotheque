<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Résultats de recherche pour: ') }} "{{ $query }}"
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Objets') }}</h3>

                    @if($items->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
                            @foreach($items as $item)
                                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <h4 class="font-medium text-blue-600">
                                        <a href="{{ route('items.show', $item) }}">{{ $item->name }}</a>
                                    </h4>
                                    <p class="text-sm text-gray-500">ID: {{ $item->identifier }}</p>
                                    <p class="text-sm mt-2 line-clamp-2">{{ $item->description }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 mb-8">{{ __('Aucun objet trouvé.') }}</p>
                    @endif

                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Catégories') }}</h3>

                    @if($categories->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
                            @foreach($categories as $category)
                                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <h4 class="font-medium text-blue-600">
                                        <a href="{{ route('items.index', ['category' => $category->id]) }}">{{ $category->name }}</a>
                                    </h4>
                                    <p class="text-sm mt-2 line-clamp-2">{{ $category->description }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 mb-8">{{ __('Aucune catégorie trouvée.') }}</p>
                    @endif

                    @if($users)
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Utilisateurs') }}</h3>

                        @if($users->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($users as $user)
                                    <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                        <h4 class="font-medium text-blue-600">
                                            <a href="{{ route('users.show', $user) }}">{{ $user->name }}</a>
                                        </h4>
                                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500">{{ __('Aucun utilisateur trouvé.') }}</p>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
