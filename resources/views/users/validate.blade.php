<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Validation des utilisateurs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if($pendingUsers->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                            <tr>
                                <th class="py-2 px-4 border-b text-left">Nom</th>
                                <th class="py-2 px-4 border-b text-left">Email</th>
                                <th class="py-2 px-4 border-b text-left">Rôles</th>
                                <th class="py-2 px-4 border-b text-left">Date d'inscription</th>
                                <th class="py-2 px-4 border-b text-left">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($pendingUsers as $user)
                                <tr>
                                    <td class="py-2 px-4 border-b">{{ $user->name }}</td>
                                    <td class="py-2 px-4 border-b">{{ $user->email }}</td>
                                    <td class="py-2 px-4 border-b">
                                        @foreach($user->roles as $role)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 mr-1">
                                                    {{ $role->name }}
                                                </span>
                                        @endforeach
                                    </td>
                                    <td class="py-2 px-4 border-b">{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="py-2 px-4 border-b">
                                        <div class="flex space-x-2">
                                            <form action="{{ route('users.validate.approve', $user) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                    Valider
                                                </button>
                                            </form>

                                            <form action="{{ route('users.validate.reject', $user) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir rejeter cet utilisateur ? Cette action ne peut pas être annulée.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                    Rejeter
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 text-center">Aucun utilisateur en attente de validation.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
