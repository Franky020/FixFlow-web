<x-app-layout>
    <x-slot name="header">
        <div class="border-l-4 border-ff-secondary pl-4">
            <h2 class="font-bold text-2xl text-ff-dark leading-tight">
                {{ __('Detalle del Reporte #') }}{{ $report['id'] ?? 'N/A' }}
            </h2>
        </div>
    </x-slot>

    {{-- NOTIFICACIONES --}}
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
        @if(session('success'))
            <div x-data="{ show: true }"
                 x-show="show"
                 x-transition
                 x-init="setTimeout(() => show = false, 3000)"
                 class="mb-6 p-4 rounded-lg bg-green-500 text-white font-bold shadow-lg">
                ✔ {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div x-data="{ show: true }"
                 x-show="show"
                 x-transition
                 x-init="setTimeout(() => show = false, 4000)"
                 class="mb-6 p-4 rounded-lg bg-red-600 text-white font-bold shadow-lg">
                ⚠ {{ session('error') }}
            </div>
        @endif
    </div>

    <div class="py-12 bg-ff-bg-light min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-ff-white shadow-2xl rounded-xl p-6 lg:p-8 border-t-8 border-ff-secondary">

                {{-- BOTÓN EXPORTAR PDF --}}
                <div class="flex justify-end mb-6">
                    <a href="{{ route('reports.export.pdf', $report['id']) }}"
                       class="inline-flex items-center px-6 py-3 bg-ff-primary text-ff-dark font-extrabold rounded-lg border border-ff-dark hover:bg-ff-secondary hover:text-white transition">
                        📄 Descargar Reporte PDF
                    </a>
                </div>

                {{-- INFORMACIÓN GENERAL --}}
                <h3 class="text-3xl font-extrabold text-ff-dark mb-6 border-b-2 border-gray-100 pb-3">
                    Información del Reporte
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-lg">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="font-semibold text-ff-secondary">ID del Reporte:</p>
                        <p class="font-bold text-ff-dark">{{ $report['id'] }}</p>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="font-semibold text-ff-secondary">Ticket Asociado:</p>
                        <p class="font-bold text-ff-dark">#{{ $report['ticket'] }}</p>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-lg md:col-span-2">
                        <p class="font-semibold text-ff-secondary">Fecha de Creación:</p>
                        <p class="font-bold text-ff-dark">
                            {{ \Carbon\Carbon::parse($report['created_at'])->format('Y-m-d H:i:s') }}
                        </p>
                    </div>
                </div>

                {{-- IMÁGENES DEL REPORTE --}}
                @if(!empty($report['messages']))
                    <h3 class="mt-10 text-2xl font-extrabold text-ff-dark border-l-4 border-ff-secondary pl-4">
                        Imágenes del Reporte
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
                        @foreach($report['messages'] as $msg)
                            @if(!empty($msg['image']))
                                <div class="bg-gray-100 p-4 rounded-xl shadow">
                                    <img src="{{ $msg['image'] }}"
                                         class="rounded-lg w-full object-cover h-48"
                                         alt="Imagen del reporte">

                                    <p class="mt-3 text-sm font-semibold text-ff-secondary">
                                        {{ \Carbon\Carbon::parse($msg['created_at'])->format('Y-m-d H:i:s') }}
                                    </p>

                                    <p class="text-ff-dark mt-1">{{ $msg['message'] }}</p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif

                {{-- FORMULARIO --}}
                <h3 class="mt-12 text-2xl font-extrabold text-ff-dark border-l-4 border-ff-primary pl-4">
                    Enviar Mensaje del Reporte
                </h3>

                <form method="POST" action="{{ route('reports.message.store', $report['id']) }}"
                      enctype="multipart/form-data"
                      class="mt-6 p-6 border-2 border-ff-primary rounded-xl bg-ff-primary/10">

                    @csrf

                    <label class="font-semibold text-ff-secondary text-lg">Mensaje:</label>
                    <textarea name="message" required rows="4"
                        class="w-full mt-2 p-3 rounded-lg border border-ff-dark/40 bg-ff-bg-light text-ff-dark"
                        placeholder="Escriba el mensaje del reporte..."></textarea>

                    <label class="block mt-6 font-semibold text-ff-secondary text-lg">Imagen (opcional):</label>
                    <input type="file" name="image" accept="image/*"
                           class="mt-2 w-full p-3 rounded-lg border border-ff-dark/40 bg-ff-white">

                    <button class="mt-6 w-full bg-ff-primary text-ff-dark font-extrabold py-3 rounded-lg hover:bg-ff-secondary hover:text-ff-white transition">
                        Enviar Mensaje
                    </button>
                </form>

                {{-- VOLVER --}}
                <div class="mt-10">
                    <a href="{{ route('reports.index') }}"
                       class="inline-flex items-center px-6 py-3 bg-ff-secondary border border-transparent rounded-lg font-bold text-ff-white uppercase tracking-widest hover:bg-ff-dark transition duration-150">
                        ← Volver al Listado de Reportes
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
