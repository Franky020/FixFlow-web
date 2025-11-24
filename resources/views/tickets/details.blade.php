<x-app-layout>
    <x-slot name="header">
        <div class="border-l-4 border-ff-primary pl-4">
            <h2 class="font-bold text-2xl text-ff-dark leading-tight">
                {{ __('Detalles y Edición de Ticket #') . ($ticket['id'] ?? 'N/A') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-ff-bg-light min-h-screen font-sans">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Mensajes de Éxito/Error --}}
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-ff-success text-ff-dark p-4 rounded-lg mb-8" role="alert">
                    <p class="font-bold">¡Éxito!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-ff-error text-ff-dark p-4 rounded-lg mb-8" role="alert">
                    <p class="font-bold">Error en la Operación</p>
                    <ul class="mt-1 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 🧡 Sección de Formulario de Edición/Actualización --}}
            <div class="bg-ff-white shadow-2xl rounded-xl p-6 lg:p-8 border-t-8 border-ff-primary mb-12">
                <h2 class="text-3xl font-extrabold text-ff-dark mb-6 border-b-2 border-gray-200 pb-3">
                    Información y Actualización <span class="text-ff-primary">| Ticket #{{ $ticket['id'] ?? 'N/A' }}</span>
                </h2>
                
                <form method="POST" action="{{ route('tickets.update', $ticket['id']) }}" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        
                        {{-- Fila 1: Título y Descripción --}}
                        <div class="md:col-span-3">
                            <label class="block text-sm font-semibold text-ff-dark mb-1">Título del Ticket:</label>
                            <input type="text" name="title" 
                                class="w-full p-3 rounded-lg border-2 border-gray-300 bg-ff-bg-light focus:outline-none focus:border-ff-primary transition-colors placeholder-gray-500" 
                                placeholder="Ej: Falla eléctrica en servidor." 
                                value="{{ old('title', $ticket['title'] ?? '') }}" />
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-sm font-semibold text-ff-dark mb-1">Descripción Detallada:</label>
                            <textarea name="description" rows="3"
                                class="w-full p-3 rounded-lg border-2 border-gray-300 bg-ff-bg-light focus:outline-none focus:border-ff-primary transition-colors placeholder-gray-500" 
                                placeholder="Detalle la falla, incluyendo síntomas y pasos para replicarla.">{{ old('description', $ticket['description'] ?? '') }}</textarea>
                        </div>
                        
                        {{-- Fila 2: Clasificación --}}
                        <div>
                            <label class="block text-sm font-semibold text-ff-dark mb-1">Categoría:</label>
                            <select name="category" 
                                class="w-full p-3 rounded-lg border-2 border-gray-300 bg-ff-bg-light focus:outline-none focus:border-ff-primary transition-colors">
                                @php $currentCategory = old('category', $ticket['category'] ?? ''); @endphp
                                <option value="" disabled {{ $currentCategory == '' ? 'selected' : '' }}>Selecciona Categoría</option>
                                <option value="Hardware" {{ $currentCategory == 'Hardware' ? 'selected' : '' }}>Hardware</option>
                                <option value="Software" {{ $currentCategory == 'Software' ? 'selected' : '' }}>Software</option>
                                <option value="Red" {{ $currentCategory == 'Red' ? 'selected' : '' }}>Red</option>
                                <option value="Electricidad" {{ $currentCategory == 'Electricidad' ? 'selected' : '' }}>Electricidad</option>
                                <option value="preventive" {{ $currentCategory == 'preventive' ? 'selected' : '' }}>Preventive</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-ff-dark mb-1">Prioridad:</label>
                            <select name="priority" 
                                class="w-full p-3 rounded-lg border-2 border-gray-300 bg-ff-bg-light focus:outline-none focus:border-ff-primary transition-colors">
                                @php $currentPriority = old('priority', $ticket['priority'] ?? ''); @endphp
                                <option value="" disabled {{ $currentPriority == '' ? 'selected' : '' }}>Selecciona Prioridad</option>
                                <option value="Baja" {{ $currentPriority == 'Baja' ? 'selected' : '' }}>Baja</option>
                                <option value="Media" {{ $currentPriority == 'Media' ? 'selected' : '' }}>Media</option>
                                <option value="Alta" {{ $currentPriority == 'Alta' ? 'selected' : '' }}>Alta</option>
                                <option value="Urgente" {{ $currentPriority == 'Urgente' ? 'selected' : '' }}>Urgente</option>
                                <option value="high" {{ $currentPriority == 'high' ? 'selected' : '' }}>high</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-ff-dark mb-1">Equipo Afectado:</label>
                            <input type="text" name="equipment" 
                                class="w-full p-3 rounded-lg border-2 border-gray-300 bg-ff-bg-light focus:outline-none focus:border-ff-primary transition-colors placeholder-gray-500" 
                                placeholder="Ej: Impresora XYZ o Servidor-01" 
                                value="{{ old('equipment', $ticket['equipment'] ?? '') }}" />
                        </div>

                        {{-- Fila 3: Tiempos y Duración (CORRECCIÓN CLAVE AQUÍ) --}}
                        <div>
                            <label class="block text-sm font-semibold text-ff-dark mb-1">Hora de Inicio (Estimada):</label>
                            @php
                                // Convierte el ISO 8601 de la API (con Z) al formato local compatible con datetime-local
                                $startTimeValue = isset($ticket['start_time']) ? \Carbon\Carbon::parse($ticket['start_time'])->tz(config('app.timezone'))->format('Y-m-d\TH:i:s') : '';
                            @endphp
                            <input type="datetime-local" name="start_time" 
                                class="w-full p-3 rounded-lg border-2 border-gray-300 bg-ff-bg-light focus:outline-none focus:border-ff-primary transition-colors" 
                                value="{{ old('start_time', $startTimeValue) }}" />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-ff-dark mb-1">Hora de Fin (Estimada):</label>
                            @php
                                // Convierte el ISO 8601 de la API (con Z) al formato local compatible con datetime-local
                                $endTimeValue = isset($ticket['end_time']) ? \Carbon\Carbon::parse($ticket['end_time'])->tz(config('app.timezone'))->format('Y-m-d\TH:i:s') : '';
                            @endphp
                            <input type="datetime-local" name="end_time" 
                                class="w-full p-3 rounded-lg border-2 border-gray-300 bg-ff-bg-light focus:outline-none focus:border-ff-primary transition-colors" 
                                value="{{ old('end_time', $endTimeValue) }}" />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-ff-dark mb-1">Duración (Ej: 2 04:30:00):</label>
                            {{-- Cambiado a tipo 'text' para manejar el formato de string especial "2 04:30:00" --}}
                            <input type="text" name="duration" 
                                class="w-full p-3 rounded-lg border-2 border-gray-300 bg-ff-bg-light focus:outline-none focus:border-ff-primary transition-colors placeholder-gray-500" 
                                placeholder="Ej: 2 04:30:00" 
                                value="{{ old('duration', $ticket['duration'] ?? '') }}" />
                        </div>
                        
                        {{-- Fila 4: Reporte, Estado y Asignación --}}
                        <div>
                            <label class="block text-sm font-semibold text-ff-dark mb-1">Reporte / Observación Final:</label>
                            <textarea name="report" rows="1" 
                                class="w-full p-3 rounded-lg border-2 border-gray-300 bg-ff-bg-light focus:outline-none focus:border-ff-primary transition-colors placeholder-gray-500" 
                                placeholder="Solo si el ticket está cerrado">{{ old('report', $ticket['report'] ?? '') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-ff-dark mb-1">Estado:</label>
                            <select name="status" 
                                class="w-full p-3 rounded-lg border-2 border-gray-300 bg-ff-bg-light focus:outline-none focus:border-ff-primary transition-colors">
                                @php $currentStatus = old('status', $ticket['status'] ?? 'Pendiente'); @endphp
                                <option value="Pendiente" {{ $currentStatus == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="En Progreso" {{ $currentStatus == 'En Progreso' ? 'selected' : '' }}>En Progreso</option>
                                <option value="Cerrado" {{ $currentStatus == 'Cerrado' ? 'selected' : '' }}>Cerrado</option>
                                <option value="abierto" {{ $currentStatus == 'abierto' ? 'selected' : '' }}>abierto</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-ff-dark mb-1">Compañía (ID), Usuario (ID), Ubicación:</label>
                            <div class="flex space-x-2">
                                <input type="number" name="company" class="w-1/3 p-3 rounded-lg border-2 border-gray-300 bg-ff-bg-light focus:border-ff-primary transition-colors" placeholder="Cía ID" value="{{ old('company', $ticket['company'] ?? '') }}" />
                                <input type="number" name="user" class="w-1/3 p-3 rounded-lg border-2 border-gray-300 bg-ff-bg-light focus:border-ff-primary transition-colors" placeholder="User ID" value="{{ old('user', $ticket['user'] ?? '') }}" />
                                
                                {{-- Ubicación ahora es opcional y sin validación HTML --}}
                                <input type="text" name="location" class="w-1/3 p-3 rounded-lg border-2 border-gray-300 bg-ff-bg-light focus:border-ff-primary transition-colors" placeholder="Ubicación" value="{{ old('location', $ticket['location'] ?? '') }}" />
                            </div>
                        </div>

                    </div>

                    {{-- Botones de Acción --}}
                    <div class="pt-4 flex justify-between items-center">
                        <button type="submit"
                            class="w-1/2 mr-2 bg-ff-primary text-ff-white px-6 py-3 rounded-xl font-bold text-lg shadow-lg hover:shadow-xl hover:bg-opacity-95 transition-all transform hover:scale-[1.005] flex items-center justify-center">
                            <i class="fas fa-save mr-2"></i> Guardar Cambios
                        </button>
                        
                        {{-- Botón de Eliminar que llama al JS --}}
                        <button type="button" onclick="showDeleteModal({{ $ticket['id'] }})"
                            class="w-1/2 ml-2 bg-ff-error text-ff-white px-6 py-3 rounded-xl font-bold text-lg shadow-lg hover:shadow-xl hover:bg-opacity-95 transition-all transform hover:scale-[1.005] flex items-center justify-center">
                            <i class="fas fa-trash-alt mr-2"></i> Eliminar Ticket
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="mt-8 text-center">
                <a href="{{ route('tickets.index') }}" class="text-ff-secondary hover:text-ff-dark transition duration-150 font-medium underline">
                    ← Volver al Listado de Tickets
                </a>
            </div>
        </div>
    </div>
    
    {{-- MODAL DE CONFIRMACIÓN DE ELIMINACIÓN (Se mantiene el script de JS igual) --}}
    {{-- ... (El modal y el script de JavaScript se mantienen sin cambios) ... --}}
    <div id="deleteModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 hidden z-50 transition-opacity duration-300 ease-out" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-full p-4">
            <div class="bg-ff-white rounded-xl shadow-2xl w-full max-w-lg p-6 transform transition-all sm:my-8 sm:align-middle">
                
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-ff-error" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.38 18c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-bold text-ff-dark" id="modal-title">
                            Confirmar Eliminación de Ticket
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                ¿Estás seguro de que deseas eliminar el Ticket #<span id="ticketIdDisplay" class="font-bold text-ff-dark"></span>? Esta acción no se puede deshacer.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <form id="deleteForm" method="POST" action="" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-ff-error text-base font-medium text-ff-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition duration-150">
                            Sí, Eliminar
                        </button>
                    </form>
                    
                    <button type="button" onclick="hideDeleteModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm transition duration-150">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Obtener referencias del modal y el formulario
        const deleteModal = document.getElementById('deleteModal');
        const deleteForm = document.getElementById('deleteForm');
        const ticketIdDisplay = document.getElementById('ticketIdDisplay');

        /**
         * Muestra el modal de confirmación de eliminación y configura el formulario.
         * @param {number} ticketId El ID del ticket a eliminar.
         */
        function showDeleteModal(ticketId) {
            // Se usa la misma lógica que en index.blade.php
            const laravelDestroyRoute = "{{ route('tickets.destroy', ['ticket' => 'TEMP_ID']) }}";
            deleteForm.action = laravelDestroyRoute.replace('TEMP_ID', ticketId);

            // 2. Actualizar el ID en el modal para el usuario
            ticketIdDisplay.textContent = ticketId;

            // 3. Mostrar el modal
            deleteModal.classList.remove('hidden');
        }

        /**
         * Oculta el modal de confirmación de eliminación.
         */
        function hideDeleteModal() {
            deleteModal.classList.add('hidden');
        }

        // También ocultar si el usuario hace clic fuera del modal
        deleteModal.addEventListener('click', (e) => {
            if (e.target === deleteModal) {
                hideDeleteModal();
            }
        });

        // Cargar el script de Font Awesome si no está cargado (para los iconos)
        if (typeof FontAwesome === 'undefined') {
            const faScript = document.createElement('script');
            faScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/js/all.min.js';
            faScript.crossOrigin = 'anonymous';
            document.head.appendChild(faScript);
        }
    </script>
</x-app-layout>