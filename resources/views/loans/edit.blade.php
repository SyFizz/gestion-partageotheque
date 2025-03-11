<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier l\'emprunt') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('loans.update', $loan) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-autocomplete
                            name="user_id"
                            label="Emprunteur"
                            placeholder="Rechercher un utilisateur..."
                            route="{{ route('api.users.search') }}"
                            value="{{ old('user_id', $loan->user_id) }}"
                            required="true"
                        />

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Objet</label>
                            <div class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 py-2 px-3">
                                {{ $loan->item->name }} ({{ $loan->item->identifier }})
                            </div>
                            <input type="hidden" name="item_id" value="{{ $loan->item_id }}">
                        </div>

                        <div>
                            <label for="loan_date" class="block text-sm font-medium text-gray-700">Date d'emprunt</label>
                            <input type="date" name="loan_date" id="loan_date" value="{{ old('loan_date', $loan->loan_date->format('Y-m-d')) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('loan_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="due_date" class="block text-sm font-medium text-gray-700">Date de retour prévue</label>
                            <input type="date" name="due_date" id="due_date" value="{{ old('due_date', $loan->due_date->format('Y-m-d')) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('due_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('notes', $loan->notes) }}</textarea>
                        @error('notes')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-6 flex items-center justify-end">
                        <a href="{{ route('loans.show', $loan) }}" class="text-gray-600 hover:text-gray-900 mr-4">Annuler</a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
