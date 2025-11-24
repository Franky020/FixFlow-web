<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - FixFlow</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-[#F2F2F2] font-sans text-[#003F4E] min-h-screen">

    <!-- NOTIFICACIÓN -->
    <div id="alertBox"
         class="hidden max-w-md mx-auto mt-4 p-4 rounded-lg text-white text-center font-semibold">
        @if(session('error'))
            {{ session('error') }}
        @endif
    </div>

    <!-- NAVBAR -->
    <header class="bg-[#006D77] shadow-lg">
        <div class="container mx-auto flex items-center justify-between p-6">
            <a href="{{ route('home') }}" class="flex items-center space-x-2">
                <span class="text-3xl font-extrabold tracking-tight text-white">FixFlow</span>
            </a>

            <nav class="hidden md:flex items-center space-x-6">
                <a href="{{ route('home') }}" class="text-white hover:text-gray-200 transition-colors">Inicio</a>
                <a href="{{ route('about') }}" class="text-white hover:text-gray-200 transition-colors">Nosotros</a>
                <a href="{{ route('consultar') }}" class="text-white hover:text-gray-200 transition-colors">Contáctanos</a>
                <a href="{{ route('feed') }}" class="text-white hover:text-gray-200 transition-colors">Califícanos</a>
            </nav>

            <a href="{{ route('login') }}"
               class="bg-[#F4A300] text-white px-6 py-2 rounded-full font-semibold shadow-md hover:bg-opacity-90 transition-all">
                Ingresar
            </a>
        </div>
    </header>

    <!-- CONTENIDO LOGIN -->
    <main class="flex items-center justify-center py-16 px-4">
        <div class="w-full max-w-md bg-white p-8 md:p-10 rounded-xl shadow-2xl border-t-8 border-[#006D77]">

            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-[#003F4E] mb-2">Bienvenido a FixFlow</h1>
                <p class="text-gray-500">Accede a tu panel de gestión de tickets.</p>
            </div>

            <!-- FORM LOGIN -->
            <form method="POST" action="{{ route('login.api') }}" class="space-y-6">
                @csrf

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-[#006D77] mb-2">Correo Electrónico</label>
                    <input name="email" type="email" required
                        class="w-full p-3 rounded-lg border-2 border-gray-300 focus:outline-none focus:border-[#F4A300]"
                        placeholder="ejemplo@empresa.com" />
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-medium text-[#006D77] mb-2">Contraseña</label>
                    <input name="password" type="password" required
                        class="w-full p-3 rounded-lg border-2 border-gray-300 focus:outline-none focus:border-[#F4A300]"
                        placeholder="Ingresa tu contraseña" />
                </div>

                <!-- Remember -->
                <div class="flex justify-between items-center">
                    <label class="inline-flex items-center text-sm text-gray-700">
                        <input name="remember_me" type="checkbox"
                            class="rounded border-gray-300 text-[#006D77] shadow-sm">
                        <span class="ms-2">Recuérdame</span>
                    </label>
                </div>

                <!-- BUTTON -->
                <div class="pt-4">
                    <button type="submit"
                        class="w-full bg-[#F4A300] text-white px-8 py-3 rounded-full font-bold shadow-lg hover:shadow-xl hover:bg-opacity-90 transition-all transform hover:scale-[1.01]">
                        Acceder
                    </button>
                </div>
            </form>
        </div>
    </main>

</body>
</html>
