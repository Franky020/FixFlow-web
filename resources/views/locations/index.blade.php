<x-app-layout>
    <x-slot name="header">
        <div class="border-l-4 border-[#f5a000] pl-4">
            <h2 class="font-bold text-2xl text-[#006b66]">
                {{ __('Gestión de Localizaciones | Sucursales / Ubicaciones') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-10 bg-[#f5f5f5] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Mensajes de Sesión --}}
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-600 text-green-800 p-4 mb-6 rounded-md shadow">
                    ✅ {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-600 text-red-800 p-4 mb-6 rounded-md shadow">
                    ❌ {{ session('error') }}
                </div>
            @endif

            {{-- FORMULARIO DE CREACIÓN / EDICIÓN --}}
            <div class="bg-white shadow-xl rounded-xl p-8 border-t-8 border-[#f5a000] mb-12">
                <h2 class="text-2xl font-extrabold text-[#006b66] mb-6 border-b-2 border-gray-100 pb-3">
                    Crear Nueva Localización <span class="text-[#f5a000]">| Detalle de Sucursal</span>
                </h2>

                <form id="locationForm" method="POST" action="{{ route('locations.store') }}">
                    @csrf
                    <input type="hidden" id="formMethod" name="_method" value="POST">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Nombre --}}
                        <div>
                            <label class="block text-sm font-semibold text-[#006b66] mb-1">
                                Nombre de la Localización <span class="text-[#f5a000]">*</span>
                            </label>
                            <input type="text" id="name" name="name" required
                                   class="w-full p-3 border-2 border-gray-300 rounded-lg focus:border-[#006b66] focus:ring-[#006b66]"
                                   placeholder="Ej: Sucursal Norte">
                        </div>

                        {{-- Compañía --}}
                        <div>
                            <label class="block text-sm font-semibold text-[#006b66] mb-1">
                                ID de Compañía <span class="text-[#f5a000]">*</span>
                            </label>
                            <input type="number" id="company" name="company" required
                                   class="w-full p-3 border-2 border-gray-300 rounded-lg focus:border-[#006b66] focus:ring-[#006b66]"
                                   placeholder="Ej: 105">
                        </div>

                        {{-- Dirección --}}
                        <div class="md:col-span-3">
                            <label class="block text-sm font-semibold text-[#006b66] mb-1">
                                Dirección Completa <span class="text-[#f5a000]">*</span>
                            </label>
                            <textarea id="address" name="address" rows="3" required
                                      class="w-full p-3 border-2 border-gray-300 rounded-lg focus:border-[#006b66] focus:ring-[#006b66]"
                                      placeholder="Calle, número, colonia, ciudad..."></textarea>
                        </div>
                    </div>

                    {{-- Botones --}}
                    <div class="flex justify-end mt-6 gap-4">
                        <button type="button" id="cancelBtn"
                                class="bg-gray-400 text-white px-6 py-3 rounded-lg shadow hover:bg-gray-500 transition">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="bg-[#f5a000] text-white px-6 py-3 rounded-lg shadow hover:opacity-90 transition">
                            🗺️ Guardar Localización
                        </button>
                    </div>
                </form>
            </div>

            {{-- TABLA DE LOCALIZACIONES --}}
            <div class="bg-white shadow-xl rounded-xl p-8 border-t-8 border-[#006b66]">
                <h2 class="text-2xl font-extrabold text-[#006b66] mb-6 border-b-2 border-gray-100 pb-3">
                    Listado de Localizaciones Registradas <span class="text-[#f5a000]">| API</span>
                </h2>

                @if (!empty($locations))
                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="min-w-full text-sm text-left">
                            <thead class="bg-[#006b66] text-white uppercase text-xs">
                                <tr>
                                    <th class="px-6 py-3">ID</th>
                                    <th class="px-6 py-3">Nombre</th>
                                    <th class="px-6 py-3">Compañía</th>
                                    <th class="px-6 py-3">Dirección</th>
                                    <th class="px-6 py-3 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($locations as $loc)
                                    <tr class="hover:bg-[#f5a000]/10 transition">
                                        <td class="px-6 py-4">{{ $loc['id'] ?? '-' }}</td>
                                        <td class="px-6 py-4 font-semibold text-[#006b66]">{{ $loc['name'] ?? 'N/A' }}</td>
                                        <td class="px-6 py-4">#{{ $loc['company'] ?? 'N/A' }}</td>
                                        <td class="px-6 py-4">{{ $loc['address'] ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 flex justify-center gap-3">
                                            <button onclick="editLocation({{ $loc['id'] }}, '{{ $loc['name'] }}', '{{ $loc['address'] }}', '{{ $loc['company'] }}')"
                                                    class="text-[#006b66] hover:text-[#f5a000] font-semibold">
                                                ✏️ Editar
                                            </button>
                                            <form method="POST" action="{{ route('location.destroy', ['id' => $loc['id']]) }}"
                                                  onsubmit="return confirm('¿Eliminar {{ $loc['name'] }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 font-semibold">
                                                    🗑️ Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center border-2 border-dashed border-[#f5a000] rounded-xl bg-[#f5f5f5]/50">
                        <p class="text-lg text-[#006b66] font-semibold">
                            ✨ No hay localizaciones registradas.
                        </p>
                        <p class="text-gray-500">Agrega una nueva usando el formulario superior.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- JS para edición dinámica --}}
    <script>
        const form = document.getElementById('locationForm');
        const methodInput = document.getElementById('formMethod');
        const cancelBtn = document.getElementById('cancelBtn');

        function editLocation(id, name, address, company) {
            form.scrollIntoView({ behavior: 'smooth' });
            form.action = `/locations/${id}`;
            methodInput.value = 'PUT';
            document.getElementById('name').value = name;
            document.getElementById('address').value = address;
            document.getElementById('company').value = company;
        }

        cancelBtn.addEventListener('click', () => {
            form.reset();
            form.action = "{{ route('locations.store') }}";
            methodInput.value = 'POST';
        });
    </script>
</x-app-layout>
