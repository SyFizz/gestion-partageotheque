<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Liste des permissions') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                        <tr>
                            <th class="py-2 px-4 border-b text-left">Nom</th>
                            <th class="py-2 px-4 border-b text-left">Slug</th>
                            <th class="py-2 px-4 border-b text-left">Description</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($permissions as $permission)
                            <tr>
                                <td class="py-2 px-4 border-b">{{ $permission->name }}</td>
                                <td class="py-2 px-4 border-b"><code>{{ $permission->slug }}</code></td>
                                <td class="py-2 px-4 border-b">{{ $permission->description }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-4 px-4 border-b text-center text-gray-500">Aucune permission trouvée</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
