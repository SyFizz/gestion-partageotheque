<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $item->name }}
            </h2>
            <div>
                @if(auth()->user()->hasPermission('edit-item'))
                    <a href="{{ route('items.edit', $item) }}"
                       class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">
                        Modifier
                    </a>
                @endif
                @if(auth()->user()->hasPermission('create-item'))
                    <form action="{{ route('items.duplicate', $item) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Dupliquer
                        </button>
                    </form>
                @endif
                @if(auth()->user()->hasPermission('delete-item'))
                    <form action="{{ route('items.destroy', $item) }}" method="POST" class="inline"
                          onsubmit="return confirm('Êtes-vous sûr de vouloir archiver cet objet?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded ml-2">
                            Archiver
                        </button>
                    </form>
                @endif
                <!-- Bouton de réservation pour les utilisateurs simples -->
                @if(auth()->user()->hasPermission('reserve-item') && !auth()->user()->hasPermission('create-reservation'))
                    <div class="mt-4">
                        <button onclick="toggleReservationForm()"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded w-full">
                            Réserver cet objet
                        </button>

                        <div id="reservationForm" class="mt-4 p-4 bg-gray-50 rounded-lg" style="display: none;">
                            <h4 class="font-medium text-gray-900 mb-2">Réserver "{{ $item->name }}"</h4>

                            <form action="{{ route('items.reserve', $item) }}" method="POST">
                                @csrf

                                <div class="mb-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="type" value="next" class="form-radio" checked
                                               onchange="toggleDateFields(false)">
                                        <span class="ml-2">Réserver dès que l'objet est retourné</span>
                                    </label>
                                </div>

                                <div class="mb-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="type" value="period" class="form-radio"
                                               onchange="toggleDateFields(true)">
                                        <span class="ml-2">Réserver pour une période spécifique</span>
                                    </label>
                                </div>

                                <div id="dateFields" class="grid grid-cols-2 gap-4 mb-4" style="display: none;">
                                    <div>
                                        <label for="start_date" class="block text-sm font-medium text-gray-700">Date de
                                            début</label>
                                        <input type="date" name="start_date" id="start_date"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </div>

                                    <div>
                                        <label for="end_date" class="block text-sm font-medium text-gray-700">Date de
                                            fin</label>
                                        <input type="date" name="end_date" id="end_date"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </div>
                                </div>

                                <div class="flex justify-end">
                                    <button type="button" onclick="toggleReservationForm()"
                                            class="mr-2 text-gray-600 hover:text-gray-900">Annuler
                                    </button>
                                    <button type="submit"
                                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded">
                                        Confirmer la réservation
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <script>
                        function toggleReservationForm() {
                            const form = document.getElementById('reservationForm');
                            form.style.display = form.style.display === 'none' ? 'block' : 'none';
                        }

                        function toggleDateFields(show) {
                            const dateFields = document.getElementById('dateFields');
                            dateFields.style.display = show ? 'grid' : 'none';
                        }
                    </script>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Informations de base -->
                    <div class="md:col-span-2">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informations</h3>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Identifiant</p>
                                <p class="mt-1 text-gray-900">{{ $item->identifier }}</p>
                            </div>

                            <div>
                                <p class="text-sm font-medium text-gray-500">Statut</p>
                                <p class="mt-1">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($item->status->slug == 'in-stock') bg-green-100 text-green-800
                                        @elseif($item->status->slug == 'on-loan') bg-yellow-100 text-yellow-800
                                        @elseif($item->status->slug == 'reserved') bg-blue-100 text-blue-800
                                        @elseif($item->status->slug == 'in-repair') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $item->status->name }}
                                    </span>
                                </p>
                            </div>

                            <div>
                                <p class="text-sm font-medium text-gray-500">Catégorie</p>
                                <p class="mt-1 text-gray-900">{{ $item->category->name }}</p>
                            </div>

                            <div>
                                <p class="text-sm font-medium text-gray-500">Caution</p>
                                <p class="mt-1 text-gray-900">{{ number_format($item->caution_amount, 2) }} €</p>
                            </div>
                        </div>

                        <div class="mb-6">
                            <p class="text-sm font-medium text-gray-500">Description</p>
                            <div class="mt-1 text-gray-900">
                                {{ $item->description ?? 'Aucune description' }}
                            </div>
                        </div>

                        @if($item->notes)
                            <div class="mb-6">
                                <p class="text-sm font-medium text-gray-500">Notes</p>
                                <div class="mt-1 text-gray-900">
                                    {{ $item->notes }}
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Image -->
                    <div>
                        @if($item->image_path)
                            <img src="{{ Storage::url($item->image_path) }}" alt="{{ $item->name }}"
                                 class="w-full rounded-lg shadow-md">
                        @else
                            <div class="bg-gray-200 rounded-lg h-64 flex items-center justify-center">
                                <p class="text-gray-500">Aucune image</p>
                            </div>
                        @endif

                        <!-- Actions -->
                        @if(auth()->user()->hasPermission('create-loan') && $item->status->slug === 'in-stock')
                            <div class="mt-4">
                                <a href="{{ route('loans.create', ['item_id' => $item->id]) }}"
                                   class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-center">
                                    Créer un emprunt
                                </a>
                            </div>
                        @endif

                        @if(auth()->user()->hasPermission('create-reservation'))
                            <div class="mt-2">
                                <a href="{{ route('reservations.create', ['item_id' => $item->id]) }}"
                                   class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center">
                                    Réserver
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Historique -->
                @if(auth()->user()->hasPermission('view-item-history'))
                    <div class="mt-8 border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Historique des emprunts</h3>

                        @if(isset($item->loans) && $item->loans->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white">
                                    <thead>
                                    <tr>
                                        <th class="py-2 px-4 border-b text-left">Emprunteur</th>
                                        <th class="py-2 px-4 border-b text-left">Date d'emprunt</th>
                                        <th class="py-2 px-4 border-b text-left">Date de retour prévue</th>
                                        <th class="py-2 px-4 border-b text-left">Date de retour réelle</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($item->loans as $loan)
                                        <tr>
                                            <td class="py-2 px-4 border-b">{{ $loan->user->name }}</td>
                                            <td class="py-2 px-4 border-b">{{ $loan->loan_date->format('d/m/Y') }}</td>
                                            <td class="py-2 px-4 border-b">{{ $loan->due_date->format('d/m/Y') }}</td>
                                            <td class="py-2 px-4 border-b">
                                                @if($loan->return_date)
                                                    {{ $loan->return_date->format('d/m/Y') }}
                                                @else
                                                    <span class="text-yellow-600">En cours</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500">Aucun emprunt dans l'historique</p>
                        @endif

                        <!-- Réservations en cours -->
                        <h3 class="text-lg font-medium text-gray-900 my-4">Réservations en cours</h3>

                        @if(isset($item->reservations) && $item->reservations->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white">
                                    <thead>
                                    <tr>
                                        <th class="py-2 px-4 border-b text-left">Priorité</th>
                                        <th class="py-2 px-4 border-b text-left">Demandeur</th>
                                        <th class="py-2 px-4 border-b text-left">Date de réservation</th>
                                        <th class="py-2 px-4 border-b text-left">Expiration</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($item->reservations as $reservation)
                                        <tr>
                                            <td class="py-2 px-4 border-b">{{ $reservation->priority_order }}</td>
                                            <td class="py-2 px-4 border-b">{{ $reservation->user->name }}</td>
                                            <td class="py-2 px-4 border-b">{{ $reservation->reservation_date->format('d/m/Y') }}</td>
                                            <td class="py-2 px-4 border-b">{{ $reservation->expiry_date->format('d/m/Y') }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500">Aucune réservation en cours</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
