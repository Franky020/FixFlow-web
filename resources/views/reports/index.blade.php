<x-app-layout>

    {{-- ENCABEZADO --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ff-dark leading-tight">
            {{ __('Reportes') }}
        </h2>
    </x-slot>

    <div class="container mx-auto py-10">

        {{-- CREAR REPORTE RÁPIDO --}}
        <div class="bg-ff-white shadow-xl rounded-xl p-6 mb-12 border-t-8 border-ff-secondary">
            <h2 class="text-2xl font-bold text-ff-dark mb-4">Crear Reporte Rápido</h2>

            <form method="POST" action="{{ route('report.create') }}">
                @csrf

                <label class="block font-semibold text-ff-dark mb-2">Seleccione Ticket:</label>

                <select name="ticket" required
                        class="w-full p-3 rounded-lg border border-ff-dark/30 mb-4 bg-ff-bg-light text-ff-dark">
                    <option value="" disabled selected>Seleccione un Ticket</option>

                    @foreach($ticketsList as $ticket)
                        <option value="{{ $ticket['id'] }}">
                            #{{ $ticket['id'] }} - {{ $ticket['title'] ?? 'Sin título' }}
                        </option>
                    @endforeach
                </select>

                <button class="w-full bg-ff-primary text-ff-dark font-bold py-3 rounded-lg hover:bg-ff-secondary hover:text-ff-white transition">
                    Crear Reporte
                </button>
            </form>
        </div>

        {{-- LISTA DE REPORTES --}}
        <div class="bg-ff-white shadow-xl rounded-xl p-6 border-t-8 border-ff-success">
            <h2 class="text-2xl font-bold text-ff-dark mb-6">Reportes Registrados</h2>

            @if (!empty($data))
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                    @foreach($data as $report)
                        <div class="p-5 bg-ff-bg-light rounded-xl shadow-lg border-l-4 border-ff-secondary">

                            <h3 class="text-xl font-bold text-ff-dark mb-2">
                                📄 Reporte #{{ $report['id'] }}
                            </h3>

                            <p class="text-ff-dark">
                                Ticket: <strong>#{{ $report['ticket'] }}</strong>
                            </p>

                            <p class="text-sm text-ff-dark/70 mt-2">
                                Fecha:
                                {{ \Carbon\Carbon::parse($report['created_at'])->format('Y-m-d H:i') }}
                            </p>

                            <div class="mt-4 flex justify-between items-center">

                                <a href="{{ route('reports.show', $report['id']) }}"
                                   class="text-ff-secondary font-bold hover:underline">
                                    Ver
                                </a>

                                <form method="POST" action="{{ route('reports.delete', $report['id']) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-ff-error font-bold hover:underline">
                                        Eliminar
                                    </button>
                                </form>

                            </div>
                        </div>
                    @endforeach

                </div>
            @else
                <p class="text-center text-ff-dark/60 py-6">No hay reportes registrados.</p>
            @endif

        </div>

    </div>

</x-app-layout>
