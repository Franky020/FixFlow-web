<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto - FixFlow</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-[#F2F2F2] font-sans text-[#003F4E]">

    <!-- Encabezado -->
    <header class="bg-[#006D77] p-4 text-white">
        <div class="container mx-auto">
            <a href="{{ route('home') }}" class="text-3xl font-extrabold tracking-tight">FixFlow</a>
        </div>
    </header>

    <main class="py-16 md:py-24">
        <div class="container mx-auto px-6">
            <div class="max-w-2xl mx-auto bg-white p-8 md:p-12 rounded-xl shadow-2xl border-t-4 border-[#F4A300]">
                <h1 class="text-4xl font-bold text-center text-[#003F4E] mb-4">Contacta a nuestro equipo</h1>
                <p class="text-center text-gray-600 mb-10">
                    ¿Tienes una consulta general o necesitas iniciar un servicio? Llena el formulario y te contactaremos pronto.
                </p>

                <form action="#" method="POST" class="space-y-6">
                    <!-- Nombre Completo -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-[#006D77] mb-2">Nombre Completo</label>
                        <input type="text" id="name" name="name" required
                            class="w-full p-3 rounded-lg border-2 border-gray-300 focus:outline-none focus:border-[#006D77] transition-colors"
                            placeholder="Tu nombre y apellido">
                    </div>

                    <!-- Email y Teléfono (en columna) -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="email" class="block text-sm font-semibold text-[#006D77] mb-2">Correo Electrónico</label>
                            <input type="email" id="email" name="email" required
                                class="w-full p-3 rounded-lg border-2 border-gray-300 focus:outline-none focus:border-[#006D77] transition-colors"
                                placeholder="ejemplo@dominio.com">
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-semibold text-[#006D77] mb-2">Teléfono (Opcional)</label>
                            <input type="tel" id="phone" name="phone"
                                class="w-full p-3 rounded-lg border-2 border-gray-300 focus:outline-none focus:border-[#006D77] transition-colors"
                                placeholder="+52 55 1234 5678">
                        </div>
                    </div>

                    <!-- Asunto -->
                    <div>
                        <label for="subject" class="block text-sm font-semibold text-[#006D77] mb-2">Asunto de la Consulta</label>
                        <select id="subject" name="subject" required
                            class="w-full p-3 rounded-lg border-2 border-gray-300 focus:outline-none focus:border-[#006D77] transition-colors bg-white">
                            <option value="">Selecciona un asunto...</option>
                            <option value="solicitud_info">Solicitud de Información General</option>
                            <option value="reporte_nuevo">Reporte de un Nuevo Problema (No urgente)</option>
                            <option value="cotizacion">Solicitar Cotización de Servicio</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>

                    <!-- Mensaje -->
                    <div>
                        <label for="message" class="block text-sm font-semibold text-[#006D77] mb-2">Mensaje Detallado</label>
                        <textarea id="message" name="message" rows="5" required
                            class="w-full p-3 rounded-lg border-2 border-gray-300 focus:outline-none focus:border-[#006D77] transition-colors resize-none"
                            placeholder="Describe tu consulta o problema aquí..."></textarea>
                    </div>

                    <!-- Botón de Envío -->
                    <div class="text-center pt-4">
                        <button type="submit"
                            class="w-full md:w-auto bg-[#F4A300] text-white px-10 py-3 rounded-full font-bold shadow-lg hover:shadow-xl hover:bg-opacity-90 transition-all transform hover:scale-105">
                            Enviar Mensaje
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>