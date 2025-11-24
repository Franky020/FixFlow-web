<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - FixFlow</title>
    @vite('resources/css/app.css')
    <script src="https://unpkg.com/lucide@latest"></script> 
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

            <h1 class="text-4xl font-bold text-center mb-4">Califica nuestro servicio</h1>
            <p class="text-center text-gray-600 mb-8">
                Tu opinión es muy importante para nosotros.
            </p>

            {{-- Mensajes --}}
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('feedback.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- CAMPO TICKET_ID --}}
                <div>
                    <label for="ticket_id" class="block text-lg font-semibold mb-2">ID del Ticket</label>
                    <input type="number" id="ticket_id" name="ticket_id"
                           required min="1"
                           value="{{ old('ticket_id') }}"
                           class="w-full p-3 rounded-lg border-2 border-gray-300 focus:border-[#006D77] outline-none">
                    @error('ticket_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- RATING (Select de 1 a 5 en pasos de 0.5) --}}
<div>
    <label for="rating" class="block text-lg font-semibold mb-2">Calificación</label>
    <select id="rating" name="rating" required
        class="w-full p-3 rounded-lg border-2 border-gray-300 focus:border-[#006D77] outline-none">

        <option value="">Selecciona una calificación</option>

        @for ($i = 1; $i <= 5; $i += 0.5)
            <option value="{{ number_format($i, 1) }}"
                {{ old('rating') == number_format($i, 1) ? 'selected' : '' }}>
                {{ number_format($i, 1) }}
            </option>
        @endfor
    </select>

    @error('rating')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>


                {{-- MENSAJE --}}
                <div>
                    <label for="message" class="block text-lg font-semibold mb-2">Comentarios</label>
                    <textarea id="message" name="message" rows="5"
                              class="w-full p-4 rounded-lg border-2 border-gray-300 focus:border-[#006D77] resize-none"
                    >{{ old('message') }}</textarea>

                    @error('message')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- BOTÓN --}}
                <div class="text-center">
                    <button type="submit"
                        class="bg-[#F4A300] text-white px-8 py-3 rounded-full font-bold shadow-lg hover:scale-105 transition">
                        Enviar Feedback
                    </button>
                </div>
            </form>

        </div>
    </div>
</main>

<script> lucide.createIcons(); </script>

</body>
</html>
