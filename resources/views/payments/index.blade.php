<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestion des paiements') }}
            </h2>
            @if(auth()->user()->hasPermission('create-payment'))
                <a href="{{ route('payments.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Nouveau paiement
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Filtres -->
                <form action="{{ route('payments.index') }}" method="GET" class="mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="user" class="block text-sm font-medium text-gray-700">Utilisateur</label>
                            <select id="user" name="user" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Tous les utilisateurs</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                            <select id="type" name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Tous les types</option>
                                <option value="membership" {{ request('type') == 'membership' ? 'selected' : '' }}>Cotisation</option>
                                <option value="caution" {{ request('type') == 'caution' ? 'selected' : '' }}>Caution</option>
                                <option value="donation" {{ request('type') == 'donation' ? 'selected' : '' }}>Don</option>
                            </select>
                        </div>

                        <div>
                            <label for="date_from" class="block text-sm font-medium text-gray-700">Date de début</label>
                            <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>

                        <div class="flex items-end">
                            <button type="submit" class="bg-gray-800 text-white py-2 px-4 rounded hover:bg-gray-700">Filtrer</button>
                            @if(request()->hasAny(['user', 'type', 'date_from', 'date_to']))
                                <a href="{{ route('payments.index') }}" class="ml-2 text-gray-600 hover:text-gray-800">Réinitialiser</a>
                            @endif
                        </div>
                    </div>
                </form>

                <!-- Liste des paiements -->
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                        <tr>
                            <th class="py-2 px-4 border-b text-left">Date</th>
                            <th class="py-2 px-4 border-b text-left">Utilisateur</th>
                            <th class="py-2 px-4 border-b text-left">Type</th>
                            <th class="py-2 px-4 border-b text-left">Montant</th>
                            <th class="py-2 px-4 border-b text-left">Expiration</th>
                            <th class="py-2 px-4 border-b text-left">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td class="py-2 px-4 border-b">{{ $payment->payment_date->format('d/m/Y') }}</td>
                                <td class="py-2 px-4 border-b">{{ $payment->user->name }}</td>
                                <td class="py-2 px-4 border-b">
                                    @if($payment->type == 'membership')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Cotisation
                                            </span>
                                    @elseif($payment->type == 'caution')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Caution
                                            </span>
                                    @elseif($payment->type == 'donation')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Don
                                            </span>
                                    @endif
                                </td>
                                <td class="py-2 px-4 border-b">{{ number_format($payment->amount, 2) }} €</td>
                                <td class="py-2 px-4 border-b">
                                    @if($payment->expiry_date)
                                        {{ $payment->expiry_date->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-2 px-4 border-b">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('payments.show', $payment) }}" class="text-blue-600 hover:text-blue-900">Voir</a>

                                        @if(auth()->user()->hasPermission('edit-payment'))
                                            <a href="{{ route('payments.edit', $payment) }}" class="text-indigo-600 hover:text-indigo-900">Modifier</a>
                                        @endif

                                        @if(auth()->user()->hasPermission('delete-payment'))
                                            <form action="{{ route('payments.destroy', $payment) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce paiement ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="!text-red-600 hover:!text-red-900">Supprimer</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-4 px-4 border-b text-center text-gray-500">Aucun paiement trouvé</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
