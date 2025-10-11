<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nosotros - FixFlow</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-white font-sans text-[#003F4E]">

    <header class="bg-[#006D77] p-4 text-white">
        <div class="container mx-auto">
            <a href="{{ route('home') }}" class="text-3xl font-extrabold tracking-tight">FixFlow</a>
        </div>
    </header>

    <main class="py-16 md:py-24 text-center">
        <div class="container mx-auto px-6">
            <h1 class="text-5xl font-extrabold text-[#003F4E] mb-4">
                Somos tu equipo de soporte.
            </h1>
            <p class="text-xl text-gray-600 max-w-4xl mx-auto">
                En FixFlow, creemos que cada problema es una oportunidad. Estamos comprometidos a proporcionar
                soluciones rápidas y transparentes, construyendo una relación de confianza con cada ticket resuelto.
            </p>
        </div>
    </main>

    <section class="bg-[#F2F2F2] py-20">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="p-6">
                    <h2 class="text-4xl font-bold text-[#006D77] mb-4">Nuestra Misión</h2>
                    <p class="text-gray-700 leading-relaxed text-lg">
                        Simplificar el proceso de soporte técnico, utilizando tecnología para gestionar y resolver
                        problemas con la mayor eficiencia posible, garantizando una experiencia de usuario sin fricciones.
                    </p>
                </div>
                <div class="p-6">
                    <h2 class="text-4xl font-bold text-[#006D77] mb-4">Nuestra Visión</h2>
                    <p class="text-gray-700 leading-relaxed text-lg">
                        Convertirnos en la plataforma de gestión de tickets de referencia, reconocida por la velocidad
                        y la calidad de su soporte, y por su compromiso con la satisfacción total del cliente.
                    </p>
                </div>
            </div>
        </div>
    </section>

</body>
</html>