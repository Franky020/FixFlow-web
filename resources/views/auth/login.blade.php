<x-guest-layout>

    <header class="w-full mb-6 bg-[#006D77] p-4 shadow-md">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="text-xl font-semibold text-white">
                Mi Aplicación
            </div>

            <nav class="hidden md:flex items-center space-x-6">
                <a href="{{ route('home') }}" class="text-white hover:text-[#F4A300] transition-colors font-medium">Inicio</a>
                <a href="{{ route('about') }}" class="text-white hover:text-[#F4A300] transition-colors font-medium">Nosotros</a>
                <a href="{{ route('consultar') }}" class="text-white hover:text-[#F4A300] transition-colors font-medium">Contactanos</a>
                <a href="{{ route('feed') }}" class="text-white hover:text-[#F4A300] transition-colors font-medium">Calificanos</a>
            </nav>
            
            <button class="md:hidden text-white focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
            </button>
        </div>
    </header>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="p-6 bg-white shadow-xl rounded-lg border-t-4 border-[#006D77]">
        @csrf

        <h2 class="text-2xl font-bold mb-6 text-[#003F4E]">{{ __('Iniciar Sesión') }}</h2>

        <div>
            <x-input-label for="email" :value="__('Email')" class="text-[#003F4E]" />
            <x-text-input id="email" class="block mt-1 w-full border-gray-300 focus:border-[#006D77] focus:ring-[#006D77]" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-[#E74C3C]" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Contraseña')" class="text-[#003F4E]" />
            <x-text-input id="password" class="block mt-1 w-full border-gray-300 focus:border-[#006D77] focus:ring-[#006D77]"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-[#E74C3C]" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#006D77] shadow-sm focus:ring-[#006D77]" name="remember">
                <span class="ms-2 text-sm text-[#003F4E]">{{ __('Recuérdame') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-6">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-[#006D77] hover:text-[#F4A300] rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#006D77]" href="{{ route('password.request') }}">
                    {{ __('¿Olvidaste tu contraseña?') }}
                </a>
            @endif

            <x-primary-button class="ms-3 bg-[#F4A300] hover:bg-orange-600 focus:ring-[#006D77] active:bg-orange-700 border-transparent">
                {{ __('Entrar') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>