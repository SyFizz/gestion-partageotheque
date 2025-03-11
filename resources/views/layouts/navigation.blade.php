<div x-data="{ sidebarOpen: false, userMenuOpen: false, adminMenuOpen: false }">
    <!-- Sidebar for desktop -->
    <div class="hidden md:flex md:flex-col md:fixed md:inset-y-0 md:z-10 md:w-64 bg-gradient-to-b from-blue-800 to-blue-900 text-white">
        <!-- Logo -->
        <div class="flex items-center justify-center h-16 px-4 bg-blue-900">
            <a href="{{ route('dashboard') }}" class="text-xl font-bold tracking-wide">
                <span class="text-white">La Partageothèque</span>
            </a>
        </div>
        <!-- Navigation -->
        <div class="flex-1 flex flex-col overflow-y-auto">
            <nav class="flex-1 px-2 py-4 space-y-2">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'bg-blue-700' : 'hover:bg-blue-700' }} text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                    <i class="fas fa-home mr-3 text-blue-300"></i>
                    Tableau de bord
                </a>

                @if(Auth::user()->hasPermission('view-catalog'))
                    <a href="{{ route('items.index') }}" class="{{ request()->routeIs('items.*') ? 'bg-blue-700' : 'hover:bg-blue-700' }} text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                        <i class="fas fa-box-open mr-3 text-blue-300"></i>
                        Catalogue
                    </a>
                @endif

                @if(Auth::user()->hasPermission('create-loan') || Auth::user()->hasPermission('return-loan'))
                    <a href="{{ route('loans.index') }}" class="{{ request()->routeIs('loans.*') ? 'bg-blue-700' : 'hover:bg-blue-700' }} text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                        <i class="fas fa-handshake mr-3 text-blue-300"></i>
                        Emprunts
                    </a>
                @endif

                @if(Auth::user()->hasPermission('create-reservation') || Auth::user()->hasPermission('edit-reservation'))
                    <a href="{{ route('reservations.index') }}" class="{{ request()->routeIs('reservations.*') ? 'bg-blue-700' : 'hover:bg-blue-700' }} text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                        <i class="fas fa-calendar-alt mr-3 text-blue-300"></i>
                        Réservations
                    </a>
                @endif

                <!-- Accès rapide -->
                @if(Auth::user()->hasPermission('create-loan') || Auth::user()->hasPermission('return-loan') ||
                    Auth::user()->hasPermission('reserve-item') || Auth::user()->hasPermission('create-payment') ||
                    Auth::user()->hasPermission('create-user'))
                    <div x-data="{ open: false }">
                        <button @click="open = !open" class="w-full text-white hover:bg-blue-700 group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-bolt mr-3 text-blue-300"></i>
                            <span class="flex-1 text-left">Accès rapide</span>
                            <i class="fas" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                        </button>
                        <div x-show="open" class="pl-6 mt-1 space-y-1">
                            @if(Auth::user()->hasPermission('create-loan'))
                                <a href="{{ route('loans.create') }}" class="text-white hover:bg-blue-700 group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                                    <i class="fas fa-plus-circle mr-3 text-blue-300"></i>
                                    Déclarer un emprunt
                                </a>
                            @endif

                            @if(Auth::user()->hasPermission('reserve-item'))
                                <a href="{{ route('reservations.create') }}" class="text-white hover:bg-blue-700 group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                                    <i class="fas fa-calendar-plus mr-3 text-blue-300"></i>
                                    Réserver un objet
                                </a>
                            @endif

                            @if(Auth::user()->hasPermission('return-loan'))
                                <a href="{{ route('loans.index', ['status' => 'active']) }}" class="text-white hover:bg-blue-700 group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                                    <i class="fas fa-undo-alt mr-3 text-blue-300"></i>
                                    Déclarer un retour
                                </a>
                            @endif

                            @if(Auth::user()->hasPermission('create-payment'))
                                <a href="{{ route('payments.create') }}" class="text-white hover:bg-blue-700 group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                                    <i class="fas fa-money-bill mr-3 text-blue-300"></i>
                                    Enregistrer un paiement
                                </a>
                            @endif

                            @if(Auth::user()->hasPermission('create-user'))
                                <a href="{{ route('users.create') }}" class="text-white hover:bg-blue-700 group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                                    <i class="fas fa-user-plus mr-3 text-blue-300"></i>
                                    Créer un utilisateur
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Administration -->
                @if(Auth::user()->hasPermission('edit-item') ||
                    Auth::user()->hasPermission('create-user') ||
                    Auth::user()->hasPermission('edit-role-permissions') ||
                    Auth::user()->hasPermission('view-all-activity-logs'))
                    <div x-data="{ open: false }">
                        <button @click="open = !open" class="w-full text-white hover:bg-blue-700 group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-cogs mr-3 text-blue-300"></i>
                            <span class="flex-1 text-left">Administration</span>
                            <i class="fas" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                        </button>
                        <div x-show="open" class="pl-6 mt-1 space-y-1">
                            @if(Auth::user()->hasPermission('edit-item'))
                                <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'bg-blue-700' : 'hover:bg-blue-700' }} text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                                    <i class="fas fa-tags mr-3 text-blue-300"></i>
                                    Catégories
                                </a>
                            @endif

                            @if(Auth::user()->hasPermission('create-payment') || Auth::user()->hasPermission('edit-payment'))
                                <a href="{{ route('payments.index') }}" class="{{ request()->routeIs('payments.*') ? 'bg-blue-700' : 'hover:bg-blue-700' }} text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                                    <i class="fas fa-money-bill-wave mr-3 text-blue-300"></i>
                                    Paiements
                                </a>
                            @endif

                            @if(Auth::user()->hasPermission('create-user') || Auth::user()->hasPermission('edit-user'))
                                <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') && !request()->routeIs('users.validate') ? 'bg-blue-700' : 'hover:bg-blue-700' }} text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                                    <i class="fas fa-users mr-3 text-blue-300"></i>
                                    Utilisateurs
                                </a>
                            @endif

                            @if(Auth::user()->hasPermission('create-user'))
                                <a href="{{ route('users.validate') }}" class="{{ request()->routeIs('users.validate') ? 'bg-blue-700' : 'hover:bg-blue-700' }} text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                                    <i class="fas fa-user-check mr-3 text-blue-300"></i>
                                    Validation
                                    @if(App\Models\User::where('is_validated', false)->count() > 0)
                                        <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full ml-2">
                                            {{ App\Models\User::where('is_validated', false)->count() }}
                                        </span>
                                    @endif
                                </a>
                            @endif

                            @if(Auth::user()->hasPermission('create-role') || Auth::user()->hasPermission('edit-role-permissions'))
                                <a href="{{ route('roles.index') }}" class="{{ request()->routeIs('roles.*') ? 'bg-blue-700' : 'hover:bg-blue-700' }} text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                                    <i class="fas fa-shield-alt mr-3 text-blue-300"></i>
                                    Rôles & Permissions
                                </a>
                            @endif

                            @if(Auth::user()->hasPermission('view-all-activity-logs') || Auth::user()->hasPermission('view-own-activity-logs'))
                                <a href="{{ route('activity-logs.index') }}" class="{{ request()->routeIs('activity-logs.*') ? 'bg-blue-700' : 'hover:bg-blue-700' }} text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                                    <i class="fas fa-history mr-3 text-blue-300"></i>
                                    Journal d'activité
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </nav>
        </div>
        <!-- User Profile Footer -->
        <div class="mt-auto border-t border-blue-700">
            <div class="flex items-center p-4">
                <div class="flex-shrink-0">
                    <div class="h-9 w-9 rounded-full bg-blue-700 flex items-center justify-center text-white font-medium">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                </div>
                <div class="ml-3 flex-1 min-w-0">
                    <div class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-blue-300 truncate">{{ Auth::user()->email }}</div>
                </div>
                <div class="ml-2 flex-shrink-0 flex">
                    <a href="{{ route('profile.edit') }}" class="text-blue-300 hover:text-white p-1" title="Modifier le profil">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="ml-2">
                        @csrf
                        <button type="submit" class="text-blue-300 hover:text-white p-1" title="Déconnexion">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile sidebar -->
    <div x-show="sidebarOpen" class="md:hidden fixed inset-0 flex z-40" style="display: none;">
        <div @click="sidebarOpen = false" x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0" style="display: none;">
            <div class="absolute inset-0 bg-gray-600 opacity-75"></div>
        </div>

        <div x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="relative flex-1 flex flex-col max-w-xs w-full pt-5 pb-4 bg-gradient-to-b from-blue-800 to-blue-900" style="display: none;">
            <div class="absolute top-0 right-0 -mr-12 pt-2">
                <button @click="sidebarOpen = false" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                    <span class="sr-only">Fermer le menu</span>
                    <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex-shrink-0 flex items-center px-4">
                <h1 class="text-xl font-bold text-white">La Partageothèque</h1>
            </div>

            <div class="mt-5 flex-1 h-0 overflow-y-auto">
                <nav class="px-2 space-y-1">
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'bg-blue-700' : 'hover:bg-blue-700' }} text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                        <i class="fas fa-home mr-3 text-blue-300"></i>
                        Tableau de bord
                    </a>

                    <!-- Accès rapide mobile -->
                    @if(Auth::user()->hasPermission('create-loan') || Auth::user()->hasPermission('return-loan') ||
                        Auth::user()->hasPermission('reserve-item') || Auth::user()->hasPermission('create-payment') ||
                        Auth::user()->hasPermission('create-user'))
                        <div x-data="{ open: false }">
                            <button @click="open = !open" class="w-full text-white hover:bg-blue-700 group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                                <i class="fas fa-bolt mr-3 text-blue-300"></i>
                                <span class="flex-1 text-left">Accès rapide</span>
                                <i class="fas" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                            </button>
                            <div x-show="open" class="pl-6 mt-1 space-y-1">
                                @if(Auth::user()->hasPermission('create-loan'))
                                    <a href="{{ route('loans.create') }}" class="text-white hover:bg-blue-700 group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                                        <i class="fas fa-plus-circle mr-3 text-blue-300"></i>
                                        Déclarer un emprunt
                                    </a>
                                @endif

                                @if(Auth::user()->hasPermission('reserve-item'))
                                    <a href="{{ route('reservations.create') }}" class="text-white hover:bg-blue-700 group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                                        <i class="fas fa-calendar-plus mr-3 text-blue-300"></i>
                                        Réserver un objet
                                    </a>
                                @endif

                                @if(Auth::user()->hasPermission('return-loan'))
                                    <a href="{{ route('loans.index', ['status' => 'active']) }}" class="text-white hover:bg-blue-700 group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                                        <i class="fas fa-undo-alt mr-3 text-blue-300"></i>
                                        Déclarer un retour
                                    </a>
                                @endif

                                @if(Auth::user()->hasPermission('create-payment'))
                                    <a href="{{ route('payments.create') }}" class="text-white hover:bg-blue-700 group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                                        <i class="fas fa-money-bill mr-3 text-blue-300"></i>
                                        Enregistrer un paiement
                                    </a>
                                @endif

                                @if(Auth::user()->hasPermission('create-user'))
                                    <a href="{{ route('users.create') }}" class="text-white hover:bg-blue-700 group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                                        <i class="fas fa-user-plus mr-3 text-blue-300"></i>
                                        Créer un utilisateur
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if(Auth::user()->hasPermission('view-catalog'))
                        <a href="{{ route('items.index') }}" class="{{ request()->routeIs('items.*') ? 'bg-blue-700' : 'hover:bg-blue-700' }} text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-box-open mr-3 text-blue-300"></i>
                            Catalogue
                        </a>
                    @endif

                    @if(Auth::user()->hasPermission('create-loan') || Auth::user()->hasPermission('return-loan'))
                        <a href="{{ route('loans.index') }}" class="{{ request()->routeIs('loans.*') ? 'bg-blue-700' : 'hover:bg-blue-700' }} text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-handshake mr-3 text-blue-300"></i>
                            Emprunts
                        </a>
                    @endif

                    @if(Auth::user()->hasPermission('create-reservation') || Auth::user()->hasPermission('edit-reservation'))
                        <a href="{{ route('reservations.index') }}" class="{{ request()->routeIs('reservations.*') ? 'bg-blue-700' : 'hover:bg-blue-700' }} text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-calendar-alt mr-3 text-blue-300"></i>
                            Réservations
                        </a>
                    @endif

                    <!-- Administration mobile -->
                    @if(Auth::user()->hasPermission('edit-item') ||
                        Auth::user()->hasPermission('create-user') ||
                        Auth::user()->hasPermission('edit-role-permissions') ||
                        Auth::user()->hasPermission('view-all-activity-logs'))
                        <div x-data="{ open: false }">
                            <button @click="open = !open" class="w-full text-white hover:bg-blue-700 group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                                <i class="fas fa-cogs mr-3 text-blue-300"></i>
                                <span class="flex-1 text-left">Administration</span>
                                <i class="fas" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                            </button>
                            <div x-show="open" class="pl-6 mt-1 space-y-1">
                                @if(Auth::user()->hasPermission('edit-item'))
                                    <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'bg-blue-700' : 'hover:bg-blue-700' }} text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                                        <i class="fas fa-tags mr-3 text-blue-300"></i>
                                        Catégories
                                    </a>
                                @endif

                                @if(Auth::user()->hasPermission('create-payment') || Auth::user()->hasPermission('edit-payment'))
                                    <a href="{{ route('payments.index') }}" class="{{ request()->routeIs('payments.*') ? 'bg-blue-700' : 'hover:bg-blue-700' }} text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                                        <i class="fas fa-money-bill-wave mr-3 text-blue-300"></i>
                                        Paiements
                                    </a>
                                @endif

                                @if(Auth::user()->hasPermission('create-user') || Auth::user()->hasPermission('edit-user'))
                                    <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') && !request()->routeIs('users.validate') ? 'bg-blue-700' : 'hover:bg-blue-700' }} text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                                        <i class="fas fa-users mr-3 text-blue-300"></i>
                                        Utilisateurs
                                    </a>
                                @endif

                                @if(Auth::user()->hasPermission('create-user'))
                                    <a href="{{ route('users.validate') }}" class="{{ request()->routeIs('users.validate') ? 'bg-blue-700' : 'hover:bg-blue-700' }} text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                                        <i class="fas fa-user-check mr-3 text-blue-300"></i>
                                        Validation
                                        @if(App\Models\User::where('is_validated', false)->count() > 0)
                                            <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full ml-2">
                                                {{ App\Models\User::where('is_validated', false)->count() }}
                                            </span>
                                        @endif
                                    </a>
                                @endif

                                @if(Auth::user()->hasPermission('create-role') || Auth::user()->hasPermission('edit-role-permissions'))
                                    <a href="{{ route('roles.index') }}" class="{{ request()->routeIs('roles.*') ? 'bg-blue-700' : 'hover:bg-blue-700' }} text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                                        <i class="fas fa-shield-alt mr-3 text-blue-300"></i>
                                        Rôles & Permissions
                                    </a>
                                @endif

                                @if(Auth::user()->hasPermission('view-all-activity-logs') || Auth::user()->hasPermission('view-own-activity-logs'))
                                    <a href="{{ route('activity-logs.index') }}" class="{{ request()->routeIs('activity-logs.*') ? 'bg-blue-700' : 'hover:bg-blue-700' }} text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                                        <i class="fas fa-history mr-3 text-blue-300"></i>
                                        Journal d'activité
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </nav>
            </div>

            <div class="flex-shrink-0 flex p-4 bg-blue-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center bg-blue-600 rounded-full text-white">
                        {{ Auth::user()->name[0] }}
                    </div>
                    <div class="ml-3 w-full">
                        <div class="text-base font-medium text-white truncate">{{ Auth::user()->name }}</div>
                        <div class="text-sm font-medium text-blue-300 truncate">{{ Auth::user()->email }}</div>
                        <div class="mt-2 flex">
                            <a href="{{ route('profile.edit') }}" class="text-xs text-blue-200 hover:text-white mr-3">Profil</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-xs !text-blue-200 hover:text-white">Déconnexion</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content area -->
    <div class="md:pl-64 flex flex-col flex-1">
        <!-- Top bar on mobile -->
        <div class="sticky top-0 z-10 flex-shrink-0 flex h-16 bg-white shadow md:hidden">
            <button @click="sidebarOpen = true" type="button" class="px-4 border-r border-gray-200 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 md:hidden">
                <span class="sr-only">Ouvrir le menu</span>
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <div class="flex-1 px-4 flex justify-between">
                <div class="flex-1 flex items-center">
                    <h1 class="text-xl font-semibold text-gray-800">La Partageothèque</h1>
                </div>
            </div>
        </div>
    </div>
</div>
