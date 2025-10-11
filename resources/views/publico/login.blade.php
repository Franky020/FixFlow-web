<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - FixFlow</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-[#F2F2F2] font-sans text-[#003F4E] min-h-screen">

    <!-- BARRA DE NAVEGACIÓN (Tomada de home.blade.php) -->
    <header class="bg-[#006D77] shadow-lg">
        <div class="container mx-auto flex items-center justify-between p-6">
            <a href="{{ route('home') }}" class="flex items-center space-x-2">
                <span class="text-3xl font-extrabold tracking-tight text-white">FixFlow</span>
            </a>
            <nav class="hidden md:flex items-center space-x-6">
                <a href="{{ route('home') }}" class="text-white hover:text-gray-200 transition-colors">Inicio</a>
                <a href="{{ route('about') }}" class="text-white hover:text-gray-200 transition-colors">Nosotros</a>
                <a href="{{ route('consultar') }}" class="text-white hover:text-gray-200 transition-colors">Contactanos</a>
                <a href="{{ route('feed') }}" class="text-white hover:text-gray-200 transition-colors">Calificanos</a>
            </nav>
            <!-- El botón de Ingresar puede ser un enlace de 'Inicio' si ya estamos en el login, o lo eliminamos -->
            <a href="{{ route('login') }}" class="bg-[#F4A300] text-white px-6 py-2 rounded-full font-semibold shadow-md hover:bg-opacity-90 transition-all">
                Ingresar
            </a>
        </div>
    </header>

    <!-- CONTENEDOR CENTRAL DE LOGIN -->
    <main class="flex items-center justify-center py-16 px-4">
        <div class="w-full max-w-md bg-white p-8 md:p-10 rounded-xl shadow-2xl border-t-8 border-[#006D77] transform transition-all duration-300 hover:shadow-3xl">

            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-[#003F4E] mb-2">Bienvenido a FixFlow</h1>
                <p class="text-gray-500">Accede a tu panel de gestión de tickets.</p>
            </div>

            <!-- FORMULARIO DE INICIO DE SESIÓN -->
            <!-- Nota: Debes asegurar que la acción del formulario apunte a la ruta de login de Laravel (usualmente 'login') -->
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Campo Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-[#006D77] mb-2">Correo Electrónico</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full p-3 rounded-lg border-2 border-gray-300 focus:outline-none focus:border-[#F4A300] transition-colors"
                           placeholder="ejemplo@empresa.com" autocomplete="username" />
                    <!-- Manejo de errores de Laravel -->
                    @error('email')
                        <span class="text-sm text-[#E74C3C] mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Campo Contraseña -->
                <div>
                    <label for="password" class="block text-sm font-medium text-[#006D77] mb-2">Contraseña</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                           class="w-full p-3 rounded-lg border-2 border-gray-300 focus:outline-none focus:border-[#F4A300] transition-colors"
                           placeholder="Ingresa tu contraseña" />
                    @error('password')
                        <span class="text-sm text-[#E74C3C] mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Opciones (Recuérdame / Olvidé contraseña) -->
                <div class="flex justify-between items-center">
                    <label for="remember_me" class="inline-flex items-center text-sm text-gray-700">
                        <input id="remember_me" type="checkbox" name="remember" class="rounded border-gray-300 text-[#006D77] shadow-sm focus:ring-[#006D77]">
                        <span class="ms-2">Recuérdame</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-sm text-[#006D77] hover:text-[#F4A300] underline transition-colors" href="{{ route('password.request') }}">
                            ¿Olvidaste tu contraseña?
                        </a>
                    @endif
                </div>

                <!-- Botón de Enviar -->
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