<x-app-layout>
    <x-slot name="header">
        <div class="border-l-4 border-ff-secondary pl-4">
            <h2 class="font-bold text-2xl text-ff-dark leading-tight">
                {{ __('Inventario de Piezas de Recambio') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-10 bg-ff-bg-light min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Mensajes --}}
            @if (session('success'))
                <div class="mb-4 bg-ff-success/20 border border-ff-success text-ff-dark px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 bg-ff-error/20 border border-ff-error text-ff-dark px-4 py-4 rounded">
                    <h3 class="font-bold">Error en la Operación</h3>
                    <ul class="list-disc pl-6 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Tarjeta principal --}}
            <div class="bg-ff-white shadow-lg rounded-xl p-6">

                <div class="flex justify-between items-center mb-6 border-b pb-3">
                    <h3 class="text-xl font-bold text-ff-dark">Listado de Piezas</h3>

                    <button onclick="document.getElementById('createForm').classList.toggle('hidden')"
                        class="bg-ff-primary text-ff-white font-semibold px-5 py-2 rounded-lg shadow hover:bg-ff-secondary transition">
                        + Nueva Pieza de Recambio
                    </button>
                </div>

                {{-- Formulario Crear --}}
                <div id="createForm" class="hidden bg-ff-bg-light p-6 rounded-xl border border-ff-secondary/30 mb-8">
                    <h4 class="text-lg font-semibold text-ff-dark mb-4">Crear Nueva Pieza</h4>

                    <form method="POST" action="{{ route('spare_parts.store') }}" class="space-y-4">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            
                            <div>
                                <label class="form-label text-ff-dark">Nombre *</label>
                                <input type="text" name="name" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ff-secondary focus:ring-ff-secondary"
                                    value="{{ old('name') }}">
                            </div>

                            <div>
                                <label class="form-label text-ff-dark">Serial</label>
                                <input type="text" name="serial_number"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ff-secondary focus:ring-ff-secondary"
                                    value="{{ old('serial_number') }}">
                            </div>

                            {{-- Compañía --}}
                            <div>
                                <label class="form-label text-ff-dark">Compañía *</label>
                                <select name="company" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ff-secondary focus:ring-ff-secondary">
                                    <option value="" disabled selected>Seleccione una Compañía</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company['id'] }}" {{ old('company') == $company['id'] ? 'selected' : '' }}>
                                            {{ $company['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="form-label text-ff-dark">Stock Mínimo</label>
                                <input type="number" name="min_stock"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ff-secondary focus:ring-ff-secondary"
                                    value="{{ old('min_stock') }}">
                            </div>

                            <div>
                                <label class="form-label text-ff-dark">Stock Actual</label>
                                <input type="number" name="stock"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ff-secondary focus:ring-ff-secondary"
                                    value="{{ old('stock') }}">
                            </div>

                            <div>
                                <label class="form-label text-ff-dark">Estado *</label>
                                <select name="status" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ff-secondary focus:ring-ff-secondary">
                                    <option value="Activo">Activo</option>
                                    <option value="Inactivo">Inactivo</option>
                                    <option value="Pendiente">Pendiente</option>
                                </select>
                            </div>

                            <div class="md:col-span-3">
                                <label class="form-label text-ff-dark">Descripción</label>
                                <textarea name="description" rows="2"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ff-secondary focus:ring-ff-secondary">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <button type="submit"
                            class="bg-ff-success text-white px-5 py-2 rounded-lg font-semibold hover:bg-ff-secondary transition">
                            Guardar Pieza
                        </button>
                    </form>
                </div>

                {{-- Tabla --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-ff-bg-light text-ff-dark">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Acciones</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($spareParts as $part)
                                <tr>
                                    <td class="px-6 py-4 text-sm font-medium text-ff-dark">{{ $part['id'] }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $part['name'] }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $part['stock'] }} /
                                        {{ $part['min_stock'] }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                            @if (($part['stock'] ?? 0) <= ($part['min_stock'] ?? 0)) 
                                                bg-ff-error/20 text-ff-error
                                            @else 
                                                bg-ff-success/20 text-ff-success 
                                            @endif">
                                            {{ $part['status'] }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-sm font-medium">
                                        <a href="{{ route('spare_parts.details', $part['id']) }}"
                                           class="text-ff-secondary font-semibold hover:text-ff-primary transition">
                                            Detalles / Editar
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        No se encontraron piezas de recambio.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

            </div>
        </div>
    </div>

</x-app-layout>
