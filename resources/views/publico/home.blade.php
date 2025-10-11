<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FixFlow - Gestión de Tickets</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-white font-sans text-[#003F4E]">

    <header class="bg-[#006D77] shadow-lg">
        <div class="container mx-auto flex items-center justify-between p-6">
            <a href="{{ route('home') }}" class="flex items-center space-x-2">
                <span class="text-3xl font-extrabold tracking-tight text-white">FixFlow</span>
            </a>
            <nav class="hidden md:flex items-center space-x-6">
                <!-- NAVIGACIÓN CONSERVADA SEGÚN SOLICITUD DEL USUARIO -->
                <a href="{{ route('home') }}" class="text-white hover:text-gray-200 transition-colors">Inicio</a>
                <a href="{{ route('about') }}" class="text-white hover:text-gray-200 transition-colors">Nosotros</a>
                <a href="{{ route('consultar') }}" class="text-white hover:text-gray-200 transition-colors">Contactanos</a>
                <a href="{{ route('feed') }}" class="text-white hover:text-gray-200 transition-colors">Calificanos</a>
            </nav>
            <!-- CAMBIO AQUÍ: Se enlaza el botón 'Ingresar' a la ruta 'login' -->
            <a href="{{ route('login') }}" class="bg-[#F4A300] text-white px-6 py-2 rounded-full font-semibold shadow-md hover:bg-opacity-90 transition-all">
                Ingresar
            </a>
        </div>
    </header>

    <main class="bg-white py-24 md:py-32">
        <div class="container mx-auto px-6 text-center">
            <h1 class="text-5xl md:text-6xl font-extrabold leading-tight text-[#003F4E] mb-4">
                El primer paso para una solución efectiva.
            </h1>
            <p class="text-xl md:text-2xl text-gray-600 mb-10 max-w-3xl mx-auto">
                ¿Necesitas soporte o tienes una consulta? Contáctanos directamente y nuestro equipo te asistirá con rapidez.
            </p>
            <!-- El botón de acción apunta a la nueva ruta 'consultar' (Contactanos) -->
            <a href="{{ route('consultar') }}" class="inline-block bg-[#F4A300] text-white px-10 py-4 rounded-full text-lg font-bold shadow-lg hover:shadow-xl hover:bg-opacity-90 transition-all transform hover:scale-105">
                Iniciar Contacto
            </a>
        </div>
    </main>

    <section class="bg-[#F2F2F2] py-20">
        <div class="container mx-auto px-6">
            <h2 class="text-4xl font-bold text-center text-[#003F4E] mb-16">¿Cómo funciona el contacto con FixFlow?</h2>
            <div class="grid md:grid-cols-3 gap-12">
                <!-- Paso 1: Contacto (Sustituye a 'Ingresa tu ID') -->
                <div class="bg-white p-8 rounded-xl shadow-lg transform hover:scale-105 transition-transform duration-300 border-t-4 border-[#006D77]">
                    <div class="text-center">
                        <span class="text-5xl text-[#006D77] mb-4 inline-block">📧</span>
                        <h3 class="text-2xl font-bold text-[#003F4E] mt-4 mb-2">1. Envíanos tu Consulta</h3>
                        <p class="text-gray-600">
                            Rellena nuestro formulario con tus datos y la descripción de tu requerimiento.
                        </p>
                    </div>
                </div>
                <!-- Paso 2: Análisis (Sustituye a 'Revisa el Estado') -->
                <div class="bg-white p-8 rounded-xl shadow-lg transform hover:scale-105 transition-transform duration-300 border-t-4 border-[#006D77]">
                    <div class="text-center">
                        <span class="text-5xl text-[#006D77] mb-4 inline-block">🧠</span>
                        <h3 class="text-2xl font-bold text-[#003F4E] mt-4 mb-2">2. Análisis Rápido</h3>
                        <p class="text-gray-600">
                            Nuestro equipo evalúa tu solicitud para identificar el especialista adecuado.
                        </p>
                    </div>
                </div>
                <!-- Paso 3: Respuesta (Sustituye a 'Mantente al tanto') -->
                <div class="bg-white p-8 rounded-xl shadow-lg transform hover:scale-105 transition-transform duration-300 border-t-4 border-[#006D77]">
                    <div class="text-center">
                        <span class="text-5xl text-[#006D77] mb-4 inline-block">📞</span>
                        <h3 class="text-2xl font-bold text-[#003F4E] mt-4 mb-2">3. Recibe la Respuesta</h3>
                        <p class="text-gray-600">
                            Te contactaremos directamente con una solución o el siguiente paso.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

</body>
</html>