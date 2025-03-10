<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Profil de l\'utilisateur') }}: {{ $user->name }}
            </h2>
            <div>
                @if(auth()->user()->hasPermission('edit-user'))
                    <a href="{{ route('users.edit', $user) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">
                        Modifier
                    </a>
                @endif

                @if(auth()->user()->hasPermission('reset-user-password'))
                    <button onclick="togglePasswordReset()" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                        Réinitialiser le mot de passe
                    </button>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Formulaire de réinitialisation du mot de passe (masqué par défaut) -->
            @if(auth()->user()->hasPermission('reset-user-password'))
                <div id="passwordResetForm" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6" style="display: none;">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Réinitialiser le mot de passe</h3>

                    <form action="{{ route('users.reset-password', $user) }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">Nouveau mot de passe</label>
                                <input type="password" name="password" id="password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmer le mot de passe</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end">
                            <button type="button" onclick="togglePasswordReset()" class="text-gray-600 hover:text-gray-900 mr-4">Annuler</button>
                            <button type="submit" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                Réinitialiser
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Informations de l'utilisateur -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informations personnelles</h3>

                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-500">Nom</p>
                            <p class="mt-1 text-gray-900">{{ $user->name }}</p>
                        </div>

                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-500">Email</p>
                            <p class="mt-1 text-gray-900">{{ $user->email }}</p>
                        </div>

                        @if($user->phone)
                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-500">Téléphone</p>
                                <p class="mt-1 text-gray-900">{{ $user->phone }}</p>
                            </div>
                        @endif

                        @if($user->address)
                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-500">Adresse</p>
                                <p class="mt-1 text-gray-900">{{ $user->address }}</p>
                            </div>
                        @endif

                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-500">Statut</p>
                            <p class="mt-1">
                                @if($user->is_validated)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Actif
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        En attente de validation
                                    </span>
                                @endif
                            </p>
                        </div>

                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-500">Rôles</p>
                            <div class="mt-1 space-y-1">
                                @foreach($user->roles as $role)
                                    <div class="flex items-center">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $role->name }}
                                        </span>
                                        <span class="ml-2 text-xs text-gray-500">{{ $role->description }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Statistiques et activité récente -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Activité</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm font-medium text-gray-500">Emprunts actifs</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $user->loans->whereNull('return_date')->count() }}</p>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm font-medium text-gray-500">Réservations actives</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $user->reservations->where('is_active', true)->count() }}</p>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm font-medium text-gray-500">Total des emprunts</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $user->loans->count() }}</p>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm font-medium text-gray-500">Membre depuis</p>
                                <p class="text-xl font-bold text-gray-900">{{ $user->created_at->format('d/m/Y') }}</p>
                            </div>
                        </div>

                        <!-- Emprunts récents -->
                        <h4 class="font-medium text-gray-700 mb-2">Emprunts récents</h4>
                        @if($user->loans->count() > 0)
                            <ul class="divide-y divide-gray-200">
                                @foreach($user->loans->sortByDesc('created_at')->take(3) as $loan)
                                    <li class="py-2">
                                        <div class="flex justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $loan->item->name }}</p>
                                                <p class="text-xs text-gray-500">
                                                    {{ $loan->loan_date->format('d/m/Y') }} -
                                                    @if($loan->return_date)
                                                        Retourné le {{ $loan->return_date->format('d/m/Y') }}
                                                    @else
                                                        Retour prévu le {{ $loan->due_date->format('d/m/Y') }}
                                                    @endif
                                                </p>
                                            </div>
                                            <a href="{{ route('loans.show', $loan) }}" class="text-blue-600 hover:text-blue-900 text-xs">Détails</a>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-gray-500">Aucun emprunt trouvé.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordReset() {
            const form = document.getElementById('passwordResetForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</x-app-layout>
