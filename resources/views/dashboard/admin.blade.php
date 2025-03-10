<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tableau de bord') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistiques -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <div class="text-sm font-medium text-gray-500">Total objets</div>
                    <div class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['total_items'] }}</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <div class="text-sm font-medium text-gray-500">Disponibles</div>
                    <div class="text-3xl font-bold text-green-600 mt-2">{{ $stats['available_items'] }}</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <div class="text-sm font-medium text-gray-500">Empruntés</div>
                    <div class="text-3xl font-bold text-blue-600 mt-2">{{ $stats['items_on_loan'] }}</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <div class="text-sm font-medium text-gray-500">Réservations</div>
                    <div class="text-3xl font-bold text-purple-600 mt-2">{{ $stats['active_reservations'] }}</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <div class="text-sm font-medium text-gray-500">Retours en retard</div>
                    <div class="text-3xl font-bold text-red-600 mt-2">{{ $stats['pending_returns'] }}</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <div class="text-sm font-medium text-gray-500">En attente validation</div>
                    <div class="text-3xl font-bold text-yellow-600 mt-2">{{ $stats['pending_validations'] }}</div>
                </div>
            </div>

            <!-- Graphiques -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Nouveaux utilisateurs</h3>
                    <div class="h-64">
                        <canvas id="userChart"></canvas>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Nouveaux emprunts</h3>
                    <div class="h-64">
                        <canvas id="loanChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Graphique d'évolution des emprunts actifs -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Évolution des emprunts actifs (30 derniers jours)</h3>
                <div class="h-64">
                    <canvas id="activeLoanChart"></canvas>
                </div>
            </div>

            <!-- Listes -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Emprunts en retard -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Emprunts en retard</h3>

                    @if(count($overdueLoans) > 0)
                        <div class="space-y-3">
                            @foreach($overdueLoans as $loan)
                                <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                                    <div class="flex justify-between">
                                        <div>
                                            <h4 class="font-medium">{{ $loan->item->name }}</h4>
                                            <p class="text-sm text-gray-500">Emprunté par: {{ $loan->user->name }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-red-700">
                                                En retard de {{ now()->diffInDays($loan->due_date) }} jours
                                            </p>
                                            <p class="text-xs text-gray-500">Date de retour: {{ $loan->due_date->format('d/m/Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if(auth()->user()->hasPermission('create-loan'))
                            <div class="mt-4 text-right">
                                <a href="{{ route('loans.index', ['status' => 'active']) }}" class="text-sm text-blue-600 hover:text-blue-800">
                                    Voir tous les emprunts →
                                </a>
                            </div>
                        @endif
                    @else
                        <p class="text-gray-500 italic">Aucun emprunt en retard.</p>
                    @endif
                </div>

                <!-- Prochains retours -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Prochains retours</h3>

                    @if(count($upcomingReturns) > 0)
                        <div class="space-y-3">
                            @foreach($upcomingReturns as $loan)
                                <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="flex justify-between">
                                        <div>
                                            <h4 class="font-medium">{{ $loan->item->name }}</h4>
                                            <p class="text-sm text-gray-500">Emprunté par: {{ $loan->user->name }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-blue-700">
                                                @if($loan->due_date->isToday())
                                                    Aujourd'hui
                                                @else
                                                    Dans {{ now()->diffInDays($loan->due_date) }} jours
                                                @endif
                                            </p>
                                            <p class="text-xs text-gray-500">Date de retour: {{ $loan->due_date->format('d/m/Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 italic">Aucun retour prévu dans les 7 prochains jours.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Configuration des graphiques
            const userCtx = document.getElementById('userChart').getContext('2d');
            const loanCtx = document.getElementById('loanChart').getContext('2d');
            const activeLoanCtx = document.getElementById('activeLoanChart').getContext('2d');

            // Données des graphiques
            const userChartData = {
                labels: @json($userChartData['labels']),
                datasets: [{
                    label: 'Nouveaux utilisateurs',
                    data: @json($userChartData['data']),
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 2,
                    tension: 0.3
                }]
            };

            const loanChartData = {
                labels: @json($loanChartData['labels']),
                datasets: [{
                    label: 'Nouveaux emprunts',
                    data: @json($loanChartData['data']),
                    backgroundColor: 'rgba(16, 185, 129, 0.2)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 2,
                    tension: 0.3
                }]
            };

            const activeLoanChartData = {
                labels: @json($activeLoanChartData['labels']),
                datasets: [{
                    label: 'Emprunts actifs',
                    data: @json($activeLoanChartData['data']),
                    backgroundColor: 'rgba(139, 92, 246, 0.2)',
                    borderColor: 'rgba(139, 92, 246, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            };

            // Options communes
            const commonOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            };

            // Création des graphiques
            new Chart(userCtx, {
                type: 'line',
                data: userChartData,
                options: commonOptions
            });

            new Chart(loanCtx, {
                type: 'line',
                data: loanChartData,
                options: commonOptions
            });

            new Chart(activeLoanCtx, {
                type: 'line',
                data: activeLoanChartData,
                options: {
                    ...commonOptions,
                    plugins: {
                        legend: {
                            display: true
                        }
                    }
                }
            });
        </script>
    @endpush
</x-app-layout>
