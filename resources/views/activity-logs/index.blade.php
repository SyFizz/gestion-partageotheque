<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Journal d\'activité') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Filtres -->
                <form action="{{ route('activity-logs.index') }}" method="GET" class="mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        @if(auth()->user()->hasPermission('view-all-activity-logs'))
                            <div>
                                <label for="user" class="block text-sm font-medium text-gray-700">Utilisateur</label>
                                <select id="user" name="user" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Tous les utilisateurs</option>
                                    @foreach($users as $user)
                                        @if($user->name == "Utilisateur système")
                                            <option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>
                                                Utilisateur supprimé
                                            </option>
                                        @else
                                            <option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div>
                            <label for="action" class="block text-sm font-medium text-gray-700">Action</label>
                            <select id="action" name="action" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Toutes les actions</option>
                                @foreach($actions as $action)
                                    <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                        {{ $action }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="date_from" class="block text-sm font-medium text-gray-700">Date de début</label>
                            <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>

                        <div>
                            <label for="date_to" class="block text-sm font-medium text-gray-700">Date de fin</label>
                            <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>

                        <div class="flex items-end">
                            <button type="submit" class="bg-gray-800 text-white py-2 px-4 rounded hover:bg-gray-700">Filtrer</button>
                            @if(request()->hasAny(['user', 'action', 'date_from', 'date_to']))
                                <a href="{{ route('activity-logs.index') }}" class="ml-2 text-gray-600 hover:text-gray-800">Réinitialiser</a>
                            @endif
                        </div>
                    </div>
                </form>

                <!-- Liste des activités -->
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                        <tr>
                            <th class="py-2 px-4 border-b text-left">Date</th>
                            <th class="py-2 px-4 border-b text-left">Utilisateur</th>
                            <th class="py-2 px-4 border-b text-left">Action</th>
                            <th class="py-2 px-4 border-b text-left">Élément</th>
                            <th class="py-2 px-4 border-b text-left">Détails</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="py-2 px-4 border-b">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                @if($log->user->name == "Utilisateur système")
                                    <td class="py-2 px-4 border-b">Utilisateur supprimé</td>
                                @else
                                    <td class="py-2 px-4 border-b">{{ $log->user->name }}</td>
                                @endif
                                    <td class="py-2 px-4 border-b">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if(in_array($log->action, ['create', 'store'])) bg-green-100 text-green-800
                                            @elseif(in_array($log->action, ['update', 'edit'])) bg-blue-100 text-blue-800
                                            @elseif(in_array($log->action, ['delete', 'destroy'])) bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $log->action }}
                                        </span>
                                </td>
                                <td class="py-2 px-4 border-b">{{ $log->model_type }}</td>
                                <td class="py-2 px-4 border-b">{{ $log->details }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 px-4 border-b text-center text-gray-500">Aucune activité trouvée</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
