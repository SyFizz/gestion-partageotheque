<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mon espace') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- État de l'adhésion -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Votre statut d'adhérent</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm font-medium text-gray-500">Statut d'adhésion</p>
                        @if($activeMembership)
                            <p class="text-lg font-bold text-green-600">
                                Membre actif
                                @if($activeMembership->expiry_date)
                                    <span class="text-sm font-normal text-gray-500"> jusqu'au {{ $activeMembership->expiry_date->format('d/m/Y') }}</span>
                                @endif
                            </p>
                        @else
                            <p class="text-lg font-bold text-red-600">Cotisation à renouveler</p>
                        @endif
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm font-medium text-gray-500">Caution versée</p>
                        <p class="text-lg font-bold text-blue-600">{{ number_format($totalCaution, 2) }} €</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm font-medium text-gray-500">Membre depuis</p>
                        <p class="text-lg font-bold text-gray-800">
                            @if($firstPayment)
                                {{ $firstPayment->payment_date->format('d/m/Y') }}
                            @else
                                -
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Statistiques personnelles -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <div class="text-sm font-medium text-gray-500">Emprunts actifs</div>
                    <div class="text-3xl font-bold text-blue-600 mt-2">{{ $stats['active_loans_count'] }}</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <div class="text-sm font-medium text-gray-500">Réservations</div>
                    <div class="text-3xl font-bold text-purple-600 mt-2">{{ $stats['reservations_count'] }}</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <div class="text-sm font-medium text-gray-500">Total emprunts</div>
                    <div class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['total_loans_count'] }}</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <div class="text-sm font-medium text-gray-500">Retards</div>
                    <div class="text-3xl font-bold {{ $stats['late_returns_count'] > 0 ? 'text-red-600' : 'text-green-600' }} mt-2">
                        {{ $stats['late_returns_count'] }}
                    </div>
                </div>
            </div>

            <!-- Emprunts en cours et réservations -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Emprunts en cours -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Vos emprunts en cours</h3>

                    @if(count($activeLoans) > 0)
                        <div class="space-y-3">
                            @foreach($activeLoans as $loan)
                                <div class="p-3 {{ $loan->due_date < now() ? 'bg-red-50 border-red-200' : 'bg-blue-50 border-blue-200' }} border rounded-lg">
                                    <div class="flex justify-between">
                                        <div>
                                            <h4 class="font-medium">{{ $loan->item->name }}</h4>
                                            <p class="text-xs text-gray-500">Emprunté le {{ $loan->loan_date->format('d/m/Y') }}</p>
                                        </div>
                                        <div class="text-right">
                                            @if($loan->due_date < now())
                                                <p class="text-sm font-medium text-red-700">
                                                    En retard de {{ now()->diffInDays($loan->due_date) }} jours
                                                </p>
                                            @else
                                                <p class="text-sm font-medium text-blue-700">
                                                    À retourner dans {{ now()->diffInDays($loan->due_date) }} jour(s)
                                                </p>
                                            @endif
                                            <p class="text-xs text-gray-500">Date de retour: {{ $loan->due_date->format('d/m/Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 italic">Vous n'avez aucun emprunt en cours.</p>
                    @endif
                </div>

                <!-- Réservations -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Vos réservations en cours</h3>

                    @if(count($activeReservations) > 0)
                        <div class="space-y-3">
                            @foreach($activeReservations as $reservation)
                                <div class="p-3 bg-purple-50 border border-purple-200 rounded-lg">
                                    <div class="flex justify-between">
                                        <div>
                                            <h4 class="font-medium">{{ $reservation->item->name }}</h4>
                                            <p class="text-xs text-gray-500">Réservé le {{ $reservation->reservation_date->format('d/m/Y') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-purple-700">
                                                @if($reservation->expiry_date < now())
                                                    Expirée
                                                @else
                                                    Expire le {{ $reservation->expiry_date->format('d/m/Y') }}
                                                @endif
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                Position: {{ $reservation->priority_order }}
                                                @if($reservation->priority_order == 1)
                                                    (prochain à emprunter)
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 italic">Vous n'avez aucune réservation en cours.</p>
                    @endif
                </div>
            </div>

            <!-- Historique des emprunts -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Vos derniers emprunts</h3>

                @if(count($recentLoans) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                            <tr>
                                <th class="py-2 px-4 border-b text-left">Objet</th>
                                <th class="py-2 px-4 border-b text-left">Emprunté le</th>
                                <th class="py-2 px-4 border-b text-left">Retourné le</th>
                                <th class="py-2 px-4 border-b text-left">Durée</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($recentLoans as $loan)
                                <tr>
                                    <td class="py-2 px-4 border-b">{{ $loan->item->name }}</td>
                                    <td class="py-2 px-4 border-b">{{ $loan->loan_date->format('d/m/Y') }}</td>
                                    <td class="py-2 px-4 border-b">{{ $loan->return_date->format('d/m/Y') }}</td>
                                    <td class="py-2 px-4 border-b">{{ $loan->loan_date->diffInDays($loan->return_date) }} jours</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 italic">Vous n'avez pas encore d'historique d'emprunts.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
