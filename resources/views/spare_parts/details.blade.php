<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-ff-dark leading-tight border-l-4 border-ff-secondary pl-4">
            {{ __('Detalles y Edición de Pieza #') . ($sparePart['id'] ?? 'N/A') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-ff-bg-light">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Éxito/Error --}}
            @if (session('success'))
                <div class="bg-ff-success/10 border border-ff-success text-ff-success px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-ff-error/10 border border-ff-error text-ff-error px-4 py-3 rounded mb-4">
                    <p class="font-bold">Error en la operación:</p>
                    <ul class="mt-1 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- CARD PRINCIPAL --}}
            <div class="bg-ff-white shadow-lg sm:rounded-lg p-8 border-t-4 border-ff-secondary">
                
                <h3 class="text-2xl font-bold mb-6 text-ff-secondary border-b pb-3">
                    Información de Pieza <span class="text-ff-primary">#{{ $sparePart['id'] }}</span>
                </h3>

                {{-- FORMULARIO --}}
                <form method="POST" action="{{ route('spare_parts.update', $sparePart['id']) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    @php
                        $statusReverseMap = [
                            'active' => 'Activo', 
                            'inactive' => 'Inactivo', 
                            'pending' => 'Pendiente',
                            'disponible' => 'Disponible',
                            'agotado' => 'Agotado',
                            'obsoleto' => 'Obsoleto',
                        ];

                        $currentStatusAPI = $sparePart['status'] ?? 'active';
                        $currentStatusUI = $statusReverseMap[strtolower($currentStatusAPI)] ?? ucfirst($currentStatusAPI);
                        $selectedStatus = old('status', $currentStatusUI);
                        $currentCompanyId = old('company', $sparePart['company'] ?? '');
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                        {{-- Nombre --}}
                        <div>
                            <label class="block text-sm font-semibold text-ff-dark">Nombre *</label>
                            <input type="text" name="name" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                                       focus:ring-ff-secondary focus:border-ff-secondary"
                                value="{{ old('name', $sparePart['name']) }}">
                        </div>

                        {{-- Serial --}}
                        <div>
                            <label class="block text-sm font-semibold text-ff-dark">Serial</label>
                            <input type="text" name="serial_number" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                                       focus:ring-ff-secondary focus:border-ff-secondary"
                                value="{{ old('serial_number', $sparePart['serial_number']) }}">
                        </div>

                        {{-- Compañía --}}
                        <div>
                            <label class="block text-sm font-semibold text-ff-dark">Compañía *</label>
                            <select name="company" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                                       focus:ring-ff-secondary focus:border-ff-secondary">
                                <option value="" disabled>Seleccione una Compañía</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company['id'] }}" 
                                        {{ $currentCompanyId == $company['id'] ? 'selected' : '' }}>
                                        {{ $company['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Stock mínimo --}}
                        <div>
                            <label class="block text-sm font-semibold text-ff-dark">Stock Mínimo</label>
                            <input type="number" name="min_stock" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                                       focus:ring-ff-secondary focus:border-ff-secondary"
                                value="{{ old('min_stock', $sparePart['min_stock']) }}">
                        </div>

                        {{-- Stock actual --}}
                        <div>
                            <label class="block text-sm font-semibold text-ff-dark">Stock Actual</label>
                            <input type="number" name="stock"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                                       focus:ring-ff-secondary focus:border-ff-secondary"
                                value="{{ old('stock', $sparePart['stock']) }}">
                        </div>

                        {{-- Estado --}}
                        <div>
                            <label class="block text-sm font-semibold text-ff-dark">Estado *</label>
                            <select name="status"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                                       focus:ring-ff-secondary focus:border-ff-secondary">
                                <option value="Activo" {{ $selectedStatus == 'Activo' ? 'selected' : '' }}>Activo</option>
                                <option value="Inactivo" {{ $selectedStatus == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                                <option value="Pendiente" {{ $selectedStatus == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                            </select>
                        </div>

                        {{-- Descripción --}}
                        <div class="md:col-span-3">
                            <label class="block text-sm font-semibold text-ff-dark">Descripción</label>
                            <textarea name="description" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                                       focus:ring-ff-secondary focus:border-ff-secondary">{{ old('description', $sparePart['description']) }}</textarea>
                        </div>

                    </div>

                    {{-- BOTONES --}}
                    <div class="pt-6 flex justify-between border-t">

                        <button type="submit"
                            class="w-1/2 mr-2 bg-ff-primary text-white px-6 py-3 rounded-md font-bold text-lg 
                                   hover:bg-ff-primary/90 transition">
                            Guardar Cambios
                        </button>

                        <button type="button" onclick="showDeleteModal({{ $sparePart['id'] }})"
                            class="w-1/2 ml-2 bg-ff-error text-white px-6 py-3 rounded-md font-bold text-lg 
                                   hover:bg-ff-error/90 transition">
                            Eliminar Pieza
                        </button>
                    </div>

                </form>
            </div>

            {{-- LINK VOLVER --}}
            <div class="mt-8 text-center">
                <a href="{{ route('spare_parts.index') }}" 
                    class="text-ff-secondary hover:text-ff-dark font-bold underline transition">
                    ← Volver al Inventario
                </a>
            </div>

        </div>
    </div>

    {{-- MODAL FIXFLOW --}}
    <div id="deleteModal" class="fixed inset-0 bg-black/60 hidden z-50">
        <div class="flex items-center justify-center min-h-full p-4">
            <div class="bg-ff-white rounded-xl shadow-2xl w-full max-w-lg p-6 border-t-4 border-ff-error">
                
                <h3 class="text-xl font-bold text-ff-dark">Confirmar Eliminación</h3>
                <p class="mt-2 text-gray-600">
                    ¿Eliminar permanentemente la Pieza 
                    <span id="partIdDisplay" class="font-bold text-ff-error"></span>?
                </p>

                <div class="mt-6 flex justify-end space-x-3">

                    <form id="deleteForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="bg-ff-error text-white px-5 py-2 rounded-md hover:bg-ff-error/90">
                            Sí, eliminar
                        </button>
                    </form>

                    <button onclick="hideDeleteModal()"
                        class="bg-gray-200 text-gray-700 px-5 py-2 rounded-md hover:bg-gray-300">
                        Cancelar
                    </button>

                </div>

            </div>
        </div>
    </div>

    {{-- SCRIPT MODAL --}}
    <script>
        const deleteModal = document.getElementById('deleteModal');
        const deleteForm = document.getElementById('deleteForm');
        const partIdDisplay = document.getElementById('partIdDisplay');

        function showDeleteModal(id) {
            deleteForm.action = "{{ route('spare_parts.destroy', 'ID') }}".replace('ID', id);
            partIdDisplay.textContent = id;
            deleteModal.classList.remove('hidden');
        }

        function hideDeleteModal() {
            deleteModal.classList.add('hidden');
        }
    </script>

</x-app-layout>
