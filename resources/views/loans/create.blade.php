<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Créer un emprunt') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('loans.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div x-data="setupAutocomplete('user_id', '{{ route('api.users.search') }}')">
                            <label for="user_search" class="block text-sm font-medium text-gray-700">Emprunteur</label>
                            <div class="relative">
                                <input
                                    type="text"
                                    id="user_search"
                                    x-model="search"
                                    @focus="showResults = search.length > 1"
                                    @click.away="showResults = false"
                                    placeholder="Rechercher un utilisateur..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                >
                                <input type="hidden" name="user_id" id="user_id" :value="selectedId">
                                <button type="button" @click="clear" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500" x-show="selectedId">
                                    <i class="fas fa-times"></i>
                                </button>

                                <!-- Dropdown results -->
                                <div
                                    x-show="showResults"
                                    class="absolute z-10 w-full mt-1 bg-white shadow-lg rounded-md py-1 text-sm"
                                    style="display: none;"
                                >
                                    <div x-show="isLoading" class="px-4 py-2 text-gray-500">Chargement...</div>
                                    <div x-show="!isLoading && results.length === 0" class="px-4 py-2 text-gray-500">Aucun résultat trouvé</div>

                                    <template x-for="(result, index) in results" :key="index">
                                        <div
                                            @click="selectOption(result)"
                                            class="px-4 py-2 hover:bg-gray-100 cursor-pointer"
                                            x-text="result.name"
                                        ></div>
                                    </template>
                                </div>
                            </div>
                            @error('user_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-data="setupAutocomplete('item_id', '{{ route('api.items.search') }}')">
                            <label for="item_search" class="block text-sm font-medium text-gray-700">Objet</label>
                            <div class="relative">
                                <input
                                    type="text"
                                    id="item_search"
                                    x-model="search"
                                    @focus="showResults = search.length > 1"
                                    @click.away="showResults = false"
                                    placeholder="Rechercher un objet..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                >
                                <input type="hidden" name="item_id" id="item_id" :value="selectedId">
                                <button type="button" @click="clear" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500" x-show="selectedId">
                                    <i class="fas fa-times"></i>
                                </button>

                                <!-- Dropdown results -->
                                <div
                                    x-show="showResults"
                                    class="absolute z-10 w-full mt-1 bg-white shadow-lg rounded-md py-1 text-sm"
                                    style="display: none;"
                                >
                                    <div x-show="isLoading" class="px-4 py-2 text-gray-500">Chargement...</div>
                                    <div x-show="!isLoading && results.length === 0" class="px-4 py-2 text-gray-500">Aucun résultat trouvé</div>

                                    <template x-for="(result, index) in results" :key="index">
                                        <div
                                            @click="selectOption(result)"
                                            class="px-4 py-2 hover:bg-gray-100 cursor-pointer"
                                            x-text="result.name"
                                        ></div>
                                    </template>
                                </div>
                            </div>
                            @error('item_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="loan_date" class="block text-sm font-medium text-gray-700">Date d'emprunt</label>
                            <input type="date" name="loan_date" id="loan_date" value="{{ old('loan_date', date('Y-m-d')) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('loan_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="due_date" class="block text-sm font-medium text-gray-700">Date de retour prévue</label>
                            <input type="date" name="due_date" id="due_date" value="{{ old('due_date', date('Y-m-d', strtotime('+1 week'))) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('due_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" id="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('notes') }}</textarea>
                        @error('notes')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-6 flex items-center justify-end">
                        <a href="{{ route('loans.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Annuler</a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Créer l'emprunt
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
