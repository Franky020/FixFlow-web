<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

```
<title>{{ config('app.name', 'FixFlow') }}</title>

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

<script src="https://unpkg.com/lucide@latest"></script>
<script src="//unpkg.com/alpinejs" defer></script>
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

</head>

<body class="font-sans antialiased bg-[#F2F2F2]">
    <div class="min-h-screen flex">

```
    @php
        // Obtener datos de usuario desde sesión
        $user = session('user_data');
        $role = $user['user_type'] ?? 'normal_user';
    @endphp

    <nav id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-[#006D77] shadow-lg flex flex-col transform transition-transform duration-300 -translate-x-full z-40">
        <div class="flex items-center justify-between h-16 border-b border-gray-200 px-4">
            <a href="{{ route('dashboards') }}">
                <span class="text-2xl font-extrabold tracking-tight text-white">FixFlow</span>
            </a>
            <button id="closeSidebar" class="text-white text-2xl hover:text-[#F4A300]">&times;</button>
        </div>

        <div class="flex-1 overflow-y-auto mt-4">
            <ul class="space-y-3 px-4 text-xl font-semibold">

                {{-- INICIO - visible para todos --}}
                <li>
                    <x-nav-link :href="route('dashboards')" :active="request()->routeIs('dashboards')" class="flex items-center gap-3 text-white hover:text-[#F4A300]">
                        <i data-lucide="home"></i> Inicio
                    </x-nav-link>
                </li>

                {{-- USUARIOS - oculto para normal_user --}}
                @if ($role !== 'normal_user')
                <li>
                    <x-nav-link :href="route('user.list')" :active="request()->routeIs('user.list')" class="flex items-center gap-3 text-white hover:text-[#F4A300]">
                        <i data-lucide="users"></i> Usuarios
                    </x-nav-link>
                </li>
                @endif

                {{-- TICKETS - visible para todos --}}
                <li>
                    <x-nav-link :href="route('tickets.index')" :active="request()->routeIs('tickets.index')" class="flex items-center gap-3 text-white hover:text-[#F4A300]">
                        <i data-lucide="ticket"></i> Tickets
                    </x-nav-link>
                </li>

                {{-- REFACCIONES - visible para todos --}}
                <li>
                    <x-nav-link :href="route('spare_parts.index')" :active="request()->routeIs('spare_parts.index')" class="flex items-center gap-3 text-white hover:text-[#F4A300]">
                        <i data-lucide="settings"></i> Refacciones
                    </x-nav-link>
                </li>

                {{-- REPORTES - visible para todos --}}
                <li>
                    <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.index')" class="flex items-center gap-3 text-white hover:text-[#F4A300]">
                        <i data-lucide="bar-chart-2"></i> Reportes
                    </x-nav-link>
                </li>

                {{-- LOCACIONES - visible para todos --}}
                <li>
                    <x-nav-link :href="route('locations.index')" :active="request()->routeIs('locations.index')" class="flex items-center gap-3 text-white hover:text-[#F4A300]">
                        <i data-lucide="map-pin"></i> Locaciones
                    </x-nav-link>
                </li>

                {{-- COMPAÑÍAS - oculto para normal_user --}}
                @if ($role !== 'normal_user')
                <li>
                    <x-nav-link :href="route('companies.index')" :active="request()->routeIs('companies.index')" class="flex items-center gap-3 text-white hover:text-[#F4A300]">
                        <i data-lucide="building-2"></i> Compañías
                    </x-nav-link>
                </li>
                @endif

                {{-- FEEDBACK - oculto para super_admin y normal_user --}}
                @if ($role !== 'super_admin' && $role !== 'normal_user')
                <li>
                    <x-nav-link :href="route('feedback.index')" :active="request()->routeIs('feedback.index')" class="flex items-center gap-3 text-white hover:text-[#F4A300]">
                        <i data-lucide="message-square"></i> Feedback
                    </x-nav-link>
                </li>
                @endif

                {{-- LOGOUT --}}
                <li class="pt-4 border-t border-gray-600 mt-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left flex items-center gap-3 text-[#F4A300] hover:text-white font-semibold transition duration-150">
                            <i data-lucide="log-out" class="w-6 h-6"></i> Cerrar Sesión
                        </button>
                    </form>
                </li>

            </ul>
        </div>
    </nav>

    {{-- MAIN CONTENT --}}
    <div id="mainContent" class="flex-1 transition-all duration-300 ml-0">
        <button id="openSidebar" class="fixed top-4 left-4 z-50 text-3xl text-[#006D77] hover:text-[#F4A300] bg-white rounded-full p-2 shadow-md">
            ☰
        </button>

        @isset($header)
            <header class="bg-white shadow mt-16">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main class="p-6">
            {{ $slot }}
        </main>
    </div>
</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const openBtn = document.getElementById('openSidebar');
    const closeBtn = document.getElementById('closeSidebar');
    const mainContent = document.getElementById('mainContent');

    const sidebarOpen = localStorage.getItem('sidebarOpen') === 'true';

    if (sidebarOpen) {
        sidebar.classList.remove('-translate-x-full');
        mainContent.classList.add('ml-64');
        openBtn.style.display = 'none';
    } else {
        sidebar.classList.add('-translate-x-full');
        mainContent.classList.remove('ml-64');
        openBtn.style.display = 'block';
    }

    openBtn.addEventListener('click', () => {
        sidebar.classList.remove('-translate-x-full');
        mainContent.classList.add('ml-64');
        openBtn.style.display = 'none';
        localStorage.setItem('sidebarOpen', true);
    });

    closeBtn.addEventListener('click', () => {
        sidebar.classList.add('-translate-x-full');
        mainContent.classList.remove('ml-64');
        openBtn.style.display = 'block';
        localStorage.setItem('sidebarOpen', false);
    });

    if (localStorage.getItem('sidebarOpen') === null) {
        localStorage.setItem('sidebarOpen', false);
        sidebar.classList.add('-translate-x-full');
        openBtn.style.display = 'block';
    }

    lucide.createIcons();
</script>
```

</body>
</html>
