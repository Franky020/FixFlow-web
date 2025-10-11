@php
    // Simulación de datos de usuario para el ejemplo
    // En un entorno real de Laravel, usarías:
    // $user = Auth::user();
    
    // Ejemplo de simulación
    $user = (object)[
        'name' => 'John Doe',
        'role' => 'Administrador', // O 'Técnico'
        'photo_url' => 'https://via.placeholder.com/150/ff-primary/fff?text=JD', // URL real de la foto
    ];
@endphp

<nav class="bg-ff-secondary shadow-md border-b border-ff-dark/10" x-data="{ open: false, userMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            {{-- Logo FlixFlow --}}
            <div class="flex-shrink-0">
                <a href="{{ route('dashboard') }}" class="text-2xl font-bold text-white tracking-wider">
                    FLIXFLOW
                </a>
            </div>

            {{-- Menú de Navegación Principal --}}
            <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                <a href="#" class="text-white hover:bg-ff-dark hover:text-ff-primary px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                    Dashboard
                </a>
                <a href="#" class="text-white hover:bg-ff-dark hover:text-ff-primary px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                    Tickets
                </a>
                @if ($user->role === 'Administrador')
                    <a href="#" class="text-white hover:bg-ff-dark hover:text-ff-primary px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                        Usuarios
                    </a>
                @endif
            </div>

            {{-- Menú de Usuario con Dropdown (Derecha) --}}
            <div class="hidden sm:ml-6 sm:flex sm:items-center">
                <div class="relative">
                    <button @click="userMenuOpen = !userMenuOpen" 
                            class="flex items-center text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-ff-primary transition duration-150 ease-in-out" 
                            id="user-menu" aria-expanded="true" aria-haspopup="true">
                        
                        {{-- Foto de Perfil --}}
                        <img class="h-8 w-8 rounded-full object-cover" src="{{ $user->photo_url }}" alt="{{ $user->name }}">
                        
                        {{-- Nombre y Rol --}}
                        <div class="ml-3 text-left hidden md:block">
                            <div class="text-sm font-medium text-white">{{ $user->name }}</div>
                            <div class="text-xs text-ff-primary">{{ $user->role }}</div>
                        </div>

                        {{-- Icono de flecha (opcional) --}}
                        <svg class="ml-2 -mr-0.5 h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>

                    </button>

                    {{-- Dropdown del Menú de Usuario --}}
                    <div x-show="userMenuOpen" 
                         @click.away="userMenuOpen = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10" 
                         role="menu" aria-orientation="vertical" aria-labelledby="user-menu">
                        
                        <a href="#" class="block px-4 py-2 text-sm text-ff-dark hover:bg-ff-bg-light" role="menuitem">
                            Mi Perfil
                        </a>
                        <a href="#" class="block px-4 py-2 text-sm text-ff-dark hover:bg-ff-bg-light" role="menuitem">
                            Configuración
                        </a>
                        <div class="border-t border-gray-100"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-ff-error hover:bg-ff-bg-light" role="menuitem">
                                Cerrar Sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Botón de Menú Móvil (Hamburguesa) --}}
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = !open" 
                        class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-ff-primary hover:bg-ff-dark focus:outline-none focus:bg-ff-dark focus:text-ff-primary transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Menú Móvil Desplegable --}}
    <div :class="{'block': open, 'hidden': ! open}" class="sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <a href="#" class="block pl-3 pr-4 py-2 border-l-4 border-ff-primary text-base font-medium text-white bg-ff-dark/50">
                Dashboard
            </a>
            <a href="#" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-white hover:text-ff-primary hover:bg-ff-dark/50">
                Tickets
            </a>
            @if ($user->role === 'Administrador')
                <a href="#" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-white hover:text-ff-primary hover:bg-ff-dark/50">
                    Usuarios
                </a>
            @endif
        </div>
        <div class="pt-4 pb-3 border-t border-ff-dark/20">
            <div class="flex items-center px-4">
                <div class="flex-shrink-0">
                    <img class="h-10 w-10 rounded-full" src="{{ $user->photo_url }}" alt="{{ $user->name }}">
                </div>
                <div class="ml-3">
                    <div class="font-medium text-base text-white">{{ $user->name }}</div>
                    <div class="font-medium text-sm text-ff-primary">{{ $user->role }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <a href="#" class="block px-4 py-2 text-sm text-ff-dark hover:bg-ff-bg-light">Mi Perfil</a>
                <a href="#" class="block px-4 py-2 text-sm text-ff-dark hover:bg-ff-bg-light">Configuración</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-ff-error hover:bg-ff-bg-light">
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>