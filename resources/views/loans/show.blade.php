<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détails de l\'emprunt') }}
            </h2>
            <div>
                @if(auth()->user()->hasPermission('edit-loan'))
                    <a href="{{ route('loans.edit', $loan) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">
                        Modifier
                    </a>
                @endif

                @if(!$loan->return_date && auth()->user()->hasPermission('return-loan'))
                    <form action="{{ route('loans.return', $loan) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Retourner
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
                    <!-- Informations de l'emprunt -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informations de l'emprunt</h3>

                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-500">Emprunteur</p>
                            <p class="mt-1 text-gray-900">{{ $loan->user->name }}</p>
                        </div>

                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-500">Objet</p>
                            <p class="mt-1 text-gray-900">
                                <a href="{{ route('items.show', $loan->item) }}" class="text-blue-600 hover:text-blue-900">
                                    {{ $loan->item->name }} ({{ $loan->item->identifier }})
                                </a>
                            </p>
                        </div>

                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-500">Date d'emprunt</p>
                            <p class="mt-1 text-gray-900">{{ $loan->loan_date->format('d/m/Y') }}</p>
                        </div>

                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-500">Date de retour prévue</p>
                            <p class="mt-1 text-gray-900">{{ $loan->due_date->format('d/m/Y') }}</p>
                        </div>

                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-500">Date de retour réelle</p>
                            <p class="mt-1 text-gray-900">
                                @if($loan->return_date)
                                    {{ $loan->return_date->format('d/m/Y') }}
                                @else
                                    <span class="text-yellow-600">En cours</span>
                                @endif
                            </p>
                        </div>

                        @if($loan->notes)
                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-500">Notes</p>
                                <p class="mt-1 text-gray-900">{{ $loan->notes }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Informations additionnelles et actions -->
                    <div>
                        @if(!$loan->return_date && request()->has('extend') && auth()->user()->hasPermission('extend-loan'))
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Prolonger l'emprunt</h3>

                            <form action="{{ route('loans.extend', $loan) }}" method="POST">
                                @csrf

                                <div class="mb-4">
                                    <label for="due_date" class="block text-sm font-medium text-gray-700">Nouvelle date de retour</label>
                                    <input type="date" name="due_date" id="due_date" value="{{ old('due_date', $loan->due_date->addDays(7)->format('Y-m-d')) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @error('due_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <button type="submit" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                                    Prolonger l'emprunt
                                </button>
                            </form>
                        @elseif(!$loan->return_date && auth()->user()->hasPermission('extend-loan'))
                            <div class="mb-6">
                                <a href="{{ route('loans.show', $loan) }}?extend=true" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                                    Prolonger l'emprunt
                                </a>
                            </div>
                        @endif

                        <div class="mt-6">
                            <p class="text-sm font-medium text-gray-500">Statut</p>
                            <p class="mt-1">
                                @if($loan->return_date)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Retourné
                                    </span>
                                @elseif($loan->due_date < now())
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        En retard
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        En cours
                                    </span>
                                @endif
                            </p>
                        </div>

                        <div class="mt-4">
                            <p class="text-sm font-medium text-gray-500">Créé par</p>
                            <p class="mt-1 text-gray-900">{{ $loan->creator->name ?? 'Inconnu' }}</p>
                        </div>

                        @if($loan->updated_by)
                            <div class="mt-4">
                                <p class="text-sm font-medium text-gray-500">Dernière modification par</p>
                                <p class="mt-1 text-gray-900">{{ $loan->updater->name ?? 'Inconnu' }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
