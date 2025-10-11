<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'FixFlow') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        </head>
    <body class="font-sans antialiased bg-[#F2F2F2]">
        <div class="min-h-screen">
            
            <nav x-data="{ open: false }" class="bg-[#006D77] border-b border-gray-100 shadow-md">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <div class="shrink-0 flex items-center">
                                <a href="{{ route('dashboard') }}">
                                    <span class="text-2xl font-extrabold tracking-tight text-white">
                                        FixFlow
                                    </span>
                                </a>
                            </div>

                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-white border-b-2 border-transparent hover:border-[#F4A300] hover:text-[#F4A300] transition-colors">
                                    {{ __('Dashboard') }}
                                </x-nav-link>
                                {{-- ENLACE CORREGIDO: Usando la ruta 'user.list' --}}
                                <x-nav-link :href="route('user.list')" :active="request()->routeIs('user.list')" class="text-white border-b-2 border-transparent hover:border-[#F4A300] hover:text-[#F4A300] transition-colors">
                                    {{ __('Gestión de Usuarios') }}
                                </x-nav-link>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            {{-- CORRECCIÓN: Usar @auth para evitar el error "Attempt to read property 'name' on null" --}}
                            @auth
                                <x-dropdown align="right" width="48">
                                    <x-slot name="trigger">
                                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-transparent hover:text-[#F4A300] focus:outline-none transition ease-in-out duration-150">
                                            <div>{{ Auth::user()->name }}</div>
                                            <div class="ms-1">
                                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </button>
                                    </x-slot>

                                    <x-slot name="content">
                                        <x-dropdown-link :href="route('profile.edit')">
                                            {{ __('Perfil') }}
                                        </x-dropdown-link>

                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <x-dropdown-link :href="route('logout')"
                                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                                {{ __('Cerrar Sesión') }}
                                            </x-dropdown-link>
                                        </form>
                                    </x-slot>
                                </x-dropdown>
                            @else
                                {{-- Mostrar enlace de login si no hay usuario autenticado --}}
                                <a href="{{ route('login') }}" class="text-white hover:text-[#F4A300] transition-colors duration-200">
                                    {{ __('Login') }}
                                </a>
                            @endauth
                        </div>

                        <div class="flex items-center sm:hidden">
                            <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-200 hover:text-white hover:bg-[#003F4E] focus:outline-none focus:bg-[#003F4E] focus:text-white transition duration-150 ease-in-out">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-[#005259]">
                    <div class="pt-2 pb-3 space-y-1">
                        <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-white hover:bg-[#003F4E]">
                            {{ __('Dashboard') }}
                        </x-responsive-nav-link>
                    </div>

                    {{-- CORRECCIÓN: Usar @auth en Responsive Settings Options --}}
                    @auth
                        <div class="pt-4 pb-1 border-t border-gray-500">
                            <div class="px-4">
                                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                                <div class="font-medium text-sm text-gray-300">{{ Auth::user()->email }}</div>
                            </div>

                            <div class="mt-3 space-y-1">
                                <x-responsive-nav-link :href="route('profile.edit')" class="text-white hover:bg-[#003F4E]">
                                    {{ __('Perfil') }}
                                </x-responsive-nav-link>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-responsive-nav-link :href="route('logout')"
                                            onclick="event.preventDefault(); this.closest('form').submit();" class="text-white hover:bg-[#003F4E]">
                                        {{ __('Cerrar Sesión') }}
                                    </x-responsive-nav-link>
                                </form>
                            </div>
                        </div>
                    @else
                        {{-- Enlace de Login para móvil si no está autenticado --}}
                        <div class="pt-4 pb-3 border-t border-gray-500">
                             <x-responsive-nav-link :href="route('login')" class="text-white hover:bg-[#003F4E]">
                                 {{ __('Login') }}
                             </x-responsive-nav-link>
                        </div>
                    @endauth
                </div>
            </nav>
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main>
                {{ $slot }}
            </main>
        </div>
        
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            console.log("DEBUG: Chart.js loaded status:", typeof Chart !== 'undefined' ? 'SUCCESS' : 'ERROR');
        </script>

        @stack('scripts')
    </body>
</html>