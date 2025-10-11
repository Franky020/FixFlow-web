<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - FixFlow</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-[#F2F2F2] font-sans text-[#003F4E]">

    <header class="bg-[#006D77] p-4 text-white">
        <div class="container mx-auto">
            <a href="{{ route('home') }}" class="text-3xl font-extrabold tracking-tight">FixFlow</a>
        </div>
    </header>

    <main class="py-16 md:py-24">
        <div class="container mx-auto px-6">
            <div class="max-w-xl mx-auto bg-white p-8 md:p-12 rounded-xl shadow-lg border-t-4 border-[#006D77]">
                <h1 class="text-4xl font-bold text-center text-[#003F4E] mb-4">Califica nuestro servicio</h1>
                <p class="text-center text-gray-600 mb-8">
                    Tu opinión es muy importante para nosotros. Ayúdanos a mejorar.
                </p>

                <form action="#" method="POST" class="space-y-6">
                    <div>
                        <label for="rating" class="block text-lg font-semibold text-[#006D77] mb-2">
                            ¿Cómo calificarías el servicio?
                        </label>
                        <div class="flex items-center justify-center space-x-4 text-3xl text-gray-400">
                            <span class="hover:text-[#F4A300] transition-colors cursor-pointer">⭐</span>
                            <span class="hover:text-[#F4A300] transition-colors cursor-pointer">⭐</span>
                            <span class="hover:text-[#F4A300] transition-colors cursor-pointer">⭐</span>
                            <span class="hover:text-[#F4A300] transition-colors cursor-pointer">⭐</span>
                            <span class="hover:text-[#F4A300] transition-colors cursor-pointer">⭐</span>
                        </div>
                    </div>

                    <div>
                        <label for="comments" class="block text-lg font-semibold text-[#006D77] mb-2">
                            Comentarios Adicionales
                        </label>
                        <textarea id="comments" name="comments" rows="5"
                            class="w-full p-4 rounded-lg border-2 border-gray-300 focus:outline-none focus:border-[#006D77] transition-colors resize-none"
                            placeholder="Escribe tus comentarios aquí..."></textarea>
                    </div>

                    <div class="text-center pt-4">
                        <button type="submit"
                            class="bg-[#F4A300] text-white px-8 py-3 rounded-full font-bold shadow-lg hover:shadow-xl hover:bg-opacity-90 transition-all transform hover:scale-105">
                            Enviar Feedback
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

</body>
</html>