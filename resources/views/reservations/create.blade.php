<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Créer une réservation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('reservations.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-autocomplete
                            name="user_id"
                            label="Demandeur"
                            placeholder="Rechercher un utilisateur..."
                            route="{{ route('api.users.search') }}"
                            value="{{ old('user_id') }}"
                            required="true"
                        />

                        <x-autocomplete
                            name="item_id"
                            label="Objet"
                            placeholder="Rechercher un objet..."
                            route="{{ route('api.items.search') }}"
                            value="{{ old('item_id', request('item_id')) }}"
                            required="true"
                            forReservation="true"
                        />

                        <div class="md:col-span-2">
                            <div class="flex items-center mb-4">
                                <input type="radio" id="reservation_type_queue" name="reservation_type" value="queue" class="mr-2" {{ old('reservation_type', 'queue') == 'queue' ? 'checked' : '' }}>
                                <label for="reservation_type_queue" class="text-sm font-medium text-gray-700">Réserver dès que disponible (file d'attente)</label>
                            </div>
                            <div class="flex items-center mb-4">
                                <input type="radio" id="reservation_type_date" name="reservation_type" value="date" class="mr-2" {{ old('reservation_type') == 'date' ? 'checked' : '' }}>
                                <label for="reservation_type_date" class="text-sm font-medium text-gray-700">Réserver pour une date spécifique</label>
                            </div>
                        </div>

                        <div id="date_fields" class="md:col-span-2 {{ old('reservation_type') == 'date' ? '' : 'hidden' }}">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="reservation_date" class="block text-sm font-medium text-gray-700">Date de début</label>
                                    <input type="date" name="reservation_date" id="reservation_date" value="{{ old('reservation_date', date('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @error('reservation_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="expiry_date" class="block text-sm font-medium text-gray-700">Date de fin</label>
                                    <input type="date" name="expiry_date" id="expiry_date" value="{{ old('expiry_date', date('Y-m-d', strtotime('+12 days'))) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @error('expiry_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
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
                        <a href="{{ route('reservations.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Annuler</a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Créer la réservation
                        </button>
                    </div>
                </form>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const queueRadio = document.getElementById('reservation_type_queue');
                        const dateRadio = document.getElementById('reservation_type_date');
                        const dateFields = document.getElementById('date_fields');

                        function toggleDateFields() {
                            dateFields.classList.toggle('hidden', queueRadio.checked);

                            if (queueRadio.checked) {
                                document.getElementById('reservation_date').removeAttribute('required');
                                document.getElementById('expiry_date').removeAttribute('required');
                            } else {
                                document.getElementById('reservation_date').setAttribute('required', 'required');
                                document.getElementById('expiry_date').setAttribute('required', 'required');
                            }
                        }

                        queueRadio.addEventListener('change', toggleDateFields);
                        dateRadio.addEventListener('change', toggleDateFields);

                        // État initial
                        toggleDateFields();
                    });
                </script>
            </div>
        </div>
    </div>
</x-app-layout>
