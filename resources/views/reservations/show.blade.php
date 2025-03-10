<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détails de la réservation') }}
            </h2>
            <div>
                @if(auth()->user()->hasPermission('edit-reservation'))
                    <a href="{{ route('reservations.edit', $reservation) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">
                        Modifier
                    </a>
                @endif

                @if($reservation->is_active && auth()->user()->hasPermission('delete-reservation'))
                    <form action="{{ route('reservations.destroy', $reservation) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            Annuler
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Informations de la réservation -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informations de la réservation</h3>

                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-500">Demandeur</p>
                            <p class="mt-1 text-gray-900">{{ $reservation->user->name }}</p>
                        </div>

                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-500">Objet</p>
                            <p class="mt-1 text-gray-900">
                                <a href="{{ route('items.show', $reservation->item) }}" class="text-blue-600 hover:text-blue-900">
                                    {{ $reservation->item->name }} ({{ $reservation->item->identifier }})
                                </a>
                            </p>
                        </div>

                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-500">Date de réservation</p>
                            <p class="mt-1 text-gray-900">{{ $reservation->reservation_date->format('d/m/Y') }}</p>
                        </div>

                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-500">Date d'expiration</p>
                            <p class="mt-1 text-gray-900">{{ $reservation->expiry_date->format('d/m/Y') }}</p>
                        </div>

                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-500">Priorité</p>
                            <p class="mt-1 text-gray-900">{{ $reservation->priority_order }}</p>
                        </div>

                        @if($reservation->notes)
                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-500">Notes</p>
                                <p class="mt-1 text-gray-900">{{ $reservation->notes }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Informations additionnelles et actions -->
                    <div>
                        <div class="mb-6">
                            <p class="text-sm font-medium text-gray-500">Statut</p>
                            <p class="mt-1">
                                @if(!$reservation->is_active)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Inactive
                                    </span>
                                @elseif($reservation->expiry_date < now())
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Expirée
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Active
                                    </span>
                                @endif
                            </p>
                        </div>

                        <div class="mt-4">
                            <p class="text-sm font-medium text-gray-500">Créé par</p>
                            <p class="mt-1 text-gray-900">{{ $reservation->creator->name ?? 'Inconnu' }}</p>
                        </div>

                        @if($reservation->updated_by)
                            <div class="mt-4">
                                <p class="text-sm font-medium text-gray-500">Dernière modification par</p>
                                <p class="mt-1 text-gray-900">{{ $reservation->updater->name ?? 'Inconnu' }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
